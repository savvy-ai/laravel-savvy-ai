<?php

namespace SavvyAI;

use SavvyAI\Traits\InteractsWithAIService;
use SavvyAI\Traits\InteractsWithVectorStore;
use SavvyAI\Traits\TrainsWithAIService;

class SavvyAI implements Contracts\AIServiceContract, Contracts\VectorStoreContract
{
    use InteractsWithAIService;
    use InteractsWithVectorStore;
    use TrainsWithAIService;
}
