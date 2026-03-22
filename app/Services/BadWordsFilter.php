<?php

namespace App\Services;

class BadWordsFilter
{
    protected static $pattern = null;

    /**
     * Build the regex pattern from config
     *
     * @return string
     */
    protected static function getPattern(): string
    {
        if (static::$pattern !== null) {
            return static::$pattern;
        }

        $words = config('badwords.words', []);
        
        if (empty($words)) {
            // Fallback pattern if config is empty
            static::$pattern = '/\b(?:fuck\w*|shit\w*|bitch\w*|asshole|damn(?:ed|ing)?)\b/i';
            return static::$pattern;
        }

        // Escape each word for regex and join with |
        $escapedWords = array_map(function($word) {
            return preg_quote($word, '/') . '\w*';
        }, $words);

        $pattern = '/\b(?:' . implode('|', $escapedWords) . ')\b/i';
        static::$pattern = $pattern;

        return static::$pattern;
    }

    /**
     * Mask bad words in text by replacing with asterisks
     *
     * @param string|null $text
     * @return string|null
     */
    public static function mask(?string $text): ?string
    {
        if ($text === null || $text === '') return $text;

        $pattern = static::getPattern();

        return preg_replace_callback($pattern, function($m) {
            return str_repeat('*', strlen($m[0]));
        }, $text);
    }

    /**
     * Check if text contains bad words
     *
     * @param string|null $text
     * @return bool
     */
    public static function contains(?string $text): bool
    {
        if ($text === null || $text === '') return false;

        $pattern = static::getPattern();

        return preg_match($pattern, $text) === 1;
    }

    /**
     * Get array of bad words found in text
     *
     * @param string|null $text
     * @return array
     */
    public static function extract(?string $text): array
    {
        if ($text === null || $text === '') return [];

        $pattern = static::getPattern();
        $matches = [];

        if (preg_match_all($pattern, $text, $matches)) {
            return array_unique(array_map('strtolower', $matches[0]));
        }

        return [];
    }

    /**
     * Clear cached pattern (useful after config changes)
     */
    public static function clearCache(): void
    {
        static::$pattern = null;
    }
}
