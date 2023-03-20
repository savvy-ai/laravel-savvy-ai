<?php

namespace SavvyAI\Traits;

use SavvyAI\Exceptions\UnknownContextException;
use SavvyAI\Savvy\Chat\Message;
use SavvyAI\Savvy\Chat\Reply;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Allows calls to the OpenAI API
 *
 * @author Selvin Ortiz <selvin@savvyhost.ai>
 * @package SavvyAI\Savvy\Chat\Traits
 */
trait InteractsWithOpenAI
{
    /**
     * @param Message[]
     */
    public function call(array $messages = []): Reply
    {
        $result = OpenAI::chat()->create([
            'model'             => $this->model,
            'max_tokens'        => $this->max_tokens,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presence_penalty,
            'frequency_penalty' => $this->frequency_penalty,
            'stop'              => $this->stop ?? null,
            'messages' => $messages,
        ]);

        $reply = new Reply($result);

        if ($reply->unknown())
        {
            throw new UnknownContextException($reply->content());
        }

        return $reply;
    }
}
