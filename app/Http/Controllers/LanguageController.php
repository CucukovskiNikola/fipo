<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    /**
     * Switch the application's language.
     */
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        // Validate
        if (!in_array($locale, ['de', 'en'])) {
            Log::warning('LanguageController: Invalid locale requested', [
                'locale' => $locale,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return back()->withErrors(['locale' => 'Invalid language selected.']);
        }

        // Store in session and apply
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        Log::info('LanguageController: Language switched', [
            'old_locale' => Session::get('locale'),
            'new_locale' => $locale,
            'url' => $request->fullUrl(),
            'referer' => $request->headers->get('referer')
        ]);

        // Get current URL or default
        $referer = $request->headers->get('referer') ?? '/';

        $parsedUrl = parse_url($referer);
        $path = $parsedUrl['path'] ?? '/';

        // Remove old locale prefix
        $cleanPath = preg_replace('/^\/en/', '', $path);
        $cleanPath = '/' . ltrim($cleanPath, '/');

        // Add new prefix if switching to English
        $newPath = $locale === 'en' ? '/en' . $cleanPath : $cleanPath;
        
        Log::info('LanguageController: Redirecting', [
            'old_path' => $path,
            'clean_path' => $cleanPath,
            'new_path' => $newPath,
            'locale' => $locale
        ]);

        return redirect($newPath);
    }
}
