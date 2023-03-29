<?php

namespace SavvyAI\Traits;

use Illuminate\Support\Facades\Log;
use Vanderlee\Sentence\Sentence;

/**
 * Makes calls to the OpenAI API to vectorize and summarize text for training purposes
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Traits
 */
trait TrainsWithAIService
{
    public function train(string $text, string $namespace, array $metadata = []): bool
    {
        $sentences = $this->summarizeForTraining($text, 128, 512);
        $vectors   = $this->vectorizeForStorage($sentences);
        $stored    = $this->store($vectors, $namespace, $metadata);

        Log::info('SavvyAI: Training completed', [
            'namespace' => $namespace,
            'metadata'  => $metadata,
            'stored'    => $stored,
        ]);

        return $stored;
    }

    /**
     * @param string $text
     * @param int $minLength
     * @param int $maxLength
     *
     * @return array
     */
    public function summarizeForTraining(string $text, int $minLength = 16, int $maxLength = 256): array
    {
        $sentences = (new Sentence())->split($text, Sentence::SPLIT_TRIM);

        $mergedSentences = [];
        $lastSentences   = [];

        while(!empty($sentences))
        {
            $sentence = $currentSentence = array_shift($sentences);

            if (!empty($lastSentences))
            {
                $sentence = implode(' ', $lastSentences) . ' ' . $sentence;
            }

            if (mb_strlen($sentence) < $minLength)
            {
                $lastSentences[] = $currentSentence;

                continue;
            }

            if (mb_strlen($sentence) > $maxLength)
            {
                array_unshift($sentences, $currentSentence);

                $sentence = implode(' ', $lastSentences);
            }

            $lastSentences     = [];
            $mergedSentences[] = $sentence;
        }

        return $mergedSentences;
    }

    public function vectorizeForStorage(array $sentences): array
    {
        $response = ai()->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $sentences,
        ]);

        $vectors = [];

        foreach ($response->embeddings as $embedding)
        {
            $vectors[] = [
                'id'       => $embedding->index,
                'values'   => $embedding->embedding,
                'sentence' => $sentences[$embedding->index],
            ];
        }

        return $vectors;
    }
}
