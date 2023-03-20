<?php

namespace SavvyAI\Savvy;

use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;
use SavvyAI\Models\Property;

class Savvy
{
    /**
     * Delegates the given message to the appropriate chatbot to handle it
     *
     * @param Property $property
     * @param Chat $chat
     * @param Message $message
     *
     * @return Message
     */
    public function chat(Property $property, Chat $chat, Message $message): Message
    {
        $property->load(['user', 'chatbot.agents.dialogues']);

        $message = $property->chatbot->delegate($chat, $message);

        // $property->user->subtractCredits($property->user->tokensToCredits('{totalTokens}'))->save();

        return $message;
    }
}
