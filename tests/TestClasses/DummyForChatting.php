<?php

namespace SavvyAI\Tests\TestClasses;

use SavvyAI\Traits\InteractsWithAIService;
use SavvyAI\Traits\InteractsWithVectorStore;

class DummyForChatting
{
    use InteractsWithAIService;
    use InteractsWithVectorStore;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
