<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\PayrollType\PayrollTypeController;
use App\Http\Controllers\ContractType\ContractTypeController;
use App\Http\Controllers\Deduction\DeductionController;
use App\Http\Controllers\Bonus\BonusController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\PerformanceEvaluation\PerformanceEvaluationController;
use App\Http\Controllers\Vacation\VacationController;

require __DIR__ . '/auth.php';

use App\Models\Rol;
use App\Helpers\ApiResponse;


Route::get('healthcheck', function () {
    return response()->json(['message' => 'API is running']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'all']);
    Route::put('/user/{user}', [UserController::class, 'update']);
    Route::put('/user/low/{user}', [UserController::class, 'low']);

    Route::put('/employee/{employee}', [EmployeeController::class, 'update']);
    Route::get('/employee', [EmployeeController::class, 'index']);

    Route::resource('payroll_type', PayrollTypeController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::resource('contract_type', ContractTypeController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::resource('deduction', DeductionController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
        
    Route::resource('bonus', BonusController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::get('roles', function () {
        return ApiResponse::success(
            Rol::all(),
            'Usuarios obtenidos exitosamente'
        );
    });

    Route::resource('payroll', PayrollController::class)
    ->only(['index', 'store']);
    Route::post('payroll/pay', [PayrollController::class, 'pay']);
    Route::get('payroll/employee/{employeeId}', [PayrollController::class, 'showByEmployee']);
    

    Route::get('report/payroll', [ReportsController::class, 'index']);
    Route::resource('performance-evaluation', PerformanceEvaluationController::class)
        ->only(['index', 'store']);

    Route::resource('vacation', VacationController::class)
    ->only(['index', 'store']);
    Route::get('vacation/employee/{employeeId}', [VacationController::class, 'getApplicationsByEmployee']);
    Route::put('vacation/approve/{id}', [VacationController::class, 'approveRequest']);
    Route::post('vacation/add-vacation', [VacationController::class, 'addVacationBalanceAllEmployees']);
    Route::get('vacation/balance/{employeeId}', [VacationController::class, 'getVacationBalance']);
    Route::put('vacation/decline/{id}', [VacationController::class, 'declineRequest']);
});

Route::get('/reports/export', [ReportsController::class, 'exportExcel']);