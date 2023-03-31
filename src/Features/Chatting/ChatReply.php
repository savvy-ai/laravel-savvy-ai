<?php

namespace SavvyAI\Features\Chatting;

use Illuminate\Support\Str;
use SavvyAI\Contracts\ChatReplyContract;

/**
 * Represents a response from the completions API
 *
 * Class ChatReply
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Chat
 */
class ChatReply implements ChatReplyContract
{
    protected array $usage;
    protected array $message;

    /**
     * @param array $response Result from the completions API request
     */
    public static function fromAIServiceResponse(array $response): ChatReplyContract
    {
        $instance = new static();

        $instance->usage   = $response['usage']->toArray() ?? [];
        $instance->message = $response['choices'][0]->message->toArray() ?? [];

        return $instance;
    }

    public function role(): string
    {
        return $this->message['role'] ?? '';
    }

    public function content(): string
    {
        return $this->message['content'] ?? '';
    }

    public function extractDelegateName(): string
    {
        preg_match('/@([a-zA-Z]+)\(([^)]+)?\)/', $this->content(), $matches);

        return $matches[1] ?? '';
    }

    public function isOnTopic(): bool
    {
        return mb_stripos($this->content(), '@OnTopic') !== false;
    }

    public function isContextUnknown(string $expected = null): bool
    {
        if (Str::contains($this->content(), '@Unknown', true))
        {
            return true;
        }

        if (!empty($expected) && !Str::contains($this->content(), '@', true))
        {
            $this->message['content'] = '*@Unknown()';

            return true;
        }

        return false;
    }

    public function totalTokensUsed(): int
    {
        return $this->usage['total_tokens'] ?? 0;
    }

    public function promptTokensUsed(): int
    {
        return $this->usage['prompt_tokens'] ?? 0;
    }

    public function completionTokensUsed(): int
    {
        return $this->usage['completion_tokens'] ?? 0;
    }
}
