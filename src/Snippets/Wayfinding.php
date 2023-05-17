<?php

namespace SavvyAI\Snippets;

use SavvyAI\Traits\InteractsWithAIService;
use SavvyAI\Traits\InteractsWithVectorStore;

class Wayfinding extends Snippet
{
    use InteractsWithAIService;
    use InteractsWithVectorStore;

    public string $namespace = '';
    public array $filters = [];

    public function expand(string $input): self
    {
        $this->media = [
            'place1' => [
                'bobs-hot-dogs' => [
                    'name' => 'Bob\'s Hot Dogs',
                    'entityId' => 'bobs-hot-dogs',
                    'address' => '123 Main St',
                ]
            ]
        ];

        return $this;
    }
}
