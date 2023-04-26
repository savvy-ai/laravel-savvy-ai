<?php

namespace SavvyAI\Features\Training;

use Vanderlee\Sentence\Sentence;

class Splitter
{
    public int $minLength = 16;
    public int $maxLength = 256;

    public function __construct(int $minLength = 16, int $maxLength = 256)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * @param string $text
     *
     * @return array<int, string> Text chunks
     */
    public function split(string $text): array
    {
        $sentences = (new Sentence())->split($text, Sentence::SPLIT_TRIM);

        $mergedSentences = [];
        $lastSentences = [];

        while (!empty($sentences))
        {
            $sentence = $currentSentence = array_shift($sentences);

            if (!empty($lastSentences))
            {
                $sentence = implode(' ', $lastSentences) . ' ' . $sentence;
            }

            if (mb_strlen($sentence) < $this->minLength)
            {
                $lastSentences[] = $currentSentence;

                continue;
            }

            if (mb_strlen($sentence) > $this->maxLength)
            {
                if (!empty($lastSentences))
                {
                    array_unshift($sentences, $currentSentence);

                    $sentence = implode(' ', $lastSentences);
                }
            }

            $lastSentences = [];
            $mergedSentences[] = $sentence;
        }

        return $mergedSentences;
    }
}
