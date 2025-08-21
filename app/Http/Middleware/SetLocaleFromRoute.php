<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $locale = null): Response
    {
        // If locale is passed as parameter, use it
        if ($locale && in_array($locale, ['en', 'de'])) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        } else {
            // Check if route starts with /de
            $segment = $request->segment(1);
            if ($segment === 'de') {
                app()->setLocale('de');
                session(['locale' => 'de']);
            } else {
                // Default to English for routes without prefix
                app()->setLocale('en');
                session(['locale' => 'en']);
            }
        }

        return $next($request);
    }
}
