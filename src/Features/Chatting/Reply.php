<?php

namespace SavvyAI\Features\Chatting;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SavvyAI\Models\Agent;
use SavvyAI\Models\Dialogue;
use SavvyAI\Models\Message;

/**
 * Represents a response from the completions API
 *
 * Class Reply
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Chat
 */
class Reply
{
    protected array $usage;
    protected array $message;

    /**
     * @param array $result Result from the completions API request
     */
    public function __construct($result)
    {
        $this->usage   = $result['usage'] ?? [];
        $this->message = $result['choices'][0]['message'] ?? [];
    }

    public function message(): Message
    {
        return new Message([
            'role' => $this->role(),
            'content' => $this->content(),
        ]);
    }

    public function role(): string
    {
        return $this->message['role'] ?? '';
    }

    public function content(): string
    {
        return $this->message['content'] ?? '';
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

    public function isOnTopic(): bool
    {
        return mb_stripos($this->content(), '@OnTopic') !== false;
    }

    public function agent(): ?Agent
    {
        return Agent::where('name', $this->entity()['class'] ?? null)->first();
    }

    public function dialogue(): ?Dialogue
    {
        return Dialogue::where('name', $this->entity()['class'] ?? null)->first();
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

    protected function entity()
    {
        preg_match('/@([a-zA-Z]+)\(([^)]+)?\)/', $this->content(), $matches);

        Log::debug('Reply::entity() -> '. $this->content(), $matches);

        return [
            'class' => $matches[1] ?? '',
            'input' => $matches[2] ?? '',
        ];
    }
}
