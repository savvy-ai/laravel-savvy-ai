<?php

namespace SavvyAI\Contracts\Vector;

interface StoreContract
{
    public function store(array $vectors, string $namespace, array $metadata = []): bool;
    public function search(array $vector, string $namespace, array $filter = []): array;
}
