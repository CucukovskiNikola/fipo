<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('locale');
        
        // Validate locale
        if (!in_array($locale, ['de', 'en'])) {
            return back()->withErrors(['locale' => 'Invalid language selected.']);
        }
        
        // Set locale for current request
        App::setLocale($locale);
        
        // Store in session
        Session::put('locale', $locale);
        
        // Get current URL and redirect to localized version
        $currentUrl = $request->headers->get('referer');
        if (!$currentUrl) {
            // Fallback to home page if no referer
            $newPath = $locale === 'de' ? '/de/' : '/';
            return redirect($newPath);
        }
        
        $currentPath = parse_url($currentUrl, PHP_URL_PATH) ?? '/';
        
        // Remove any existing locale prefix
        $cleanPath = preg_replace('/^\/de/', '', $currentPath);
        // Ensure clean path starts with /
        $cleanPath = $cleanPath ?: '/';
        
        // Redirect based on selected locale
        if ($locale === 'de') {
            // Add /de prefix
            $newPath = '/de' . ($cleanPath === '/' ? '/' : $cleanPath);
        } else {
            // Use clean path (no prefix for English)
            $newPath = $cleanPath;
        }
        
        return redirect($newPath);
    }
}