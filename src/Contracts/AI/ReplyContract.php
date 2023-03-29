<?php

namespace SavvyAI\Contracts\AI;

use Illuminate\Database\Eloquent\Model;

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
interface ReplyContract
{
    public static function fromClientResponse(array $response): self;

    public function role(): string;
    public function content(): string;

    public function totalTokensUsed(): int;
    public function promptTokensUsed(): int;
    public function completionTokensUsed(): int;

    public function isOnTopic(): bool;
    public function isContextUnknown(string $expected = null): bool;

    public function agent(): string;
    public function dialogue(): string;
}
