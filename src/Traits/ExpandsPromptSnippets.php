<?php

namespace SavvyAI\Traits;

use Exception;
use Illuminate\Support\Facades\Config;
use SavvyAI\Contracts\SnippetResolverContract;
use SavvyAI\Snippets\Snippet;

trait ExpandsPromptSnippets
{
    const CAST_REGEX = '/([a-zA-Z0-9_-]+):\s?(\w+)/';
    const SNIPPET_REGEX = '/<(\S+)(\s+[^>]+)?\s*\/?>/';
    const ATTRIBUTE_REGEX = '/([^\s=]+)="([^"]+)"/';

    protected ?SnippetResolverContract $resolver;

    public function setResolver(SnippetResolverContract $resolver): self
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * @param string $prompt
     * @param string $input
     *
     * @return string
     */
    public function expand(string $prompt, string $input = ''): string
    {
        preg_match_all(self::SNIPPET_REGEX, $prompt, $matches, PREG_SET_ORDER);

        foreach ($matches as $match)
        {
            $snippet = rtrim($match[1], '/'); // Remove trailing slash
            $attributes = [];

            preg_match_all(self::ATTRIBUTE_REGEX, $match[0], $attributeMatches, PREG_SET_ORDER);

            foreach ($attributeMatches as $attributeMatch)
            {
                $attributes[$attributeMatch[1]] = $this->cast($attributeMatch[2]);
            }

            try
            {
                $snippet = $this->resolver
                    ? $this->resolver->resolve($snippet, $attributes)
                    : $this->resolveSnippet($snippet, $attributes);

                $prompt = str_replace($match[0], $snippet->use($input), $prompt);
            }
            catch (Exception $e)
            {
                //
            }
        }

        return $prompt;
    }

    /**
     * Given a string value, attempt to cast it to the appropriate type
     *
     * @param string $value
     *
     * @return mixed
     * @example
     * <ConnectionSpeed cost="29.99" zones="zone1: Arizona, zone2: California, zone3: Nevada" />
     * [cost => 29.99, zones => [zone1 => Arizona, zone2 => California, zone3 => Nevada]]
     *
     */
    public function cast(string $value): mixed
    {
        if (is_numeric($value))
        {
            return $value + 0;
        }

        if (in_array(mb_strtolower($value), ['true', 'false']))
        {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if (in_array(mb_strtolower($value), ['null', 'nil', '']))
        {
            return null;
        }

        preg_match_all(self::CAST_REGEX, $value, $matches);

        if (!empty($matches[1]))
        {
            return array_combine($matches[1], $matches[2]);
        }

        return $value;
    }

    /**
     * @param string $snippet
     * @param array $attributes
     *
     * @return Snippet
     * @throws Exception
     */
    public function resolveSnippet(string $snippet, array $attributes = []): Snippet
    {
        $class = sprintf('%s\\%s', Config::get('savvy-ai.snippets.namespace', '\\SavvyAI\\Snippets'), ucfirst($snippet));

        if (!class_exists($class))
        {
            throw new Exception(sprintf('Snippet %s does not exist', $class));
        }

        return new $class($attributes);
    }
}
