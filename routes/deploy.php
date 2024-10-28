<?php

use App\Http\Controllers\AlertVolumeByMonthController;
use App\Http\Controllers\POEScreeningVolumeController;
use App\Http\Controllers\SymptomDiseaseAnalysisController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth'])->group(function () {

    Route::get('/SymptomDiseaseAnalysis', [SymptomDiseaseAnalysisController::class, 'SymptomDiseaseAnalysis'])->name('SymptomDiseaseAnalysis');

    Route::get('/AlertVolumeByMonth', [AlertVolumeByMonthController::class, 'AlertVolumeByMonth'])->name('AlertVolumeByMonth');

    Route::get('/monthlyScreeningVolumeByPOE', [POEScreeningVolumeController::class, 'monthlyScreeningVolumeByPOE'])->name('monthlyScreeningVolumeByPOE');

    Route::get('/HighRiskAlertByMonth', [AlertVolumeByMonthController::class, 'HighRiskAlertByMonth'])->name('HighRiskAlertByMonth');

});
