<?php

namespace SavvyAI\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;
use SavvyAI\Features\Training\Splitter;

interface TrainableContract
{
    public function getTextSplitter(): Splitter;
    public function getStatementRepository(): Builder;
}
