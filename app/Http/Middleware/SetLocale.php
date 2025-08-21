<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // First priority: Check URL for locale prefix
        $segment = $request->segment(1);
        if ($segment === 'de') {
            App::setLocale('de');
            Session::put('locale', 'de');
        } else {
            // For routes without /de prefix, use English
            App::setLocale('en');
            Session::put('locale', 'en');
        }
        
        return $next($request);
    }
}