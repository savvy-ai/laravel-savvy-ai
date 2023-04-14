<?php

namespace SavvyAI\Tests\Feature;

use SavvyAI\Contracts\SnippetResolverContract;
use SavvyAI\Snippets\Snippet;

class ExampleSnippet extends Snippet
{
    public string $name = '';
    public int $age = 0;

    public function use(string $input = ''): string
    {
        // @todo: Add support for multiple attributes dynamically
        return trim(sprintf(
            '%s + name=%s age=%s',
            $input, $this->name, $this->age
        ));
    }
}

class SnippetResolver implements SnippetResolverContract
{
    public function resolve(string $snippet, array $attributes = []): Snippet
    {
        return new ExampleSnippet($attributes);
    }
}

class SnippetExpansionTest extends \stdClass
{
    use \SavvyAI\Traits\ExpandsPromptSnippets;
}

it('can expand prompts without snippets', function () {
    $result = (new SnippetExpansionTest())->expand('Hello, world!');

    expect($result)->toBe('Hello, world!');
});

it('can expand prompts with snippets without attributes', function () {
    $result = (new SnippetExpansionTest())
        ->setResolver(new SnippetResolver())
        ->expand('Hello, <World />!');

    expect($result)->toBe('Hello, + name= age=0!');
});

it('can expand prompts with snippets with one or more attributes', function () {
    $result = (new SnippetExpansionTest())
        ->setResolver(new SnippetResolver())
        ->expand('Hello, <World name="Steven" age="21"/>!');

    expect($result)->toBe('Hello, + name=Steven age=21!');
});
