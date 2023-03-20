<?php

namespace SavvyAI\Savvy;

use Illuminate\Support\Facades\Log;

/**
 * Splits text into smaller segments.
 *
 * This is important because the OpenAI API has a limit of 4000 tokens per request.
 * Splitting the text into smaller segments allows us to send multiple requests to the API.
 *
 * Class Segmenter
 * @author Selvin Ortiz <selvin@savvhost.ai>
 * @package SavvyAI\Savvy
 */
class Segmenter
{
    /**
     * Splits text into smaller segments using size as the number of segments to split into
     *
     * @param string $text
     * @param int $size
     *
     * @return array
     */
    public function segment(string $text, int $size): array
    {
        $segments = [];

        $lines  = array_filter(preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $text));
        $chunks = array_chunk($lines, ceil(count($lines) / $size));

        foreach ($chunks as $chunk)
        {
            $segments[] = trim(implode(' ', $chunk));
        }

        return $segments;
    }
}
