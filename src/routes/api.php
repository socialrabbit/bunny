<?php

use Illuminate\Support\Facades\Route;
use Bunny\Http\Controllers\ResumeController;

// Resume routes
Route::prefix('resume')->middleware('api')->group(function () {
    Route::get('/', [ResumeController::class, 'show']);
    Route::post('/', [ResumeController::class, 'store']);
    Route::get('/{resume}/download', [ResumeController::class, 'download']);
    Route::delete('/{resume}', [ResumeController::class, 'destroy']);
}); 