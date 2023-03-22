<?php

namespace SavvyAI\Features\Training;


/**
 * Represents a cost calculation from the the tokenizer
 *
 * Class Estimate
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @package SavvyAI\Base
 */
class Estimate
{
    public string $text;
    public int    $tokens;
    public float  $credits;

    public function __construct(string $text, int $tokens, float $credits)
    {
        $this->text    = $text;
        $this->tokens  = $tokens;
        $this->credits = $credits;
    }
}
