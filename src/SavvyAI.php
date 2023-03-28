<?php

namespace SavvyAI;

use SavvyAI\Traits\InteractsWithAIService;
use SavvyAI\Traits\InteractsWithVectorStore;
use SavvyAI\Traits\TrainsWithAIService;

class SavvyAI implements Contracts\AI\ServiceContract, Contracts\Vector\StoreContract
{
    use InteractsWithAIService;
    use InteractsWithVectorStore;
    use TrainsWithAIService;
}
