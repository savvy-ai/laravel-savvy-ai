<?php

namespace SavvyAI\Exceptions;

/**
 * Thrown when a dialogue is off topic
 *
 * Class OffTopicException
 *
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Exceptions
 */
class OffTopicException extends \Exception
{
    public function __construct(string $message = 'Off Topic', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
