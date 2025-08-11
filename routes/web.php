<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

Route::get('map-demo', function () {
    return Inertia::render('MapDemo');
})->name('map-demo');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
