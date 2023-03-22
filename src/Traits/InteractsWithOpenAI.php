<?php

namespace SavvyAI\Traits;

use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Features\Chatting\Message;
use SavvyAI\Features\Chatting\Reply;

/**
 * Allows calls to the OpenAI API
 *
 * @author Selvin Ortiz <selvin@savvyhost.ai>
 * @package SavvyAI\Traits
 */
trait InteractsWithOpenAI
{
    /**
     * @param Message[]
     */
    public function call(array $messages = []): Reply
    {
        $result = openai()->chat()->create([
            'model'             => $this->model,
            'max_tokens'        => $this->max_tokens,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presence_penalty,
            'frequency_penalty' => $this->frequency_penalty,
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
