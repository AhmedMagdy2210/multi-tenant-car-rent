<?php

use App\Http\Controllers\API\V1\System\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// =========================== API V1 =================================
Route::prefix('v1')->group(function () {
    // ========== Auth routes =============
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/generate-username', [AuthController::class, 'generateUsername']);

});
