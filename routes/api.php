<?php

use App\Http\Controllers\PoeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\SuspectedCaseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Define a route group with middleware for API and rate limiting
Route::middleware(['api', 'throttle:1000000,1'])->group(function () {
    // POE routes
    Route::prefix('poes')->group(function () {
        Route::get('/', [PoeController::class, 'index']);
        Route::post('/', [PoeController::class, 'store']);
        Route::get('/{id}', [PoeController::class, 'show']);
        Route::put('/{id}', [PoeController::class, 'update']);
        Route::delete('/{id}', [PoeController::class, 'destroy']);
    });

    Route::post('/login', [UserController::class, 'appLogin']);

    Route::post('/screenings', [ScreeningController::class, 'store']);
    Route::get('/screenings', [ScreeningController::class, 'index']);
    Route::get('/screenings/{id}', [ScreeningController::class, 'show']);
    Route::put('/screenings/{id}', [ScreeningController::class, 'update']);
    Route::delete('/screenings/{id}', [ScreeningController::class, 'destroy']);
    Route::get('/screenings/poe/{poe}', [ScreeningController::class, 'getByPOE']);
    Route::get('/screenings/date-range', [ScreeningController::class, 'getByDateRange']);

    Route::prefix('reports')->group(function () {
        Route::get('/daily-summary', [ReportController::class, 'dailyScreeningSummary']);
        Route::get('/top-diseases', [ReportController::class, 'topSuspectedDiseases']);
        Route::get('/symptom-frequency', [ReportController::class, 'symptomFrequencyAnalysis']);
        Route::get('/officer-performance', [ReportController::class, 'screeningOfficerPerformance']);
        Route::get('/travel-routes', [ReportController::class, 'travelRouteAnalysis']);
        Route::get('/age-group-risk', [ReportController::class, 'ageGroupRiskAnalysis']);
        Route::get('/poe-workload', [ReportController::class, 'poeWorkloadAnalysis']);
        Route::get('/risk-factor-analysis', [ReportController::class, 'riskFactorAnalysis']);
        Route::get('/gender-analysis', [ReportController::class, 'genderAnalysis']);
    });

    Route::get('/suspected-cases', [SuspectedCaseController::class, 'index']);
    Route::get('/poes-with-cases', [SuspectedCaseController::class, 'poesWithCases']);

    Route::get('/traveler-origin-risk-assessment', [SuspectedCaseController::class, 'travelerOriginRiskAssessment']);

    Route::get('/demographic-analysis', [SuspectedCaseController::class, 'demographicAnalysis']);

    Route::get('/ageGenderDistribution', [SuspectedCaseController::class, 'ageGenderDistribution']);

    Route::get('/screening-officer-performance', [SuspectedCaseController::class, 'screeningOfficerPerformance']);

    // // ex

    // Route::post('/screenings', [ScreeningController::class, 'store']);
    // Route::post('/screenings/check-sync-status', [ScreeningController::class, 'checkSyncStatus']);
    // Route::get('/screenings/unsynced', [ScreeningController::class, 'getUnsyncedRecords']);
    // Route::post('/screenings/bulk-sync', [ScreeningController::class, 'bulkSync']);
    // Route::get('/screenings/{screeningId}', [ScreeningController::class, 'getScreening']);
    // Route::put('/screenings/{screeningId}', [ScreeningController::class, 'updateScreening']);
    // Route::delete('/screenings/{screeningId}', [ScreeningController::class, 'deleteScreening']);
    // Route::get('/screenings/search', [ScreeningController::class, 'searchScreenings']);
    // Route::get('/screenings/statistics', [ScreeningController::class, 'getStatistics']);

    // // ex

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/{id}/last-login', [UserController::class, 'updateLastLogin']);
        Route::get('/poe/{poeId}', [UserController::class, 'getUsersByPOE']);

    });

    // // User authentication routes (assuming you might need these in the future)
    // Route::post('/login', [AuthController::class, 'login']);
    // Route::post('/register', [AuthController::class, 'register']);
    // Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    // Route::get('/user', [AuthController::class, 'user'])->middleware('auth:api');

    // Add any other API routes here
    // For example:
    // Route::apiResource('users', UserController::class);
    // Route::apiResource('roles', RoleController::class);
});

// Add a catch-all route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. If you believe this is an error, please contact support.',
    ], 404);
});

require __DIR__ . '/reports.php';
