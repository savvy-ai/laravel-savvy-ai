<?php

namespace SavvyAI\Traits;

trait ExpandsPromptSnippets
{
    const NAMESPACE       = '\\SavvyAI\\Snippets\\';
    const CAST_REGEX      = '/(\w+):\s?(\w+)/';
    const SNIPPET_REGEX   = '/<(\S+)(\s+[^>]+)?\s*\/?>/';
    const ATTRIBUTE_REGEX = '/([^\s=]+)="([^"]+)"/';

    public function expand(string $prompt, string $input = ''): string
    {
        preg_match_all(self::SNIPPET_REGEX, $prompt, $matches, PREG_SET_ORDER);

        foreach ($matches as $match)
        {
            $snippet    = $match[1];
            $attributes = [];

            preg_match_all(self::ATTRIBUTE_REGEX, $match[0], $attributeMatches, PREG_SET_ORDER);

            foreach ($attributeMatches as $attributeMatch)
            {
                $attributes[$attributeMatch[1]] = $this->cast($attributeMatch[2]);
            }

            $name    = self::NAMESPACE . $snippet;
            $snippet = new $name($attributes);

            $prompt = str_replace($match[0], $snippet->use($input), $prompt);
        }

        return $prompt;
    }

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
}
