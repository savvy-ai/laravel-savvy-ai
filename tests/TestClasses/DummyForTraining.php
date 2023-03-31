<?php

namespace SavvyAI\Tests\TestClasses;

use SavvyAI\Traits\InteractsWithVectorStore;
use SavvyAI\Traits\TrainsWithAIService;

class DummyForTraining
{
    use TrainsWithAIService;
    use InteractsWithVectorStore;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
