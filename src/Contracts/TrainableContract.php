<?php

namespace SavvyAI\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface TrainableContract
{
    public function getStatementRepository(): Builder;
}
