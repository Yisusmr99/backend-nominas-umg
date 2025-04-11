<?php

namespace App\Http\Controllers\ContractType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContractType;
use App\Helpers\ApiResponse;
use App\Http\Requests\ContractType\ContractTypeRequest;

class ContractTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contractTypes = ContractType::all();
        return ApiResponse::success($contractTypes, 'Lista de tipos de contrato');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractTypeRequest $request)
    {
        try {
            $contractType = ContractType::create($request->validated());
            return ApiResponse::success($contractType, 'Tipo de contrato creado');
        } catch (\Throwable $th) {
            return ApiResponse::error(
                null, 
                'Error al crear el tipo de contrato: '. $th->getMessage(), 
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractType $contract_type)
    {
        return ApiResponse::success($contract_type, 'Tipo de contrato encontrado');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ContractTypeRequest $request, ContractType $contractType)
    {
        try {
            // $contractType = ContractType::findOrFail($id);
            $contractType->update($request->validated());
            return ApiResponse::success($contractType, 'Tipo de contrato actualizado');
        } catch (\Throwable $th) {
            return ApiResponse::error(
                null, 
                'Error al actualizar el tipo de contrato: '. $th->getMessage(), 
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractType $contract_type)
    {
        try {
            $contract_type->delete();
            return ApiResponse::success(null, 'Tipo de contrato eliminado');
        } catch (\Throwable $th) {
            return ApiResponse::error(
                null, 
                'Error al eliminar el tipo de contrato: '. $th->getMessage(), 
                500
            );
        }
    }
}
