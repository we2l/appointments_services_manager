<?php

use App\Http\Controllers\Api\ServicesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('services')->group(function () {
    Route::get('/', [ServicesController::class, 'index']);
    Route::get('/{idAppointment}', [ServicesController::class, 'show']);
    Route::post('/', [ServicesController::class, 'store']);
    Route::put('/{idAppointment}', [ServicesController::class, 'update']);
    Route::delete('/{idAppointment}', [ServicesController::class, 'delete']);
});
