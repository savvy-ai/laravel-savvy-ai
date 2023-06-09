<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Blade;
use SavvyAI\Contracts\ChatMessageContract;
use SavvyAI\Contracts\ChatReplyContract;
use SavvyAI\Exceptions\OffTopicException;
use SavvyAI\Exceptions\DelegateNotFoundException;
use SavvyAI\Features\Chatting\ChatReply;
use SavvyAI\Features\Chatting\Role;

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
    public ?string $stop           = null;

    protected string $classificationPrompt = <<<'EOT'
Strictly follow the {!! count($subjects) + 1 !!} rules provided below to classify the text.
@foreach($subjects as $subject)
- {!! $subject !!}
@endforeach
- If you cannot CONFIDENTLY classify the text, you MUST say "@Unknown()"
Keep these rules in mind when classifying the text, and do not deviate from them.
EOT;

    protected string $topicValidationPrompt = <<<'EOT'
Classify whether or not the given conversation is on topic.
Provide a label of @OnTopic() or @OffTopic(), where @OnTopic() means completely on-topic and @OffTopic() means off-topic.

Note that the topic is "{!! $topic !!}".
EOT;

    /**
     * Classifies text into the correct delegate or unknown, based on the given subjects
     *
     * @param string $text Text to classify
     * @param string[] $subjects List of subjects to classify
     * @param string|null $expectedStringInReply
     *
     * @return ChatReplyContract
     *
     * @throws DelegateNotFoundException
     */
    public function classify(string $text, array $subjects = [], string $expectedStringInReply = null): ChatReplyContract
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
                    'content' => Blade::render($this->classificationPrompt, compact('subjects')),
                ],
                [
                    'role' => 'user',
                    'content' => $text,
                ]
            ],
        ]);

        $reply = ChatReply::fromAIServiceResponse((array) $result);

        if ($reply->isContextUnknown($expectedStringInReply))
        {
            throw new DelegateNotFoundException();
        }

        return $reply;
    }

    /**
     * @param string $text
     * @param string $topic
     *
     * @return ChatReplyContract
     *
     * @throws DelegateNotFoundException|OffTopicException
     */
    public function validate(string $text, string $topic): ChatReplyContract
    {
        return $this->validateWithMessages([
            'role' => Role::User->value,
            'content' => $text
        ], $topic);
    }

    /**
     * @param array<int, array<string, string>> $messages
     * @param string $topic
     *
     * @return ChatReplyContract
     *
     * @throws DelegateNotFoundException|OffTopicException
     */
    public function validateWithMessages(array $messages, string $topic): ChatReplyContract
    {
        $result = ai()->chat()->create([
            'model'             => $this->model,
            'max_tokens'        => $this->maxTokens,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty,
            'stop'              => $this->stop ?? null,
            'messages' => array_merge([
                [
                    'role' => 'system',
                    'content' => Blade::render($this->topicValidationPrompt, compact('topic')),
                ],
            ], $messages),
        ]);

        $reply = ChatReply::fromAIServiceResponse((array) $result);

       if ($reply->isContextUnknown())
       {
           throw new DelegateNotFoundException();
       }

       if (!$reply->isOnTopic())
       {
           throw new OffTopicException();
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
     * @param ChatMessageContract[] $messages
     *
     * @return ChatReplyContract
     *
     * @throws DelegateNotFoundException
     */
    public function chat(array $messages = []): ChatReplyContract
    {
        $messages = collect($messages)
            ->map(fn (ChatMessageContract $message) => $message->asArray())
            ->toArray();

        $response = ai()->chat()->create([
            'model'             => $this->model,
            'max_tokens'        => $this->maxTokens,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty,
            'stop'              => $this->stop ?? null,
            'messages'          => $messages,
        ]);

        $reply = ChatReply::fromAIServiceResponse((array) $response);

        if ($reply->isContextUnknown())
        {
            throw new DelegateNotFoundException();
        }

        return $reply;
    }
}
