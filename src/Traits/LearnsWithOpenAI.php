<?php

namespace SavvyAI\Traits;

use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\Reply;

/**
 * Makes calls to the OpenAI API to vectorize and summarize text for training purposes
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Traits
 */
trait LearnsWithOpenAI
{
    protected $model            = 'gpt-3.5-turbo';
    protected $maxTokens        = 1000;
    protected $temperature      = 0.5;
    protected $frequencyPenalty = 0.5;
    protected $presencePenalty  = 0.1;
    protected $stop             = null;

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
     * @throws UnknownContextException
     *
     * @return Reply
     */
    public function summarize(string $text): Reply
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
                    'content' => $this->summarizingPrompt,
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
}
