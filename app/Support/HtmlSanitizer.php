<?php

namespace App\Support;

use Mews\Purifier\Facades\Purifier;

class HtmlSanitizer
{
    /**
     * Sanitize rich HTML (Tiptap editor / AI output) before persisting it.
     *
     * Uses the tight "school_content" HTMLPurifier profile so stored content
     * can never carry <script>, inline event handlers, or javascript: URLs.
     */
    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        return Purifier::clean($html, 'school_content');
    }
}
