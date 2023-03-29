<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Blade;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Contracts\AI\ReplyContract;
use SavvyAI\Reply;

/**
 * Makes calls to the OpenAI API to classify text, validate replies, and to chat
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Traits
 */
trait InteractsWithAIService
{
    public string $model           = 'gpt-3.5-turbo';
    public int $maxTokens          = 32;
    public float $temperature      = 0.0;
    public float $frequencyPenalty = 0.0;
    public float $presencePenalty  = 0.0;
    public ?string $stop           = ' ';

    protected string $classificationPrompt = <<<'EOT'
Carefully classify the text to find the correct delegate.

You MUST classify the text according to the following rules:
@foreach($subjects as $subject)
- {!! $subject !!}
@endforeach
- If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"
EOT;

    protected string $topicValidationPrompt = <<<'EOT'
Carefully analyze the following conversation to determine whether or not it is on topic.

- The topic of the conversation is: "{!! $topic !!}"
- If the conversation is on topic, you MUST say "@OnTopic()"
- If the conversation is off topic, you MUST say "@OffTopic()"
EOT;

    /**
     * Classifies text into the correct delegate or unknown, based on the given subjects
     *
     * @param string $text Text to classify
     * @param string[] $subjects List of subjects to classify
     * @param string|null $expectedStringInReply
     *
     * @return ReplyContract
     *
     * @throws UnknownContextException
     */
    public function classify(string $text, array $subjects = [], string $expectedStringInReply = null): ReplyContract
    {
        $prompt = Blade::render($this->classificationPrompt, compact('subjects'));

        $result = ai()->chat()->create([
            'model'             => $this->model,
            'max_tokens'        => $this->maxTokens,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty,
            'stop'              => $this->stop ?? null,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $prompt,
                ],
                [
                    'role' => 'user',
                    'content' => $text,
                ]
            ],
        ]);

        $reply = Reply::fromClientResponse((array) $result);

        if ($reply->isContextUnknown($expectedStringInReply))
        {
            throw new UnknownContextException($reply->content());
        }

        return $reply;
    }

    /**
     * @param string $text
     * @param string $topic
     *
     * @return ReplyContract
     *
     * @throws UnknownContextException
     */
    public function validate(string $text, string $topic): ReplyContract
    {
        $result = ai()->chat()->create([
            'model'             => $this->model,
            'max_tokens'        => $this->maxTokens,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty,
            'stop'              => $this->stop ?? null,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => Blade::render($this->topicValidationPrompt, compact('topic')),
                ],
                [
                    'role' => 'user',
                    'content' => $text,
                ]
            ],
        ]);

        $reply = Reply::fromClientResponse((array) $result);

        if ($reply->isContextUnknown())
        {
            throw new UnknownContextException($reply->content());
        }

        return $reply;
    }

    /**
     * @param string $text
     *
     * @return array<int, float>
     */
    public function vectorize(string $text): array
    {
        $response = ai()->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $text,
        ]);

        return $response->embeddings[0]->embedding;
    }

    /**
     * @param array $messages
     *
     * @return ReplyContract
     *
     * @throws UnknownContextException
     */
    public function chat(array $messages = []): ReplyContract
    {
        $result = ai()->chat()->create([
            'model'             => $this->model,
            'max_tokens'        => $this->maxTokens,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty,
            'stop'              => $this->stop ?? null,
            'messages' => $messages,
        ]);

        $reply = Reply::fromClientResponse((array) $result);

        if ($reply->isContextUnknown())
        {
            throw new UnknownContextException($reply->content());
        }

        return $reply;
    }
}
