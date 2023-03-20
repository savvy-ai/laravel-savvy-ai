<?php

namespace SavvyAI\Savvy\Chat\Tools;

use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;
use SavvyAI\Models\Property;
use SavvyAI\Savvy\Chat\Role;

class GetCurrentTime
{
    public function use(Chat $chat, Message $incomingMessage): Message
    {
        $property = $chat->property;

        $now = now($property->timezone ?? 'UTC');

        return new Message([
            'role'    => Role::Assistant,
            'content' => sprintf('The current time in %s, %s is %s', $property->city, $property->state, $now->format('g:i A T'))
        ]);
    }
}
