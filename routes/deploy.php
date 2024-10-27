<?php

use App\Http\Controllers\POEScreeningVolumeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth'])->group(function () {

    Route::get('/monthlyScreeningVolumeByPOE', [POEScreeningVolumeController::class, 'monthlyScreeningVolumeByPOE'])
        ->name('monthlyScreeningVolumeByPOE');

});
