<?php

namespace SavvyAI\Exceptions;

class DelegateNotFoundException extends \Exception
{
    public function __construct(string $message = 'I am sorry, but I am not equipped to answer that question.', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}