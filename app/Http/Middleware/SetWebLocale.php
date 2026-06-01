<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the UI language for server-rendered (Blade) pages. Preference order:
 * the authenticated user's saved language, then the session, then the default.
 */
class SetWebLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->preferred_language
            ?? $request->session()->get('locale')
            ?? config('app.locale', 'en');

        if (!in_array($locale, ['en', 'ar'], true)) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
