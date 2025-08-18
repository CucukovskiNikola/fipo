<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'meta' => [
            'title' => 'findemich - Find Local Business Partners',
            'description' => 'Find trusted local business partners near you. Connect with verified companies, explore interactive maps, and discover quality services in your area.',
            'keywords' => 'business partners, local services, verified companies, business networking, findemich'
        ]
    ]);
})->name('home');

Route::get('/api/partners', [App\Http\Controllers\PartnerController::class, 'getPublicPartners']);
Route::get('/api/search-location', [App\Http\Controllers\PartnerController::class, 'searchLocation']);
Route::get('/api/reverse-geocode', [App\Http\Controllers\PartnerController::class, 'reverseGeocode']);
Route::put('/api/user/location', [App\Http\Controllers\UserController::class, 'updateLocation'])->middleware('auth');


// SEO-friendly query parameter route
Route::get('/partners', [App\Http\Controllers\PartnerController::class, 'showPublic'])->name('partners.public.show');

Route::prefix('dashboard')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('map', [App\Http\Controllers\PartnerController::class, 'map'])->name('map');

    Route::resource('partners', App\Http\Controllers\PartnerController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
