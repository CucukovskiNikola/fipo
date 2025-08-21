<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRoute
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $locale = null): Response
    {
        // Use route parameter or infer from URL segment
        if ($locale && in_array($locale, ['en', 'de'])) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        } else {
            $segment = $request->segment(1);

            if ($segment === 'en') {
                app()->setLocale('en');
                session(['locale' => 'en']);
            } else {
                app()->setLocale('de'); // Default
                session(['locale' => 'de']);
            }
        }

        return $next($request);
    }
}
