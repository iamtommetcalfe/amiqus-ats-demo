<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmiqusOAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Example API route
Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello from Laravel API!',
        'status' => 'success'
    ]);
});

// Amiqus OAuth routes
Route::prefix('amiqus')->group(function () {
    Route::get('/settings', [AmiqusOAuthController::class, 'settings'])->name('amiqus.settings');
    Route::post('/settings', [AmiqusOAuthController::class, 'storeCredentials'])->name('amiqus.store-credentials');
    Route::delete('/settings', [AmiqusOAuthController::class, 'deleteCredentials'])->name('amiqus.delete-credentials');
    Route::post('/refresh-token', [AmiqusOAuthController::class, 'refreshToken'])->name('amiqus.refresh-token');
    Route::post('/disconnect', [AmiqusOAuthController::class, 'disconnect'])->name('amiqus.disconnect');
    Route::get('/test-connection', [AmiqusOAuthController::class, 'testConnection'])->name('amiqus.test-connection');
});

// ATS routes
use App\Http\Controllers\JobPostingController;

Route::prefix('ats')->group(function () {
    Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{id}', [JobPostingController::class, 'show'])->name('jobs.show');
});
