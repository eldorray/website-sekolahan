<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** Locales the public site may render in. */
    private const SUPPORTED = ['id', 'en'];

    /**
     * Apply the visitor's chosen locale (session) to the app for this request.
     * Runs on full page loads and Livewire AJAX updates. Invalid/absent →
     * config default. Never reads locale from request input.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
