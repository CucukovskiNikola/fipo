<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRoute
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $locale = null): Response
    {
        // Priority 1: Check if there's a locale in the session (user's preference)
        $sessionLocale = Session::get('locale');
        
        if ($sessionLocale && in_array($sessionLocale, ['en', 'de'])) {
            // Use the user's saved preference
            App::setLocale($sessionLocale);
            return $next($request);
        }
        
        // Priority 2: Use route parameter if provided
        if ($locale && in_array($locale, ['en', 'de'])) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            return $next($request);
        }
        
        // Priority 3: Check URL segment for explicit locale
        $segment = $request->segment(1);

        if ($segment === 'en') {
            App::setLocale('en');
            Session::put('locale', 'en');
            return $next($request);
        }
        
        // Priority 4: Check if user has a stored preference (e.g., in database)
        if (auth()->check()) {
            $userLocale = auth()->user()->locale ?? null;
            if ($userLocale && in_array($userLocale, ['en', 'de'])) {
                App::setLocale($userLocale);
                Session::put('locale', $userLocale);
                return $next($request);
            }
        }
        
        // Priority 5: Check Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $preferredLocale = $this->parseAcceptLanguage($acceptLanguage);
            if ($preferredLocale && in_array($preferredLocale, ['en', 'de'])) {
                App::setLocale($preferredLocale);
                Session::put('locale', $preferredLocale);
                return $next($request);
            }
        }
        
        // Priority 6: Default fallback to German
        App::setLocale('de');
        Session::put('locale', 'de');

        return $next($request);
    }
    
    /**
     * Parse Accept-Language header to find preferred locale
     */
    private function parseAcceptLanguage(string $acceptLanguage): ?string
    {
        $languages = explode(',', $acceptLanguage);
        
        foreach ($languages as $language) {
            $parts = explode(';', trim($language));
            $locale = strtolower(trim($parts[0]));
            
            // Extract language code (e.g., 'en' from 'en-US')
            $langCode = substr($locale, 0, 2);
            
            if (in_array($langCode, ['en', 'de'])) {
                return $langCode;
            }
        }
        
        return null;
    }
}
