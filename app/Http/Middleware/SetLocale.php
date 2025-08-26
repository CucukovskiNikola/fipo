<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Priority 1: Check if there's a locale in the session (user's preference)
        $sessionLocale = Session::get('locale');
        
        if ($sessionLocale && in_array($sessionLocale, ['en', 'de'])) {
            // Use the user's saved preference
            App::setLocale($sessionLocale);
            Log::info('SetLocale: Using session locale', [
                'locale' => $sessionLocale,
                'url' => $request->fullUrl(),
                'path' => $request->path()
            ]);
            return $next($request);
        }
        
        // Priority 2: Check URL segment for explicit locale
        $segment = $request->segment(1);
        
        if ($segment === 'en') {
            // If URL starts with /en, set English
            App::setLocale('en');
            Session::put('locale', 'en');
            Log::info('SetLocale: Using URL segment locale', [
                'locale' => 'en',
                'url' => $request->fullUrl(),
                'path' => $request->path()
            ]);
            return $next($request);
        }
        
        // Priority 3: Check if user has a stored preference (e.g., in database)
        if (auth()->check()) {
            $userLocale = auth()->user()->locale ?? null;
            if ($userLocale && in_array($userLocale, ['en', 'de'])) {
                App::setLocale($userLocale);
                Session::put('locale', $userLocale);
                Log::info('SetLocale: Using user database locale', [
                    'locale' => $userLocale,
                    'url' => $request->fullUrl(),
                    'path' => $request->path()
                ]);
                return $next($request);
            }
        }
        
        // Priority 4: Check Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $preferredLocale = $this->parseAcceptLanguage($acceptLanguage);
            if ($preferredLocale && in_array($preferredLocale, ['en', 'de'])) {
                App::setLocale($preferredLocale);
                Session::put('locale', $preferredLocale);
                Log::info('SetLocale: Using Accept-Language header locale', [
                    'locale' => $preferredLocale,
                    'url' => $request->fullUrl(),
                    'path' => $request->path()
                ]);
                return $next($request);
            }
        }
        
        // Priority 5: Default fallback to German
        App::setLocale('de');
        Session::put('locale', 'de');
        Log::info('SetLocale: Using default locale', [
            'locale' => 'de',
            'url' => $request->fullUrl(),
            'path' => $request->path()
        ]);
        
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
