<?php

use App\Http\Controllers\MainReportsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ScreeningController;
use Illuminate\Support\Facades\Route;

// Define a route group with middleware for API and rate limiting
Route::middleware(['api', 'throttle:1000000,1'])->group(function () {
    // POE routes

    Route::get('/reports/by-province', [ReportsController::class, 'reportByProvince']);

    Route::get('/reports/by-district', [ReportsController::class, 'reportByDistrict']);

    // use App\Http\Controllers\ReportsController;

    Route::get('/reports/national-dashboard', [ReportsController::class, 'nationalDashboard']);

    Route::post('/screenings', [ScreeningController::class, 'storedata']);

    Route::get('/reports/{report_type}', [ReportsController::class, 'generateScreeningReport'])
        ->where('report_type', 'province|district|poe|screener');
});

// Access Reports
// Access Reports
// Access Reports

Route::get('/screening-summary', [MainReportsController::class, 'getScreeningSummary']);

Route::get('/screening-data', [MainReportsController::class, 'getScreeningData']);

Route::get('/getAllCases', [MainReportsController::class, 'getAllCases']);

Route::get('/getAllContacts', [MainReportsController::class, 'getAllContacts']);

Route::get('/TravellerRoutes', [MainReportsController::class, 'TravellerRoutes']);

// Access Reports
// Access Reports
// Access Reports
// Add a catch-all route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. If you believe this is an error, please contact support.',
    ], 404);
});
