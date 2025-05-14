<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;
use App\Http\Requests\Employee\EditRequest;


class EmployeeController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            $employees = Employee::with("user")->get();
            return ApiResponse::success(
                $employees,
                'Lista de empleados obtenida exitosamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                null,
                'Error al obtener empleados: ' . $e->getMessage(),
                500
            );
        }
    }

    public function update(EditRequest $request, Employee $employee): JsonResponse
    {
        try {
            $employee->update($request->all());
            return ApiResponse::success(
                $employee,
                'Empleado actualizado exitosamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                null,
                'Error al actualizar empleado: ' . $e->getMessage(),
                500
            );
        }
    }
}
