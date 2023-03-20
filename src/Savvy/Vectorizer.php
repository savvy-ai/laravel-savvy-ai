<?php

namespace SavvyAI\Savvy;

use SavvyAI\Models\Statement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Turns text statements into vectors using the OpenAI embeddings API and the Pinecone API.
 *
 * Class Vectorizer
 * @author Selvin Ortiz <selvin@savvhost.ai>
 * @package SavvyAI\Savvy
 */
class Vectorizer
{
    /**
     * @param Statement[] $statements Statement models to vectorize.
     * @param string $namespace Namespaces the vector in Pinecone.
     * @param array $metadata
     *
     * @return $this
     */
    public function vectorize(array $statements, string $namespace, array $metadata = []): self
    {
        $response = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => array_map(function ($statement)
            {
                return $statement->statement;
            }, $statements),
        ]);

        $vectors = array_map(function ($embedding) use ($statements, $metadata)
        {
            $mapped = [
                'id'     => (string)$statements[$embedding->index]->id,
                'values' => $embedding->embedding,
            ];

            if (!empty($metadata))
            {
                $mapped['metadata'] = $metadata;
            }

            return $mapped;
        }, $response->embeddings);

        Http::pinecone()->post('/vectors/upsert', [
            'vectors'   => $vectors,
            'namespace' => $namespace,
        ])->json();

        return $this;
    }

    public function delete(array $ids, string $namespace): self
    {
        if (empty($ids))
        {
            return $this;
        }

        // Pinecone requires IDs to be strings
        $ids = array_map(function ($id)
        {
            return (string)$id;
        }, $ids);

        Http::pinecone()->post('/vectors/delete', [
            'ids'       => $ids,
            'namespace' => $namespace,
            'deleteAll' => false,
        ])->json();

        return $this;
    }
}
