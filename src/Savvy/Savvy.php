<?php

namespace SavvyAI\Savvy;

use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;
use SavvyAI\Models\Property;
use SavvyAI\Models\Trainable;

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
    public function chat(Trainable $trainable, Chat $chat, Message $message): Message
    {
        $trainable->load(['user', 'chatbot.agents.dialogues']);

        $message = $trainable->chatbot->delegate($chat, $message);

        // $trainable->user->subtractCredits($trainable->user->tokensToCredits('{totalTokens}'))->save();

        return $message;
    }
}
