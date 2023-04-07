<?php

namespace SavvyAI\Snippets;

abstract class Snippet
{
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value)
        {
            $this->{$key} = $value;
        }
    }

    abstract public function use(string $input): string;
}
