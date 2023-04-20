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
    public function getSplitter(): Splitter
    {
        return new Splitter();
    }

    public function getVectorizer(): Vectorizer
    {
        return new Vectorizer();
    }

    public function train(TrainableContract $trainable, string $text, string $namespace, array $metadata = []): bool
    {
        $statements = $this->summarizeForTraining($text);

        foreach ($statements as $statement)
        {
            $trainable->getStatementRepository()->create(compact('statement'));
        }

        return true;
    }

    /**
     * @param string $text
     *
     * @return array
     */
    public function summarizeForTraining(string $text): array
    {
        return $this->getSplitter()->split($text);
    }

    /**
     * @param array $sentences
     *
     * @return array
     */
    public function vectorizeForStorage(array $sentences): array
    {
        return $this->getVectorizer()->vectorize($sentences);
    }
}
