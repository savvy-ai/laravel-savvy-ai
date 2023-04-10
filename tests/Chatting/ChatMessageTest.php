<?php

use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;
use SavvyAI\Features\Chatting\ChatReply;

it('can be instantiated', function () {
    $message = new ChatMessage(Role::User, 'Hello');

    expect($message->role()->value)->toBe('user')
        ->and($message->content())->toBe('Hello');
});

it('can be created from reply', function () {
    $usage = [
        'prompt_tokens' => 100,
        'completion_tokens' => 100,
        'total_tokens' => 200,
    ];

    $message = [
        'role' => 'user',
        'content' => 'Can I check in early?',
    ];

    $message = ChatMessage::fromChatReply(new ChatReply($usage, $message));

    expect($message->role()->value)->toBe('user')
        ->and($message->content())->toBe('Can I check in early?');
});
