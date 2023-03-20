<?php

namespace SavvyAI\Savvy\Config;

use SavvyAI\Models\Property;
use SavvyAI\Models\User;

class PromptConfig
{
    const DEFAULT_MAX_TOKENS  = 1000;
    const DEFAULT_MAX_RESULTS = 5;

    public int       $maxTokens;
    public int       $maxResults;
    public ?string   $namespace;
    public ?array    $metadata;
    public ?Property $property;

    /**
     * @var []Agent
     */
    public array     $agents;

    public ?User     $user;

    public function __construct(array $config = [])
    {
        $this->maxTokens  = $config['maxTokens'] ?? self::DEFAULT_MAX_TOKENS;
        $this->maxResults = $config['maxResults'] ?? self::DEFAULT_MAX_RESULTS;
        $this->namespace  = $config['namespace'] ?? $config['user']->uuid ?? null;
        $this->metadata   = $config['metadata'] ?? [];
        $this->property   = $config['property'] ?? null;
        $this->agents     = $config['agents'] ?? [];
        $this->user       = $config['user'] ?? null;
    }
}
