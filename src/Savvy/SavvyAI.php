<?php

namespace SavvyAI\Savvy;

use SavvyAI\Events\TrainingEvent;
use SavvyAI\Models\Statement;
use SavvyAI\Models\User;
use SavvyAI\Savvy\Base\Estimate;
use SavvyAI\Savvy\Base\Reply;
use SavvyAI\Savvy\Config\PromptConfig;
use SavvyAI\Savvy\Config\TrainingConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class SavvyAI
{
    const DEFAULT_TOKENS_PER_CREDIT   = 500;
    const DEFAULT_RESPONSE_MULTIPLIER = 1.5;

    private Tokenizer  $tokenizer;
    private Segmenter  $segmenter;
    private Summarizer $summarizer;
    private Vectorizer $vectorizer;

    /**
     * @param Tokenizer $tokenizer
     * @param nullSegmenter $segmenter
     * @param nullSummarizer $summarizer
     * @param nullVectorizer $vectorizer
     */
    public function __construct(Tokenizer $tokenizer = null, Segmenter $segmenter = null, Summarizer $summarizer = null, Vectorizer $vectorizer = null)
    {
        $this->tokenizer  = $tokenizer ?? new Tokenizer();
        $this->segmenter  = $segmenter ?? new Segmenter();
        $this->summarizer = $summarizer ?? new Summarizer();
        $this->vectorizer = $vectorizer ?? new Vectorizer();
    }

    /**
     * @param string $text
     * @param TrainingConfig $config
     */
    public function train(string $text, TrainingConfig $config): void
    {
        DB::beginTransaction();

        try
        {
            event(new TrainingEvent($config->property->id, TrainingConfig::TOKENIZING));
            $tokens = $this->tokenizer->tokenize($text);

            event(new TrainingEvent($config->property->id, TrainingConfig::SEGMENTING));
            $segments = $this->segmenter->segment($text, ceil(count($tokens) / $config->maxSegmentTokens));

            event(new TrainingEvent($config->property->id, TrainingConfig::SUMMARIZING));
            $statements  = [];
            $totalTokens = 0;

            foreach ($segments as $segment)
            {
                $reply = $this->summarizer->summarize($segment, $config->maxSummaryTokens);

                $sentences = explode(PHP_EOL, $reply->text);

                foreach ($sentences as $sentence)
                {
                    $sentence = trim($sentence, ' -');

                    $statement = new Statement([
                        'user_id'     => $config->user->id,
                        'property_id' => $config->property->id,
                        'statement'   => $sentence,
                        'category'    => 'training',
                    ]);

                    $key = md5($sentence);

                    // Prevent duplicates within the same segment
                    // as well as duplicates across segments and summaries
                    if (array_key_exists($key, $statements))
                    {
                        continue;
                    }

                    $statement->save();

                    $statements[$key] = $statement;
                }

                $totalTokens += $reply->tokens;
            }

            event(new TrainingEvent($config->property->id, TrainingConfig::VECTORIZING));
            $this->vectorizer->vectorize(array_values($statements), $config->namespace, $config->metadata);

            $config->user->subtractCredits($config->user->tokensToCredits($totalTokens))->save();

            event(new TrainingEvent($config->property->id, TrainingConfig::COMPLETED));

            DB::commit();
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
            DB::rollBack();

            throw $e;
        }
    }

    public function tune(Statement $statement, TrainingConfig $config): void
    {
        $this->vectorizer->vectorize([$statement], $config->namespace, $config->metadata);
    }

    public static function prompt(string $text, PromptConfig $config): Reply
    {
        $embeddings = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $text,
        ]);

        $matches = Http::pinecone()->post('/query', [
            'vector'    => $embeddings->embeddings[0]->embedding,
            'namespace' => $config->namespace,
            'topK'      => $config->maxResults,
            'filter'    => ['property_id' => $config->property->id]
        ])->json('matches');

        $statementIds = collect($matches)->pluck('id')->toArray();
        $statements   = Statement::whereIn('id', $statementIds)->get()->toArray();

        $prompt = view('prompts.prompt', [
            'statements' => $statements,
            'agents'     => $config->agents,
            'text'       => $text
        ])->render();

        $prompt = trim($prompt);

        $result = OpenAI::completions()->create([
            'model'       => 'text-davinci-003',
            'temperature' => 0.1,
            'max_tokens'  => $config->maxTokens,
            'prompt'      => $prompt,
        ]);

        $reply = new Reply($result, $statements);

        $config->user->subtractCredits($config->user->tokensToCredits($reply->tokens))->save();

        Log::info('Prompt handled', [
            'tokens'     => $result->usage->totalTokens,
            'text'       => $text,
            'reply'      => $reply->text,
            'statements' => $statements,
        ]);

        return $reply;
    }

    public function estimate(string $text, User $user): Estimate
    {
        $tokens  = (new Tokenizer())->count($text) * self::DEFAULT_RESPONSE_MULTIPLIER;
        $credits = $user->tokensToCredits($tokens);

        return new Estimate($text, $tokens, $credits);
    }
}
