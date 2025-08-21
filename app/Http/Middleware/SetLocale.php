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
        $segment = $request->segment(1);

        if ($segment === 'en') {
            // If URL starts with /en, set English
            App::setLocale('en');
            Session::put('locale', 'en');
        } else {
            // Default to German for root or any non-en-prefixed URL
            App::setLocale('de');
            Session::put('locale', 'de');
        }

        return $next($request);
    }
}
