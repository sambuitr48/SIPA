<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ParkingLotController;
use App\Http\Controllers\Api\ReservationController;

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

    // Enviar correo de verificación
    Route::post('/auth/send-verification-email', [UserController::class, 'sendVerificationEmail']);

    /*
    |--------------------------------------------------------------------------
    | RUTAS QUE REQUIEREN EMAIL VERIFICADO
    |--------------------------------------------------------------------------
    */
    Route::middleware('verified')->group(function () {

        // Datos personales
        Route::get('/auth/me', [UserController::class, 'me']);
        Route::post('/auth/logout', [UserController::class, 'logout']);
        Route::post('/auth/logout-all', [UserController::class, 'logoutAll']);

        Route::patch('/auth/profile', [UserController::class, 'updateProfile']);
        Route::patch('/auth/change-password', [UserController::class, 'changePassword']);


        /*
        |--------------------------------------------------------------------------
        | RUTAS PARA HOST: MÓDULO DE PARQUEADEROS
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:host')->group(function () {

            Route::get('/host/dashboard', function () {
                return response()->json(['message' => 'Welcome host']);
            });

            // CRUD de parqueaderos
            Route::get('/parking-lots', [ParkingLotController::class, 'index']);
            Route::post('/parking-lots', [ParkingLotController::class, 'store']);
            Route::patch('/parking-lots/{parkingLot}', [ParkingLotController::class, 'update']);
            Route::delete('/parking-lots/{parkingLot}', [ParkingLotController::class, 'destroy']);
        });


        /*
        |--------------------------------------------------------------------------
        | RUTAS PARA DRIVERS: MÓDULO DE RESERVAS
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:driver')->group(function () {

            // Listar todas las reservas del driver
            Route::get('/reservations', [ReservationController::class, 'index']);

            // Crear reserva
            Route::post('/reservations', [ReservationController::class, 'store']);

            // Cancelar reserva
            Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);

            //Método de pago de reserva (pendiente de implementación)
            Route::post('/reservations/{reservation}/pay', [ReservationController::class, 'pay']);
        });


        /*
        |--------------------------------------------------------------------------
        | RUTAS ACCESIBLES POR CUALQUIER ROL (host o driver)
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:driver,host')->group(function () {
            Route::get('/profile', function () {
                return response()->json(['message' => 'Profile access allowed']);
            });
        });

    });
});
