<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Ruta pública para login
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('api')
    ->name('login');

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register');
        
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

});
