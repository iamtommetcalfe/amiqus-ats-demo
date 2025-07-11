<?php

use App\Http\Controllers\AmiqusOAuthController;
use Illuminate\Support\Facades\Route;

// Amiqus OAuth routes
Route::get('/amiqus/authorize', [AmiqusOAuthController::class, 'redirectToProvider'])->name('amiqus.authorize');
Route::get('/amiqus/callback', [AmiqusOAuthController::class, 'handleProviderCallback'])->name('amiqus.callback');

// SPA catch-all route
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!amiqus/(callback|authorize)).*$');
