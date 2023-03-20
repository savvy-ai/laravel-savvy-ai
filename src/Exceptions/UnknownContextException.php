<?php

namespace SavvyAI\Exceptions;

class UnknownContextException extends \Exception
{
    public function __construct(string $message = 'Unknown Context', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
