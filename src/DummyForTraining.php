<?php

namespace SavvyAI;

use SavvyAI\Traits\InteractsWithPinecone;
use SavvyAI\Traits\LearnsWithOpenAI;

class DummyForTraining
{
    use LearnsWithOpenAI;
    use InteractsWithPinecone;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
