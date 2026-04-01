<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\ClientDashboard\ReservationController;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [ReservationController::class, 'handle']);


Route::post('/login',  [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/rooms',   [RoomController::class, 'index']);
});
