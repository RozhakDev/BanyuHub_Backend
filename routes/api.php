<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\RegistrationController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::get('/events/{event}/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'index']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/register-event', [RegistrationController::class, 'store']);
    Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations']);
    
    Route::post('/events/{event}/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'store']);
});
