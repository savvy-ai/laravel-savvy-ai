<?php

namespace SavvyAI;

use SavvyAI\Traits\LearnsWithOpenAI;

class DummyForTraining
{
    use LearnsWithOpenAI;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
