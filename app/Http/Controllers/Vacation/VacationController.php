<?php

namespace App\Http\Controllers\Vacation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacation;
use App\Models\VacationBalance;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;


class VacationController extends Controller
{
    public function index(){
        $vacations = Vacation::with('employee.user')->get();
        return ApiResponse::success($vacations, 'Solicitudes de vacaciones obtenidas correctamente');
    }

    public function getApplicationsByEmployee($employeeId){
        $vacations = Vacation::where('employee_id', $employeeId)->with('employee.user')->get();
        return ApiResponse::success($vacations, 'Solicitudes de vacaciones obtenidas correctamente');
    }

    public function store(Request $request){
        try {
            $vacation = Vacation::create($request->all());
            return ApiResponse::success($vacation, 'Solicitud de vacaciones creada correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(null, 'Error al crear la solicitud de vacaciones', 500);
        }
    }

    public function approveRequest(Request $request, $id){
        try {
            DB::beginTransaction();
            $vacation = Vacation::findOrFail($id);
            $vacation->update(['status' => '2']);
            $this->updateVacationBalance($vacation->employee_id, $vacation->total_days);
            DB::commit();
            return ApiResponse::success($vacation, 'Solicitud de vacaciones aprobada correctamente');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ApiResponse::error(null, 'Error al aprobar la solicitud de vacaciones', 500);
        }
    }

    private function updateVacationBalance($employeeId, $days){
        $vacationBalance = VacationBalance::where('employee_id', $employeeId)->first();
        $vacationBalance->update([
            'used_days' => $vacationBalance->used_days + $days,
            'available_days' => $vacationBalance->available_days - $days,
        ]);
    }

    public function addVacationBalanceAllEmployees($days = 1.25){
        try {
            DB::beginTransaction();
            $employees = Employee::all();
            foreach ($employees as $employee) {
                $this->addVacationBalance($employee->id, $days);
            }
            DB::commit();
            return ApiResponse::success(null, 'Días de vacaciones agregados a todos los empleados correctamente');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ApiResponse::error(null, 'Error al agregar días de vacaciones a todos los empleados', 500);
        }
    }

    private function addVacationBalance($employeeId, $days){
        $vacationBalance = VacationBalance::where('employee_id', $employeeId)->first();
        if ($vacationBalance) {
            $vacationBalance->update([
                'accrued_days' => $vacationBalance->accrued_days + $days,
                'available_days' => $vacationBalance->available_days + $days,
            ]);
        } else {
            VacationBalance::create([
                'employee_id' => $employeeId,
                'accrued_days' => $days,
                'used_days' => 0,
                'available_days' => $days,
            ]);
        }
    }

    public function getVacationBalance($employeeId){
        $vacationBalance = VacationBalance::where('employee_id', $employeeId)->first();
        if ($vacationBalance) {
            return ApiResponse::success($vacationBalance, 'Saldo de vacaciones obtenido correctamente');
        } else {
            return ApiResponse::error(null, 'No se encontró el saldo de vacaciones para este empleado', 404);
        }
    }

    public function declineRequest($id){
        try {
            $vacation = Vacation::findOrFail($id);
            $vacation->update(['status' => '3']);
            return ApiResponse::success($vacation, 'Solicitud de vacaciones denegada correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(null, 'Error al denegar la solicitud de vacaciones', 500);
        }
    }
}