<?php

namespace SavvyAI\Snippets;

class Expanded
{
    public string $text;
    public array $media;

    public function __construct(?string $text =  null, ?array $media = null)
    {
        $this->text = $text ?? '';
        $this->media = $media ?? [];
    }
}
