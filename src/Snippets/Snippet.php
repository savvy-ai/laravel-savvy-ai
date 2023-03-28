<?php

namespace SavvyAI\Snippets;

class Snippet
{
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
