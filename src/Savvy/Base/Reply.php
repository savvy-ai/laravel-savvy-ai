<?php

namespace SavvyAI\Savvy\Base;

use SavvyAI\Models\Statement;

/**
 * Represents a response from the completions API
 *
 * Class Reply
 * @author Selvin Ortiz <selvin@savvhost.ai>
 * @package SavvyAI\Savvy\Base
 */
class Reply
{
    public string $text;
    public int    $tokens;
    public array  $statements;

    /**
     * @param array $result - From the completions API
     * @param Statement[] $statement
     */
    public function __construct($result, array $statements)
    {
        $this->text       = $result['choices'][0]['text'] ?? '';
        $this->tokens     = $result['usage']['total_tokens'] ?? 0;
        $this->statements = $statements;
    }
}
