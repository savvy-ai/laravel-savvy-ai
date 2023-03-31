<?php

namespace SavvyAI\Contracts;

interface VectorStoreContract
{
    public function store(array $vectors, string $namespace, array $metadata = []): bool;
    public function search(array $vector, string $namespace, array $filter = []): array;
    public function destroy(string $namespace, array $filters = []): bool;
}
