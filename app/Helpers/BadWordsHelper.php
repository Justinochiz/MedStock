<?php

if (!function_exists('mask_bad_words')) {
    /**
     * Mask bad words in the given text
     *
     * @param string|null $text
     * @return string|null
     */
    function mask_bad_words(?string $text): ?string
    {
        return \App\Services\BadWordsFilter::mask($text);
    }
}

if (!function_exists('has_bad_words')) {
    /**
     * Check if text contains bad words
     *
     * @param string|null $text
     * @return bool
     */
    function has_bad_words(?string $text): bool
    {
        return \App\Services\BadWordsFilter::contains($text);
    }
}

if (!function_exists('extract_bad_words')) {
    /**
     * Extract bad words from text
     *
     * @param string|null $text
     * @return array
     */
    function extract_bad_words(?string $text): array
    {
        return \App\Services\BadWordsFilter::extract($text);
    }
}
