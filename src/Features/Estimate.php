<?php

namespace SavvyAI\Base;


/**
 * Represents a cost calculation from the the tokenizer
 *
 * Class Estimate
 * @author Selvin Ortiz <selvin@savvhost.ai>
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
