<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\AlertDatabaseController;
use App\Http\Controllers\CasesByDistrict;
use App\Http\Controllers\CasesByPoe;
use App\Http\Controllers\CasesByProvince;
use App\Http\Controllers\CasesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GenderAndRouteAnalysis;
use App\Http\Controllers\PrimaryScreeningDashboard;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/getSuspectedCasesByDistrict', [CasesByDistrict::class, 'getSuspectedCasesByDistrict'])
        ->name('getSuspectedCasesByDistrict');

    Route::get('/getSuspectedCasesByPoe', [CasesByPoe::class, 'getSuspectedCasesByPoe'])->name('getSuspectedCasesByPoe');

    Route::get('/SelectAlertDatabaseDiseases', [AlertDatabaseController::class, 'SelectAlertDatabaseDiseases'])
        ->name('SelectAlertDatabaseDiseases');

    Route::get('/MainDashboard', [DashboardController::class, 'MainDashboard'])->name('MainDashboard');

    Route::get('/GenderAndRouteAnalysisDashboard', [GenderAndRouteAnalysis::class, 'GenderAndRouteAnalysisDashboard'])
        ->name('GenderAndRouteAnalysisDashboard');

    Route::any('/getSuspectedCasesByProvince', [CasesByProvince::class, 'getSuspectedCasesByProvince'])
        ->name('getSuspectedCasesByProvince');

    Route::any('/getPriorityDiseaseAlerts', [AlertDatabaseController::class, 'getPriorityDiseaseAlerts'])
        ->name('getPriorityDiseaseAlerts');

    Route::any('/AlertReport', [AlertController::class, 'AlertReport'])->name('AlertReport');

    Route::any('/casesReport', [CasesController::class, 'casesReport'])->name('CasesReport');

    Route::get('/', [DashboardController::class, 'MainDashboard'])->name('MainDashboard');

    Route::get('/dashboard', [DashboardController::class, 'MainDashboard'])->name('dashboard');
    //
    Route::any('/ScreeningVolumebyPOE', [PrimaryScreeningDashboard::class, 'ScreeningVolumebyPOE'])
        ->name('ScreeningVolumebyPOE');

});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/deploy.php';
