<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;

// Public routes (tanpa token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/venues', [VenueController::class, 'index']);
Route::get('/venues/{id}', [VenueController::class, 'show']);
Route::get('/venues/{id}/slots', [VenueController::class, 'slots']);
Route::get('/promos', [PromoController::class, 'index']);

// Protected routes (butuh token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'show']);
    Route::patch('/user', [UserController::class, 'update']);
    Route::post('/user/photo', [UserController::class, 'updatePhoto']);
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/bookings/{id}/rating', [BookingController::class, 'rating']);
});
