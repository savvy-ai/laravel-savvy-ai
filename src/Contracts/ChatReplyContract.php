<?php

namespace SavvyAI\Contracts;

/**
 * Represents a response from the AI service
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 *
 * @package SavvyAI\Contracts
 */
interface ChatReplyContract
{
    public static function fromAIServiceResponse(array $response): self;

    public function role(?string $role = null): string;
    public function content(?string $content = null): string;
    public function media(?array $media = null): array;

    public function totalTokensUsed(): int;
    public function promptTokensUsed(): int;
    public function completionTokensUsed(): int;

    public function isOnTopic(): bool;
    public function isContextUnknown(string $expected = null): bool;

    public function extractDelegateName(): string;
}
