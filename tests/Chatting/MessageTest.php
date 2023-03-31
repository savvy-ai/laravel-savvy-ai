<?php

use SavvyAI\Features\Chatting\ChatMessage;
use SavvyAI\Features\Chatting\Role;

it('has role and content', function () {
    $message = new ChatMessage(Role::User, 'Hello');

    expect($message->role()->value)->toBe('user')
        ->and($message->content())->toBe('Hello');
});
