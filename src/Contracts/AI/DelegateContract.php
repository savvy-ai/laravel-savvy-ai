<?php

namespace SavvyAI\Contracts\AI;

use Exception;
use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;

interface DelegateContract
{
    /**
     * Returns a list of delegates/strings that can be used during classification
     *
     * @return array<int, string>
     */
    public function delegates(): array;

    /**
     * @param Chat $chat
     * @param Message $incomingMessage
     * @param Exception|null $previouslyThrownException
     *
     * @return Message
     */
    public function delegate(Chat $chat, Message $incomingMessage, Exception $previouslyThrownException = null): Message;
}
