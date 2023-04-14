<?php

namespace SavvyAI\Snippets;

use SavvyAI\Contracts\SnippetResolverContract;

abstract class Snippet
{
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value)
        {
            if (property_exists($this, $key))
            {
                $this->{$key} = $value;
            }
        }
    }

    abstract public function use(string $input): string;
}
