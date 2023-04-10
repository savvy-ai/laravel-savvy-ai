<?php

use OpenAI\Responses\Chat\CreateResponseChoice;
use OpenAI\Responses\Chat\CreateResponseUsage;
use SavvyAI\Features\Chatting\ChatReply;

it('can be created from a response with usage and message content', function () {
    $result = [
        'usage' => CreateResponseUsage::from([
            'prompt_tokens' => 100,
            'completion_tokens' => 100,
            'total_tokens' => 200,
        ]),
        'choices' => [
            CreateResponseChoice::from([
                'index' => 0,
                'message' => [
                    'role' => 'user',
                    'content' => 'Can I check in early?',
                ],
                'finish_reason' => null,
            ]),
        ],
    ];

    $reply = ChatReply::fromAIServiceResponse($result);

    expect($reply->promptTokensUsed())->toBe(100)
        ->and($reply->completionTokensUsed())->toBe(100)
        ->and($reply->totalTokensUsed())->toBe(200)
        ->and($reply->role())->toBe('user')
        ->and($reply->content())->toBe('Can I check in early?');
});
