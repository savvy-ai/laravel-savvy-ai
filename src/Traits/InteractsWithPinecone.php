<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\File;

trait InteractsWithPinecone
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
        $matches = pinecone()->post('/query', [
            'vector'    => $vector,
            'namespace' => $namespace,
            'topK'      => 10,
        ])->json('matches');

        $sentences = [];

        foreach ($matches as $match)
        {
            $sentences[] = file_get_contents(storage_path('sentences/'.$match['id'].'.txt'));
        }

        return $sentences;
    }

    public function memorize(array $vectors, string $namespace, array $metadata = [])
    {
        File::makeDirectory(storage_path('sentences'), 0777, true, true);

        return pinecone()->post('/vectors/upsert', [
            'namespace' => $namespace,
            'vectors'   => collect($vectors)->map(function ($vector) use ($metadata) {
                $key = sha1($vector['sentence']);

                file_put_contents(storage_path('sentences/'.$key.'.txt'), $vector['sentence']);

                return [
                    'id'       => $key,
                    'values'   => $vector['values'],
                    'metadata' => array_merge($metadata, compact('key')),
                ];
            })->toArray(),
        ])->json();
    }
}
