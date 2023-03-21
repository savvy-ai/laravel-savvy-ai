<?php

namespace SavvyAI\Exceptions;

use InvalidArgumentException;

final class MissingApiKeyException extends InvalidArgumentException
{
    public static function create(): self
    {
        return new self(
            'Missing API key. Please set the OPENAI_API_KEY environment variable.'
        );
    }
}
