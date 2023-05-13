<?php

namespace SavvyAI\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;
use SavvyAI\Features\Training\Splitter;

interface TrainableContract
{
    public function vectorize(array $statements): array;
    public function getBatchSize(): int;
    public function getMaxTokensPerBatch(): int;
    public function getTextSplitter(): Splitter;
    public function getStatementRepository(): Builder;
}
