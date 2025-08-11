<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::prefix('dashboard')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('map-demo', function () {
        return Inertia::render('MapDemo');
    })->name('map-demo');

    Route::resource('partners', App\Http\Controllers\PartnerController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
