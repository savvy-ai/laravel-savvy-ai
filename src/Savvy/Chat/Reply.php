<?php

namespace SavvyAI\Savvy\Chat;

use SavvyAI\Models\Agent;
use SavvyAI\Models\Dialogue;
use SavvyAI\Exceptions\AgentNotFoundException;
use SavvyAI\Exceptions\DialogueNotFoundException;
use Illuminate\Support\Facades\Log;

/**
 * Represents a response from the completions API
 *
 * Class Reply
 * @author Selvin Ortiz <selvin@savvhost.ai>
 * @package SavvyAI\Savvy\Chat
 */
class Reply
{
    private array $usage;
    private array $message;

    /**
     * @param array $result Result from the completions API request
     */
    public function __construct($result)
    {
        $this->usage   = $result['usage'] ?? [];
        $this->message = $result['choices'][0]['message'] ?? [];
    }

    public function message(): array
    {
        return $this->message;
    }

    public function role(): string
    {
        return $this->message['role'] ?? '';
    }

    public function content(): string
    {
        return $this->message['content'] ?? '';
    }

    public function unknown(): bool
    {
        return mb_stripos($this->content(), '@Unknown') !== false;
    }

    public function onTopic(): bool
    {
        return mb_stripos($this->content(), '@OnTopic') !== false;
    }

    public function entity()
    {
        preg_match('/@([a-zA-Z]+)\(([^)]+)?\)/', $this->content(), $matches);

        Log::debug('Reply::entity() -> '. $this->content(), $matches);

        return [
            'class' => $matches[1] ?? '',
            'input' => $matches[2] ?? '',
        ];
    }

    public function agent(): ?Agent
    {
        $entity = $this->entity();

        return Agent::where('name', $entity['class'] ?? null)->first();
    }

    public function dialogue(): ?Dialogue
    {
        $entity = $this->entity();

        return Dialogue::where('name', $entity['class'] ?? null)->first();
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

    public function toArray(): array
    {
        return [
            'usage'   => $this->usage,
            'message' => $this->message,
        ];
    }
}
