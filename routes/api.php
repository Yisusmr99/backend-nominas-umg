<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Employee\EmployeeController;
require __DIR__ . '/auth.php';


Route::get('healthcheck', function () {
    return response()->json(['message' => 'API is running']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'all']);
    Route::put('/user/{user}', [UserController::class, 'update']);

    Route::put('/employee/{employee}', [EmployeeController::class, 'update']);
});