<?php

namespace SavvyAI\Features\Training;

class Vectorizer
{
    public int $maxTokens = 1000;

    public function __construct(int $maxTokens = 1000)
    {
        $this->maxTokens = $maxTokens;
    }

    public function vectorize(array $statements): array
    {
        $response = ai()->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $statements,
        ]);

        $vectors = [];

        foreach ($response->embeddings as $embedding)
        {
            $vectors[] = [
                'id' => $embedding->index,
                'values' => $embedding->embedding,
                'statement' => $statements[$embedding->index],
            ];
        }

        return $vectors;
    }
}
