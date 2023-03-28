<?php

namespace SavvyAI\Contracts\AI;

interface ServiceContract
{
    public function chat(array $messages = []): ReplyContract;

    public function classify(string $text, array $subjects = [], string $expectedStringInReply = null): ReplyContract;
    public function validate(string $text, string $topic): ReplyContract;

    public function vectorize(string $text): array;

    public function summarizeForTraining(string $text, int $minLength = 16, int $maxLength = 256): array;
    public function vectorizeForStorage(array $sentences): array;
}
