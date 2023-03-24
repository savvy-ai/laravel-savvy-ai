<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Blade;
use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\Message;
use SavvyAI\Features\Chatting\Reply;

/**
 * Makes calls to the OpenAI API to classify text, validate replies, and to chat
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Traits
 */
trait InteractsWithOpenAI
{
    protected $model            = 'gpt-3.5-turbo';
    protected $maxTokens        = 32;
    protected $temperature      = 0.0;
    protected $frequencyPenalty = 0.0;
    protected $presencePenalty  = 0.0;
    protected $stop             = ' ';

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
     * @param strings[] $subjects List of subjects to classify
     *
     * @throws UnknownContextException
     *
     * @return Reply
     */
    public function classify(string $text, array $subjects = [], string $expectedStringInReply = null): Reply
    {
        $prompt = Blade::render($this->classificationPrompt, compact('subjects'));

        $result = openai()->chat()->create([
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

        $reply = new Reply($result);

        if ($reply->isContextUnknown($expectedStringInReply))
        {
            throw new UnknownContextException($reply->content());
        }

        return $reply;
    }

    /**
     * @param string $topic
     *
     * @throws UnknownContextException
     *
     * @return Reply
     */
    public function validate(string $text, string $topic): Reply
    {
        $result = openai()->chat()->create([
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

        $reply = new Reply($result);

        if ($reply->isContextUnknown())
        {
            throw new UnknownContextException($reply->content());
        }

        return $reply;
    }

    /**
     * @param Message[]
     */
    public function chat(array $messages = []): Reply
    {
        $result = openai()->chat()->create([
            'model'             => $this->model,
            'max_tokens'        => $this->maxTokens,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty,
            'stop'              => $this->stop ?? null,
            'messages' => $messages,
        ]);

        $reply = new Reply($result);

        if ($reply->isContextUnknown())
        {
            throw new UnknownContextException($reply->content());
        }

        return $reply;
    }
}
