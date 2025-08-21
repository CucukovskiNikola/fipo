<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/language/switch', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// English routes (no prefix)
Route::group(['middleware' => ['locale:en']], function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'meta' => [
                'title' => 'findemich - Find Local Business Partners',
                'description' => 'Find trusted local business partners near you. Connect with verified companies, explore interactive maps, and discover quality services in your area.',
                'keywords' => 'business partners, local services, verified companies, business networking, findemich'
            ]
        ]);
    })->name('home');
});

// German routes (with /de prefix)
Route::group(['prefix' => 'de', 'middleware' => ['locale:de']], function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'meta' => [
                'title' => 'findemich - Lokale Geschäftspartner finden',
                'description' => 'Finden Sie vertrauenswürdige lokale Geschäftspartner in Ihrer Nähe. Verbinden Sie sich mit verifizierten Unternehmen, erkunden Sie interaktive Karten und entdecken Sie Qualitätsdienstleistungen in Ihrer Region.',
                'keywords' => 'Geschäftspartner, lokale Dienstleistungen, verifizierte Unternehmen, Business-Networking, findemich'
            ]
        ]);
    })->name('de.home');
});

// API routes (not localized)
Route::get('/api/partners', [App\Http\Controllers\PartnerController::class, 'getPublicPartners']);
Route::get('/api/search-location', [App\Http\Controllers\PartnerController::class, 'searchLocation']);
Route::get('/api/reverse-geocode', [App\Http\Controllers\PartnerController::class, 'reverseGeocode']);
Route::put('/api/user/location', [App\Http\Controllers\UserController::class, 'updateLocation'])->middleware('auth');

// Contact form routes (not localized)
Route::get('/api/contact/captcha', [App\Http\Controllers\ContactController::class, 'getCaptcha']);
Route::post('/api/contact', [App\Http\Controllers\ContactController::class, 'store']);

// Add routes to both language groups
Route::group(['middleware' => ['locale:en']], function () {
    // SEO-friendly query parameter route
    Route::get('/partners', [App\Http\Controllers\PartnerController::class, 'showPublic'])->name('partners.public.show');

    Route::prefix('dashboard')->middleware(['auth', 'verified', 'admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('map', [App\Http\Controllers\PartnerController::class, 'map'])->name('map');
        Route::resource('partners', App\Http\Controllers\PartnerController::class);
    });
});

Route::group(['prefix' => 'de', 'middleware' => ['locale:de']], function () {
    // SEO-friendly query parameter route
    Route::get('/partners', [App\Http\Controllers\PartnerController::class, 'showPublic'])->name('de.partners.public.show');

    Route::prefix('dashboard')->middleware(['auth', 'verified', 'admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('de.dashboard');
        Route::get('map', [App\Http\Controllers\PartnerController::class, 'map'])->name('de.map');
        Route::resource('partners', App\Http\Controllers\PartnerController::class, ['as' => 'de']);
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
