<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/api/partners', [App\Http\Controllers\PartnerController::class, 'getPublicPartners']);
Route::get('/api/search-location', [App\Http\Controllers\PartnerController::class, 'searchLocation']);
Route::get('/api/reverse-geocode', [App\Http\Controllers\PartnerController::class, 'reverseGeocode']);
Route::put('/api/user/location', [App\Http\Controllers\UserController::class, 'updateLocation'])->middleware('auth');

Route::prefix('dashboard')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('map', [App\Http\Controllers\PartnerController::class, 'map'])->name('map');

    Route::resource('partners', App\Http\Controllers\PartnerController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
