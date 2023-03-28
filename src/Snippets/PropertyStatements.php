<?php

namespace SavvyAI\Snippets;

use SavvyAI\Traits\InteractsWithAIService;
use SavvyAI\Traits\InteractsWithVectorStore;

class PropertyStatements extends Snippet
{
    use InteractsWithVectorStore;
    use InteractsWithAIService;

    public string $namespace;
    public array $filters;

    public function use(string $input): string
    {
        $vector    = $this->vectorize($input);
        $sentences = $this->search($vector, $this->namespace, $this->filters);

        return implode(PHP_EOL, $sentences);
    }
}
