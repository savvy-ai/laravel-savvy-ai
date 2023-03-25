<?php

namespace SavvyAI;

use SavvyAI\Traits\InteractsWithOpenAI;
use SavvyAI\Traits\InteractsWithPinecone;

class DummyForChatting
{
    use InteractsWithOpenAI;
    use InteractsWithPinecone;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
