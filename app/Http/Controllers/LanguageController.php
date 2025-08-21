<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
            return back()->withErrors(['locale' => 'Invalid language selected.']);
        }

        // Store in session and apply
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Get current URL or default
        $referer = $request->headers->get('referer') ?? '/';

        $parsedUrl = parse_url($referer);
        $path = $parsedUrl['path'] ?? '/';

        // Remove old locale prefix
        $cleanPath = preg_replace('/^\/en/', '', $path);
        $cleanPath = '/' . ltrim($cleanPath, '/');

        // Add new prefix if switching to English
        $newPath = $locale === 'en' ? '/en' . $cleanPath : $cleanPath;

        return redirect($newPath);
    }
}
