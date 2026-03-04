<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::middleware('api')->group(function () {
    // Authentication routes (no auth required)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/resend-verification', [AuthController::class, 'resendVerificationEmail']);
    Route::post('/auth/verify-email', [AuthController::class, 'verifyEmailWithToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Additional endpoints can be added here for mobile app
    // These will be separate from web routes completely
});
