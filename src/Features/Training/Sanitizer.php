<?php

namespace SavvyAI\Features\Training;

/**
 * Sanitizes text.
 *
 * Class Sanitizer
 * @author Selvin Ortiz <selvin@savvyai.com>
 * @author Brennen Phippen <brennen@savvyai.com>
 * @package SavvyAI\Savvy
 */
class Sanitizer
{
    /**
     * @param string $text
     *
     * @return str
     */
    public function sanitize(string $text): string
    {
        return trim($this->lines($this->utf8($text)));
    }

    /**
     * @param string $text
     */
    private function lines(string $text): string
    {
        return preg_replace("/\r\n|\r|\n/", "\n", $text);
    }

    /**
     * @param string $text
     */
    private function utf8(string $text): string
    {
        return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $text);
    }
}
