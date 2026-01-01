<?php

use App\Http\Controllers\API\V1\System\Admin\PlanController;
use App\Http\Controllers\API\V1\System\Auth\AuthController;
use App\Http\Controllers\API\V1\System\Auth\PasswordController;
use App\Http\Controllers\API\V1\System\Auth\ProfileController;
use App\Http\Controllers\API\V1\System\Auth\UserPhoneController;
use App\Http\Controllers\API\V1\System\Auth\VerificationController;
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
        Route::put('update-profile', [ProfileController::class, 'updateProfile']);
        Route::get('/', [ProfileController::class, 'profile']);
        Route::put('update-username', [ProfileController::class, 'updateUsername']);
        Route::delete('delete-account', [ProfileController::class, 'deleteAccount']);
        Route::post('phone', [UserPhoneController::class, 'addPhone']);
        Route::delete('delete-phone', [UserPhoneController::class, 'deletePhone']);
        Route::post('verify-phone', [VerificationController::class, 'verifyPhone']);
    });

    Route::prefix('admin')->middleware(['role:super_admin,admin', 'auth:sanctum'])->group(function () {
        Route::apiResource('plans', PlanController::class);
        Route::patch('plans/{plan}', [PlanController::class, 'toggleActive']);
    });
});
