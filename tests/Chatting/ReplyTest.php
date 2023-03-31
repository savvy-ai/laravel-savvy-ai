<?php

use SavvyAI\Features\Chatting\ChatReply;

beforeEach(function () {
    $client = mockClient('POST', 'chat/completions', [
        'model' => 'gpt-3.5-turbo',
        'messages' => ['role' => 'user', 'content' => 'Hello!'],
    ], chatCompletion());

    $this->result = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => ['role' => 'user', 'content' => 'Hello!'],
    ]);
});

it('has a role and content', function () {
    $reply = ChatReply::fromAIServiceResponse((array) $this->result);

    expect($reply->role())->toBeString()
        ->and($reply->content())->toBeString();
});
