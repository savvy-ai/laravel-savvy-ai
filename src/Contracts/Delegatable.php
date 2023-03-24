<?php

namespace SavvyAI\Contracts;

use Exception;
use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;

interface Delegatable
{
    /**
     * Returns a list of delegates that can be used during classification
     *
     * @return array
     */
    public function delegates(): array;

    /**
     * @param Chat $chat
     * @param Message $incomingMessage
     * @param Exception|null $previouslyThrowException
     *
     * @return Message
     */
    public function delegate(Chat $chat, Message $incomingMessage, Exception $previouslyThrowException = null): Message;
}
