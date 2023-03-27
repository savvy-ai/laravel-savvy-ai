<?php

namespace SavvyAI;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use SavvyAI\Traits\InteractsWithAIService;
use SavvyAI\Traits\InteractsWithVectorStore;
use SavvyAI\Traits\LearnsWithAIService;

class Savvy implements Contracts\AI\ServiceContract, Contracts\Vector\StoreContract
{
    use InteractsWithAIService;
    use InteractsWithVectorStore;
    use LearnsWithAIService;

    public function train(string $text, string $namespace, array $metadata = []): int
    {
        $sentences = $this->summarizeForTraining($text, 128, 512);
        $vectors   = $this->vectorizeForStorage($sentences);
        $stored    = $this->store($vectors, $namespace, $metadata);

        Log::info('SavvyAI: Training completed', [
            'namespace' => $namespace,
            'metadata'  => $metadata,
            'stored'    => $stored,
        ]);

        return $stored;
    }
}
