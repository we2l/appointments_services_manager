<?php

use App\Http\Controllers\Api\AppointmentsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('appointments')->group(function () {
    Route::get('/', [AppointmentsController::class, 'index']);
    Route::get('/{idAppointment}', [AppointmentsController::class, 'show']);
    Route::post('/', [AppointmentsController::class, 'store']);
    Route::put('/{idAppointment}', [AppointmentsController::class, 'update']);
    Route::patch('/cancel/{idAppointment}', [AppointmentsController::class, 'cancel']);
});
