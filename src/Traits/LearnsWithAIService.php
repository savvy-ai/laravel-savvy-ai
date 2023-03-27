<?php

namespace SavvyAI\Traits;

use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\Reply;
use Vanderlee\Sentence\Sentence;
use OpenAI\Responses\Embeddings\CreateResponse;

/**
 * Makes calls to the OpenAI API to vectorize and summarize text for training purposes
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Traits
 */
trait LearnsWithAIService
{
    protected string $model           = 'gpt-3.5-turbo';
    protected int $maxTokens          = 1000;
    protected float $temperature      = 0.5;
    protected float $frequencyPenalty = 0.5;
    protected float $presencePenalty  = 0.1;
    protected ?string $stop           = null;

    protected string $summarizingPrompt = <<<'EOT'
Extract the most important phrases from the following text without losing any context and summarize them into multiple summaries.
EOT;

    protected string $vectorizingPrompt = <<<'EOT'
Carefully analyze the following conversation to determine whether or not it is on topic.

- The topic of the conversation is: "{!! $topic !!}"
- If the conversation is on topic, you MUST say "@OnTopic()"
- If the conversation is off topic, you MUST say "@OffTopic()"
EOT;

    /**
     * @param string $text
     * @param int $minLength
     * @param int $maxLength
     *
     * @return array
     */
    public function summarizeForTraining(string $text, int $minLength = 16, int $maxLength = 256): array
    {
        $sentences = (new Sentence())->split($text, Sentence::SPLIT_TRIM);

        $mergedSentences = [];
        $lastSentences   = [];

        while(!empty($sentences))
        {
            $sentence = $currentSentence = array_shift($sentences);

            if (!empty($lastSentences))
            {
                $sentence = implode(' ', $lastSentences) . ' ' . $sentence;
            }

            if (mb_strlen($sentence) < $minLength)
            {
                $lastSentences[] = $currentSentence;

                continue;
            }

            if (mb_strlen($sentence) > $maxLength)
            {
                array_unshift($sentences, $currentSentence);

                $sentence = implode(' ', $lastSentences);
            }

            $lastSentences     = [];
            $mergedSentences[] = $sentence;
        }

        return $mergedSentences;
    }

    public function vectorizeForStorage(array $sentences): array
    {
        $response = openai()->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $sentences,
        ]);

        $vectors = [];

        foreach ($response->embeddings as $embedding)
        {
            $vectors[] = [
                'id'       => $embedding->index,
                'values'   => $embedding->embedding,
                'sentence' => $sentences[$embedding->index],
            ];
        }

        return $vectors;
    }
}
