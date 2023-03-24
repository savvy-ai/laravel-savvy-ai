<?php

namespace SavvyAI\Features\Training;

use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;

/**
 * Splits text into tokens.
 *
 * This is important because the OpenAI API has a limit of 4000 tokens per request.
 * Splitting the text into tokens allows to determine if the given text is below a threshhold.
 * If the text exceeds the threshhold, it can be split into smaller segments by the Segmenter class.
 *
 * Class Tokenizer
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Savvy
 */
class Tokenizer
{
    private $tokenizer;

    public function __construct(Gpt3TokenizerConfig $config = new Gpt3TokenizerConfig())
    {
        $this->tokenizer = new Gpt3Tokenizer($config);
    }

    public function tokenize(string $text): array
    {
        return $this->tokenizer->encode($text);
    }

    public function count(string $text): int
    {
        return $this->tokenizer->count($text);
    }
}
