<?php

namespace SavvyAI;

use SavvyAI\Traits\InteractsWithOpenAI;

class Dummy
{
    use InteractsWithOpenAI;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
