<?php

namespace SavvyAI\Contracts\AI;

interface Client
{
    public function classify(string $text, array $subjects = [], string $expectedStringInReply = null): Reply;
    public function validate(string $text, string $topic): Reply;
    public function vectorize(string $text): array;
    public function chat(array $messages = []): Reply;
}
