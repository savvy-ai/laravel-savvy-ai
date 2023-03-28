<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\File;

trait InteractsWithVectorStore
{
    /**
     * @param array<int, float> $vector
     * @param string $namespace
     * @param array<string, string> $filter
     *
     * @return array<int, string>
     */
    public function search(array $vector, string $namespace, array $filter = []): array
    {
        $dir = storage_path('statements/' . $namespace);

        $matches = pinecone()->post('/query', [
            'vector'    => $vector,
            'namespace' => $namespace,
            'topK'      => 10,
        ])->json('matches');

        $sentences = [];

        foreach ($matches as $match)
        {
            $sentences[] = file_get_contents($dir.'/'.$match['id'].'.txt');
        }

        return $sentences;
    }

    public function store(array $vectors, string $namespace, array $metadata = []): bool
    {
        $dir = storage_path('statements/' . $namespace);

        if (!is_readable($dir))
        {
            File::makeDirectory($dir, 0777, true, true);
        }

        $stored = pinecone()->post('/vectors/upsert', [
            'namespace' => $namespace,
            'vectors'   => collect($vectors)->map(function ($vector) use ($dir, $metadata) {
                $key = sha1($vector['sentence']);

                file_put_contents(sprintf('%s/%s.txt', $dir, $key), $vector['sentence']);

                return [
                    'id'       => $key,
                    'values'   => $vector['values'],
                    'metadata' => array_merge($metadata, compact('key')),
                ];
            })->toArray(),
        ])->json('upsertedCount');

        return $stored > 0;
    }

    public function destroy(string $namespace, array $filters = []): bool
    {
        $dir = storage_path('statements/' . $namespace);

        if (is_readable($dir))
        {
            File::deleteDirectory($dir);
        }

        $params = [
            'namespace' => $namespace,
            'deleteAll' => true,
        ];

        if (!empty($filters))
        {
            $params['filters'] = $filters;
        }

        pinecone()->post('/vectors/delete', $params)->json();

        return true;
    }
}
