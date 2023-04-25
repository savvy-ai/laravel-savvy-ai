<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Log;
use SavvyAI\Contracts\TrainableContract;
use SavvyAI\Features\Training\Splitter;
use SavvyAI\Features\Training\Vectorizer;
use Vanderlee\Sentence\Sentence;

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
            if (empty($statement))
            {
                continue;
            }

            $trainable->getStatementRepository()->create(compact('statement'));
        }

        return true;
    }
}
