<?php

namespace App\Http\Controllers\PayrollType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PayrollType\PayrollTypeRequest;
use App\Models\PayrollType;
use App\Helpers\ApiResponse;

class PayrollTypeController extends Controller
{
    public function index()
    {
        $payrollTypes = PayrollType::all();
        return ApiResponse::success($payrollTypes, 'Lista de tipos de nómina');
    }

    public function store(PayrollTypeRequest $request)
    {
        try {
            $payrollType = PayrollType::create($request->validated());
            return ApiResponse::success($payrollType, 'Tipo de nómina creado correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(
                null, 'Error al crear el tipo de nómina: ' . $th->getMessage(), 500
            );
        }
    }

    public function show(PayrollType $payroll_type)
    {
        return ApiResponse::success($payroll_type, 'Tipo de nómina encontrado');
    }

    public function update(PayrollTypeRequest $request, PayrollType $payroll_type)
    {
        try {
            $payroll_type->update($request->validated());
            return ApiResponse::success($payroll_type, 'Tipo de nómina actualizado correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(
                null, 'Error al actualizar el tipo de nómina: ' . $th->getMessage(), 500
            );
        }
    }

    public function destroy(PayrollType $payroll_type)
    {
        try {
            $payroll_type->delete();
            return ApiResponse::success(null, 'Tipo de nómina eliminado correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(
                null, 'Error al eliminar el tipo de nómina: ' . $th->getMessage(), 500
            );
        }
    }
}
