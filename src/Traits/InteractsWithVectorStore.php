<?php

namespace SavvyAI\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait InteractsWithVectorStore
{
    /**
     * @param array<int, float> $vector
     * @param string $namespace
     * @param array<string, string> $filter
     * @param Builder|null $statements
     * @return array<int, string>
     */
    public function search(array $vector, string $namespace, array $filter = [], Builder $statements = null): array
    {
        $matches = vector()->post('/query', [
            'vector' => $vector,
            'namespace' => $namespace,
            'topK' => 20,
        ])->json('matches');

        if ($statements)
        {
            return $statements->whereIn('id', collect($matches)->pluck('id')->toArray())
                ->get()
                ->toArray();
        }

        return $matches;
    }

    public function store(array $vectors, string $namespace, array $metadata = [], Builder $statements = null): bool
    {
        $stored = vector()->post('/vectors/upsert', [
            'namespace' => $namespace,
            'vectors' => collect($vectors)->map(function ($vector) use ($statements, $metadata) {
                $key = sha1($vector['sentence']);

                if ($statements)
                {
                    $model = $statements->create([
                        'statement' => $vector['sentence'],
                    ]);

                    $key = $model->id;
                }

                return [
                    'id' => $key,
                    'values' => $vector['values'],
                    'metadata' => array_merge($metadata, compact('key')),
                ];
            })->toArray(),
        ])->json('upsertedCount');

        return $stored > 0;
    }

    public function destroy(string $namespace, array $filters = []): bool
    {
        $params = [
            'namespace' => $namespace,
            'deleteAll' => true,
        ];

        if (!empty($filters))
        {
            $params['filters'] = $filters;
        }

        vector()->post('/vectors/delete', $params)->json();

        return true;
    }
}
