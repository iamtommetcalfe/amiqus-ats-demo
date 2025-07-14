<?php

use App\Http\Controllers\AmiqusOAuthController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
        'status' => 'success',
    ]);
});

// Search route
Route::get('/search', [SearchController::class, 'search'])->name('search');

// Amiqus OAuth routes
use App\Http\Controllers\RequestTemplateController;

Route::prefix('amiqus')->group(function () {
    Route::get('/settings', [AmiqusOAuthController::class, 'settings'])->name('amiqus.settings');
    Route::post('/settings', [AmiqusOAuthController::class, 'storeCredentials'])->name('amiqus.store-credentials');
    Route::delete('/settings', [AmiqusOAuthController::class, 'deleteCredentials'])->name('amiqus.delete-credentials');
    Route::post('/refresh-token', [AmiqusOAuthController::class, 'refreshToken'])->name('amiqus.refresh-token');
    Route::post('/disconnect', [AmiqusOAuthController::class, 'disconnect'])->name('amiqus.disconnect');
    Route::get('/test-connection', [AmiqusOAuthController::class, 'testConnection'])->name('amiqus.test-connection');

    // Request Templates routes
    Route::get('/templates', [RequestTemplateController::class, 'index'])->name('amiqus.templates.index');
    Route::post('/templates/import', [RequestTemplateController::class, 'import'])->name('amiqus.templates.import');
});

// ATS routes
use App\Http\Controllers\AmiqusApiLogController;
use App\Http\Controllers\BackgroundCheckController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CheckWizardController;
use App\Http\Controllers\JobPostingController;

Route::prefix('ats')->group(function () {
    Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{id}', [JobPostingController::class, 'show'])->name('jobs.show');
    Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/{id}', [CandidateController::class, 'show'])->name('candidates.show');
    Route::post('/candidates/{id}/amiqus-client', [CandidateController::class, 'createAmiqusClient'])->name('candidates.create-amiqus-client');
    Route::patch('/candidates/{id}/amiqus-client', [CandidateController::class, 'updateAmiqusClient'])->name('candidates.update-amiqus-client');

    // Background Check routes
    Route::get('/background-checks', [BackgroundCheckController::class, 'all'])->name('background-checks.all');
    Route::get('/candidates/{candidateId}/background-checks', [BackgroundCheckController::class, 'index'])->name('background-checks.index');
    Route::post('/candidates/{candidateId}/background-checks', [BackgroundCheckController::class, 'store'])->name('background-checks.store');
    Route::post('/candidates/{candidateId}/background-checks/{id}/sync', [BackgroundCheckController::class, 'sync'])->name('background-checks.sync');

    // API Logs routes
    Route::get('/candidates/{candidateId}/api-logs', [AmiqusApiLogController::class, 'index'])->name('api-logs.index');

    // Check Wizard routes
    Route::get('/check-wizard/candidates', [CheckWizardController::class, 'getCandidates'])->name('check-wizard.candidates');
    Route::get('/check-wizard/templates', [CheckWizardController::class, 'getTemplates'])->name('check-wizard.templates');
    Route::post('/check-wizard/process', [CheckWizardController::class, 'process'])->name('check-wizard.process');
});
