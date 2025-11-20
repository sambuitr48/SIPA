<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| AUTH PÚBLICO (Sin token)
|--------------------------------------------------------------------------
*/

Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);

Route::post('/auth/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [UserController::class, 'resetPassword']);
Route::post('/auth/verify-email', [UserController::class, 'verifyEmail']);


/*
|--------------------------------------------------------------------------
| AUTH PRIVADO (Requiere token Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/send-verification-email', [UserController::class, 'sendVerificationEmail']);

    // 🔒 TODO lo que esté aquí dentro pasa por EnsureEmailIsVerified
    Route::middleware('verified')->group(function () {

        Route::get('/auth/me', [UserController::class, 'me']);
        Route::post('/auth/logout', [UserController::class, 'logout']);
        Route::post('/auth/logout-all', [UserController::class, 'logoutAll']);

        Route::patch('/auth/profile', [UserController::class, 'updateProfile']);
        Route::patch('/auth/change-password', [UserController::class, 'changePassword']);

        Route::middleware('role:host')->group(function () {
            Route::get('/host/dashboard', function () {
                return response()->json(['message' => 'Welcome host']);
            });
        });

        Route::middleware('role:driver,host')->group(function () {
            Route::get('/profile', function () {
                return response()->json(['message' => 'Profile access allowed']);
            });
        });
    });
});
