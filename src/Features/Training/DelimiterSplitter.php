<?php

namespace SavvyAI\Features\Training;

class DelimiterSplitter extends Splitter
{
    public string $delimiter = PHP_EOL;

    public function __construct(string $delimiter = PHP_EOL)
    {
        parent::__construct();

        $this->delimiter = $delimiter;
    }

    public function split(string $text): array
    {
        return explode($this->delimiter, $text);
    }
}
