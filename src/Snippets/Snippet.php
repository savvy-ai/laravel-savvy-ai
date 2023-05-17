<?php

namespace SavvyAI\Snippets;

use SavvyAI\Contracts\SnippetResolverContract;

abstract class Snippet
{
    protected array $media = [];
    protected string $expanded = '';

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

    public function expended(): string
    {
        return $this->expanded;
    }

    public function media(): array
    {
        return $this->media;
    }

    abstract public function expand(string $input): self;
}
