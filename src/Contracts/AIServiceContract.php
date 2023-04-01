<?php

namespace SavvyAI\Contracts;

/**
 * Interface AIServiceContract
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 *
 * @package SavvyAI\Contracts
 */
interface AIServiceContract
{
    public function chat(array $messages = []): ChatReplyContract;

    public function classify(string $text, array $subjects = [], string $expectedStringInReply = null): ChatReplyContract;

    public function validate(string $text, string $topic): ChatReplyContract;

    public function vectorize(string $text): array;

    public function summarizeForTraining(string $text, int $minLength = 16, int $maxLength = 256): array;

    public function vectorizeForStorage(array $sentences): array;
}
