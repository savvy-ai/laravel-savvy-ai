<?php

namespace SavvyAI\Contracts\AI;

interface ServiceContract
{
    public function chat(array $messages = []): ReplyContract;

    public function classify(string $text, array $subjects = [], string $expectedStringInReply = null): ReplyContract;
    public function validate(string $text, string $topic): ReplyContract;

    public function vectorize(string $text): array;
}
