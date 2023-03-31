<?php

namespace SavvyAI\Traits;

trait ExpandsPromptSnippets
{
    const NAMESPACE       = '\\SavvyAI\\Snippets\\';
    const CAST_REGEX      = '/([a-zA-Z0-9_-]+):\s?(\w+)/';
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

    /**
     * Given a string value, attempt to cast it to the appropriate type
     *
     * @example
     * <ConnectionSpeed cost="29.99" zones="zone1: Arizona, zone2: California, zone3: Nevada" />
     * [cost => 29.99, zones => [zone1 => Arizona, zone2 => California, zone3 => Nevada]]
     *
     * @param string $value
     *
     * @return mixed
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
}
