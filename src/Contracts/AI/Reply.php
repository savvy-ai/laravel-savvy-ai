<?php

namespace SavvyAI\Contracts\AI;

/**
 * Represents a response from the completions API
 *
 * Class Reply
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 *
 * @package SavvyAI\Contracts\AI
 */
interface Reply
{
    public static function fromClientResponse(): self;

    public function role(): string;
    public function content(): string;

    public function totalTokensUsed(): int;
    public function promptTokensUsed(): int;
    public function completionTokensUsed(): int;

    public function isOnTopic(): bool;
    public function isContextUnknown(string $expected = null): bool;

    public function agent(): ?\SavvyAI\Models\Agent;
    public function dialogue(): ?\SavvyAI\Models\Dialogue;
}
