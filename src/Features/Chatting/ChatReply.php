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
    protected array $media;

    /**
     * @param array $response Result from the completions API request
     */
    public static function fromAIServiceResponse(array $response): ChatReplyContract
    {
        return new static(
            $response['usage']->toArray() ?? [],
            $response['choices'][0]->message->toArray() ?? []
        );
    }

    public function __construct(array $usage, array $message, ?array $media = null)
    {
        $this->usage = $usage;
        $this->message = $message;
        $this->media = $media ?? [];
    }

    public function role(?string $role = null): string
    {
        if (!empty($role))
        {
            $this->message['role'] = $role;

            return $role;
        }

        return $this->message['role'] ?? '';
    }

    public function content(?string $content = null): string
    {
        if (!empty($content))
        {
            $this->message['content'] = $content;

            return $content;
        }

        return $this->message['content'] ?? '';
    }

    public function media(?array $media = null): array
    {
        if (!empty($media))
        {
            $this->media = $media;

            return $media;
        }

        return $this->media;
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
