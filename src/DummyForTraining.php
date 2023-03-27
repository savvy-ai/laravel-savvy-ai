<?php

namespace SavvyAI;

use SavvyAI\Traits\InteractsWithVectorStore;
use SavvyAI\Traits\LearnsWithAIService;

class DummyForTraining
{
    use LearnsWithAIService;
    use InteractsWithVectorStore;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
