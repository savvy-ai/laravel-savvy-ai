<?php

namespace SavvyAI\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface VectorStoreContract
{
    public function store(array $vectors, string $namespace, array $metadata = [], Builder $statements = null): bool;
    public function search(array $vector, string $namespace, array $filter = []): array;
    public function destroy(string $namespace, array $filters = []): bool;
}
