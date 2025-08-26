<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Language switching route (not localized)
Route::post('/language/switch', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// GERMAN: Default language (no prefix)
Route::group(['middleware' => ['locale:de']], function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'meta' => [
                'title' => 'findemich - Lokale Geschäftspartner finden',
                'description' => 'Finden Sie vertrauenswürdige lokale Geschäftspartner in Ihrer Nähe. Verbinden Sie sich mit verifizierten Unternehmen, erkunden Sie interaktive Karten und entdecken Sie Qualitätsdienstleistungen in Ihrer Region.',
                'keywords' => 'Geschäftspartner, lokale Dienstleistungen, verifizierte Unternehmen, Business-Networking, findemich'
            ]
        ]);
    })->name('home');

    Route::get('/partners', [App\Http\Controllers\PartnerController::class, 'showPublic'])->name('partners.public.show');
    Route::get('/partners/{id}', [App\Http\Controllers\PartnerController::class, 'showPublic'])->name('partners.public.show.id');

    Route::prefix('dashboard')->middleware(['auth', 'verified', 'admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('map', [App\Http\Controllers\PartnerController::class, 'map'])->name('map');
        Route::resource('partners', App\Http\Controllers\PartnerController::class);
    });
});

// ENGLISH: Secondary language (with /en prefix)
Route::group(['prefix' => 'en', 'middleware' => ['locale:en']], function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'meta' => [
                'title' => 'findemich - Find Local Business Partners',
                'description' => 'Find trusted local business partners near you. Connect with verified companies, explore interactive maps, and discover quality services in your area.',
                'keywords' => 'business partners, local services, verified companies, business networking, findemich'
            ]
        ]);
    })->name('en.home');

    Route::get('/partners', [App\Http\Controllers\PartnerController::class, 'showPublic'])->name('en.partners.public.show');
    Route::get('/partners/{id}', [App\Http\Controllers\PartnerController::class, 'showPublic'])->name('en.partners.public.show.id');

    Route::prefix('dashboard')->middleware(['auth', 'verified', 'admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('en.dashboard');
        Route::get('map', [App\Http\Controllers\PartnerController::class, 'map'])->name('en.map');
        Route::resource('partners', App\Http\Controllers\PartnerController::class, ['as' => 'en']);
    });
});

// API routes (not localized)
Route::get('/api/partners', [App\Http\Controllers\PartnerController::class, 'getPublicPartners']);
Route::get('/api/search-location', [App\Http\Controllers\PartnerController::class, 'searchLocation']);
Route::get('/api/reverse-geocode', [App\Http\Controllers\PartnerController::class, 'reverseGeocode']);
Route::put('/api/user/location', [App\Http\Controllers\UserController::class, 'updateLocation'])->middleware('auth');

// Translation API routes
Route::post('/api/translate', [App\Http\Controllers\TranslationController::class, 'translate']);
Route::post('/api/is-german', [App\Http\Controllers\TranslationController::class, 'isGerman']);
Route::get('/api/translate/stats', [App\Http\Controllers\TranslationController::class, 'stats']);
Route::delete('/api/translate/cache', [App\Http\Controllers\TranslationController::class, 'clearCache']);

// Contact form routes (not localized)
Route::get('/api/contact/captcha', [App\Http\Controllers\ContactController::class, 'getCaptcha']);
Route::post('/api/contact', [App\Http\Controllers\ContactController::class, 'store']);

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
