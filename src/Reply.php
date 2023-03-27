<?php

namespace SavvyAI;

class Reply implements Contracts\AI\ReplyContract
{
    protected array $usage;
    protected array $message;

    public static function fromClientResponse(array $response): Contracts\AI\ReplyContract
    {
        $reply = new self();

        $reply->usage   = $result['usage'] ?? [];
        $reply->message = $result['choices'][0]['message'] ?? [];

        return $reply;
    }

    public function role(): string
    {
        return $this->message['role'] ?? '';
    }

    public function content(): string
    {
        return $this->message['content'] ?? '';
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

    public function isOnTopic(): bool
    {
        // TODO: Implement isOnTopic() method.
    }

    public function isContextUnknown(string $expected = null): bool
    {
        // TODO: Implement isContextUnknown() method.
    }

    public function agent(): ?\SavvyAI\Models\Agent
    {
        // TODO: Implement agent() method.
    }

    public function dialogue(): ?\SavvyAI\Models\Dialogue
    {
        // TODO: Implement dialogue() method.
    }
}
