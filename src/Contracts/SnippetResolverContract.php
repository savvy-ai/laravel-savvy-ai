<?php

namespace SavvyAI\Contracts;

use SavvyAI\Snippets\Snippet;

interface SnippetResolverContract
{
    public function resolve(string $snippet, array $attributes = []): Snippet;
}
