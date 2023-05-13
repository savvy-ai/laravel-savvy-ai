<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\TrainableContract;
use SavvyAI\Features\Training\Vectorizer;

/**
 * Makes calls to the OpenAI API to vectorize and summarize text for training purposes
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 *
 * @package SavvyAI\Traits
 */
trait TrainsWithAIService
{
    public function train(TrainableContract $trainable, string $text, string $namespace, array $metadata = []): bool
    {
        $statements = $trainable->getTextSplitter()->split($text);

        foreach ($statements as $statement)
        {
            $trainable->getStatementRepository()->create(compact('statement'));
        }

        return true;
    }

    public function trainInBatches(TrainableContract $trainable, string $text, string $namespace, array $metadata = []): bool
    {
        Log::debug('Training in batches', [
            'namespace' => $namespace,
            'metadata' => $metadata,
        ]);

        $segments = $trainable->getTextSplitter()->split($text);

        Log::debug('Split text into segments', [
            'segments' => $segments,
        ]);

        foreach (array_chunk($segments, $trainable->getBatchSize()) as $batch)
        {
            Log::debug('Vectorizing batch', [
                'batch' => $batch,
            ]);

            $statements = $trainable->vectorize($batch);
            $savedStatements = $trainable->getStatementRepository()->createManyQuietly(array_map(function ($statement) {
                return [
                    'statement' => $statement['statement'],
                ];
            }, $statements));

            vector()->post('/vectors/upsert', [
                'namespace' => $namespace,
                'vectors' => array_map(function ($statement) use ($savedStatements) {
                    return [
                        'id' => $savedStatements[$statement['id']]->id,
                        'values' => $statement['values'],
                    ];
                }, $statements),
            ]);
        }

        Log::debug('Finished training in batches', [
            'namespace' => $namespace,
            'metadata' => $metadata,
        ]);

        return true;
    }
}
