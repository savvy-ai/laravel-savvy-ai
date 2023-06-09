<?php

namespace SavvyAI\Exceptions;

class DelegateNotFoundException extends \Exception
{
    public function __construct(string $message = 'I\'m not equipped to answer that question, and I apologize for any inconvenience.', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
