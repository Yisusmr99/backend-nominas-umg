<?php

namespace App\Http\Controllers\Bonus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Bonus\BonusRequest;
use App\Models\Bonu;
use App\Helpers\ApiResponse;

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bonuses = Bonu::all();
        return ApiResponse::success($bonuses, 'Bonuses retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BonusRequest $bonusRequest)
    {
        try {
            $bonus = Bonu::create($bonusRequest->validated());
            return ApiResponse::success($bonus, 'Bonus created successfully', 201);
        } catch (\Throwable $th) {
            return ApiResponse::error(null, 'Error creating bonus: '. $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bonu $bonus)
    {
        return ApiResponse::success($bonus, 'Bonus retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BonusRequest $bonusRequest, $id)
    {
        try {
            $bonus = Bonu::findOrFail($id);
            if (!$bonus) {
                return ApiResponse::error(null, 'Bonus not found', 404);
            }
            $bonus->update($bonusRequest->validated());
            return ApiResponse::success($bonus, 'Bonus updated successfully');
        } catch (\Throwable $th) {
            return ApiResponse::error(null, 'Error updating bonus: '. $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bonus = Bonu::find($id);
        if (!$bonus) {
            return ApiResponse::error(null, 'Bonus not found', 404);
        }
        try {
            $bonus->delete();
            return ApiResponse::success(null, 'Bonus deleted successfully');
        } catch (\Throwable $th) {
            return ApiResponse::error(null, 'Error deleting bonus: '. $th->getMessage(), 500);
        }
        
    }
}
