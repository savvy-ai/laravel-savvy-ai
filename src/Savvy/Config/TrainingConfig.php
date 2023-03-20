<?php

namespace SavvyAI\Savvy\Config;

use SavvyAI\Models\Property;
use SavvyAI\Models\User;

class TrainingConfig
{
    const DEFAULT_MAX_SEGMENT_TOKENS = 250;
    const DEFAULT_MAX_SUMMARY_TOKENS = 2800;

    const TOKENIZING  = 'tokenizing';
    const SEGMENTING  = 'segmenting';
    const SUMMARIZING = 'summarizing';
    const VECTORIZING = 'vectorizing';
    const COMPLETED   = 'completed';

    public int       $maxSegmentTokens;
    public int       $maxSummaryTokens;
    public ?string   $namespace;
    public ?array    $metadata;
    public ?Property $property;
    public ?User     $user;

    public function __construct(array $config = [])
    {
        $this->maxSegmentTokens = $config['maxSegmentTokens'] ?? self::DEFAULT_MAX_SEGMENT_TOKENS;
        $this->maxSummaryTokens = $config['maxSummaryTokens'] ?? self::DEFAULT_MAX_SUMMARY_TOKENS;
        $this->namespace        = $config['namespace'] ?? $config['user']->uuid ?? null;
        $this->metadata         = $config['metadata'] ?? [];
        $this->property         = $config['property'] ?? null;
        $this->user             = $config['user'] ?? null;
    }
}
