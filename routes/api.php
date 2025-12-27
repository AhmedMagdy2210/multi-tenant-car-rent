<?php

use App\Http\Controllers\API\V1\System\Auth\AuthController;
use App\Http\Controllers\API\V1\System\Auth\PasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// =========================== API V1 =================================
Route::prefix('v1')->group(function () {
    // ========== Auth routes =============
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('generate-username', [AuthController::class, 'generateUsername']);
        Route::post('forget-password/email', [PasswordController::class, 'sendOtpEmail']);
        Route::post('forget-password/phone', [PasswordController::class, 'sendOtpPhone']);
        Route::post('verify-otp', [PasswordController::class, 'verifyOtp']);
        Route::post('reset-password', [PasswordController::class, 'resetPassword']);
    })->middleware('throttle:5,1');
    Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
        Route::put('change-password', [PasswordController::class, 'changePassword']);
    });
});
