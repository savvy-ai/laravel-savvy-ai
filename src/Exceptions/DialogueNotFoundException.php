<?php

namespace SavvyAI\Exceptions;

class DialogueNotFoundException extends \Exception
{
    public function __construct(string $message = 'Dialogue not found', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
