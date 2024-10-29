<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgeGenderAnalysisController;
use App\Http\Controllers\AlertVolumeByMonthController;
use App\Http\Controllers\ClassificationsController;
use App\Http\Controllers\POEController;
use App\Http\Controllers\POEScreeningVolumeController;
use App\Http\Controllers\SymptomDiseaseAnalysisController;
use App\Http\Controllers\TravelRouteAnalysisController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth'])->group(function () {

    Route::any('/index', [AdminController::class, 'index'])->name('index');

    Route::any('/DeleteAdmin/{id}', [AdminController::class, 'DeleteAdmin'])->name('DeleteAdmin');

    Route::any('/EditPOE', [POEController::class, 'EditPOE'])->name('EditPOE');

    Route::get('/MgtPoes', [POEController::class, 'MgtPoes'])->name('MgtPoes');

    Route::get('/GetClassificationData', [ClassificationsController::class, 'GetClassificationData'])->name('GetClassificationData');

    Route::get('/SelectClassification', [ClassificationsController::class, 'SelectClassification'])->name('SelectClassification');

    Route::get('/TravelRouteAnalysis', [TravelRouteAnalysisController::class, 'TravelRouteAnalysis'])->name('TravelRouteAnalysis');

    Route::get('/TravelRouteAnalysis', [TravelRouteAnalysisController::class, 'TravelRouteAnalysis'])->name('TravelRouteAnalysis');

    Route::get('/ageDistribution', [AgeGenderAnalysisController::class, 'ageDistribution'])->name('ageDistribution');

    Route::get('/AgeGenderAnalysis', [AgeGenderAnalysisController::class, 'AgeGenderAnalysis'])->name('AgeGenderAnalysis');

    Route::get('/SymptomDiseaseAnalysis', [SymptomDiseaseAnalysisController::class, 'SymptomDiseaseAnalysis'])->name('SymptomDiseaseAnalysis');

    Route::get('/AlertVolumeByMonth', [AlertVolumeByMonthController::class, 'AlertVolumeByMonth'])->name('AlertVolumeByMonth');

    Route::get('/monthlyScreeningVolumeByPOE', [POEScreeningVolumeController::class, 'monthlyScreeningVolumeByPOE'])->name('monthlyScreeningVolumeByPOE');

    Route::get('/HighRiskAlertByMonth', [AlertVolumeByMonthController::class, 'HighRiskAlertByMonth'])->name('HighRiskAlertByMonth');

});
