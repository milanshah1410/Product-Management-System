<?php

namespace App\Helpers;

class XssProtection
{
    /**
     * Sanitize HTML content for safe display.
     */
    public static function sanitize(string $html): string
    {
        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><blockquote><code><pre>';
        
        $cleaned = strip_tags($html, $allowedTags);
        
        // Additional cleaning for attributes
        $cleaned = preg_replace('/<a[^>]+href=["\']javascript:/i', '<a href="', $cleaned);
        
        return $cleaned;
    }

    /**
     * Escape output for safe display (Blade does this automatically with {{ }}).
     */
    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}