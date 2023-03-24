<?php

namespace SavvyAI;

use SavvyAI\Traits\InteractsWithOpenAI;

class DummyForChatting
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
