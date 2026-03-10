<?php

namespace App\Support;

class HtmlSanitizer
{
    public static function sanitizeLimitedHtml(?string $html): string
    {
        if (! is_string($html) || $html === '') {
            return '';
        }

        $sanitized = preg_replace('#<\s*(script|style)[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? '';
        $sanitized = strip_tags($sanitized, '<p><br><b><i><ul><ol><li>');

        return preg_replace('/<(p|br|b|i|ul|ol|li)\b[^>]*>/i', '<$1>', $sanitized) ?? '';
    }
}

