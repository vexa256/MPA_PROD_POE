<?php

use App\Http\Controllers\MainReportsController;
use App\Http\Controllers\PrimaryScreeningController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReportsSecondaryScreeningController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\SecondaryScreeningController;
use App\Http\Controllers\SecondaryScreeningDataCapture;
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

Route::post('/secondary-screenings', [SecondaryScreeningController::class, 'store']);

Route::post('/referrals', [SecondaryScreeningController::class, 'getReferrals']);
Route::delete('/referrals', [SecondaryScreeningController::class, 'cancelReferral']);

Route::post('/record-new-screening', [SecondaryScreeningDataCapture::class, 'recordNewScreening']);
Route::post('/fetch-all-screenings', [SecondaryScreeningController::class, 'fetchAllScreenings']);
Route::post('/retrieve-screening-details/{id}', [SecondaryScreeningController::class, 'retrieveScreeningDetails']);
Route::put('/modify-existing-screening/{id}', [SecondaryScreeningController::class, 'modifyExistingScreening']);
Route::delete('/remove-screening-record/{id}', [SecondaryScreeningController::class, 'removeScreeningRecord']);

Route::get('/dashboard-data', [ReportsSecondaryScreeningController::class, 'getDashboardData']);
Route::get('/primary-screenings', [PrimaryScreeningController::class, 'getScreenings']);

// Route::get('reports/screening-classification-summary', [ReportsSecondaryScreeningController::class, 'screeningClassificationSummary'])
//     ->name('reports.screening-classification-summary');

// Access Reports
// Access Reports
// Access Reports
// Add a catch-all route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. If you believe this is an error, please contact support.',
    ], 404);
});