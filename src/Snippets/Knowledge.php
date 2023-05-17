<?php

namespace SavvyAI\Snippets;

use SavvyAI\Models\Statement;
use SavvyAI\Traits\InteractsWithAIService;
use SavvyAI\Traits\InteractsWithVectorStore;

class Knowledge extends Snippet
{
    use InteractsWithAIService;
    use InteractsWithVectorStore;

    public string $namespace = '';
    public array $filters = [];

    public function expand(string $input): self
    {
        $vector    = $this->vectorize($input);
        $sentences = $this->search($vector, $this->namespace, $this->filters);

        $statements = Statement::query()
            ->whereIn('id', collect($sentences)->pluck('id')->all())
            ->get()
            ->pluck('statement')
            ->all();

        $this->expanded = implode(PHP_EOL.'- ', $statements);

        return $this;
    }
}
