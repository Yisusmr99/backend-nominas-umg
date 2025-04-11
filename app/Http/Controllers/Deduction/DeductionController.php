<?php

namespace App\Http\Controllers\Deduction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Deduction\DeductionRequest;
use App\Models\Deduction;
use App\Helpers\ApiResponse;

class DeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deductions = Deduction::all();
        return ApiResponse::success($deductions, 'Deducciones obtenidas correctamente');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeductionRequest $requestDeduction)
    {
        try {
            $deduction = Deduction::create($requestDeduction->validated());
            return ApiResponse::success($deduction, 'Deducción creada correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(null, $th->getMessage(), 'Error al crear la deducción', 500);
        } 
    }

    /**
     * Display the specified resource.
     */
    public function show(Deduction $deduction)
    {
        return ApiResponse::success($deduction, 'Deducción obtenida correctamente');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeductionRequest $request, Deduction $deduction)
    {
        try {
            $deduction->update($request->validated());
            return ApiResponse::success($deduction, 'Deducción actualizada correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(null, $th->getMessage(), 'Error al actualizar la deducción', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deduction $deduction)
    {
        try {
            $deduction->delete();
            return ApiResponse::success(null, 'Deducción eliminada correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(null, $th->getMessage(), 'Error al eliminar la deducción', 500);
        }
    }
}
