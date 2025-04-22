<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\PayrollType\PayrollTypeController;
use App\Http\Controllers\ContractType\ContractTypeController;
use App\Http\Controllers\Deduction\DeductionController;
use App\Http\Controllers\Bonus\BonusController;
require __DIR__ . '/auth.php';


Route::get('healthcheck', function () {
    return response()->json(['message' => 'API is running']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'all']);
    Route::put('/user/{user}', [UserController::class, 'update']);

    Route::put('/employee/{employee}', [EmployeeController::class, 'update']);

    Route::resource('payroll_type', PayrollTypeController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::resource('contract_type', ContractTypeController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::resource('deduction', DeductionController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
        
    Route::resource('bonus', BonusController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
});