<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Http\Requests\User\EditRequest;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;
use App\Models\Payroll;
use Carbon\Carbon;
use App\Models\Deduction;
use Illuminate\Support\Str;
use App\Models\PayrollDeduction;
use App\Models\PayrollBonu;
use Illuminate\Support\Facades\DB;
use App\Models\Bonu;

class UserController extends Controller
{

    public function all(): JsonResponse
    {
        try {
            $users = User::with('employee', 'role')->get();
            return ApiResponse::success(
                $users,
                'Usuarios obtenidos exitosamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                null,
                'Error al obtener usuarios: ' . $e->getMessage(),
                500
            );
        }
    }

    public function update(EditRequest $request, User $user): JsonResponse
    {
        try {
            $user->update($request->all());
            return ApiResponse::success(
                $user->load('employee', 'role'),
                'Usuario actualizado exitosamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                null,
                'Error al actualizar usuario: ' . $e->getMessage(),
                500
            );
        } 
    }

    public function low(Request $request, User $user): JsonResponse
    {
        try {
            DB::beginTransaction();

            $end_date = now();
            $user->update(['is_active' => false]);
            $user->employee()->update(['is_active' => false]);
            $user->employee()->update(['termination_date' => now()]);

            $employee = $user->employee;
            //--------------------------------------------------------------------------
            // obtener vacaciones pendientes del empleado
            $pending_vacations = $this->getPendingVacations($employee);
            //--------------------------------------------------------------------------
            // obtener el bono 14 pendiente del empleado
            $pending_bonus_14 = $this->getPendingBonus($employee, $end_date);
            //--------------------------------------------------------------------------
            // obtener el aguinaldo pendiente del empleado
            $pending_aguinaldo = $this->getPendingAguinaldo($employee, $end_date);
            //--------------------------------------------------------------------------
            // obtener salario pendiente del empleado
            $pending_salary = $this->getPendigSalary(
                $employee, $end_date, $pending_aguinaldo, $pending_bonus_14,
                $pending_vacations
            );
            //--------------------------------------------------------------------------

            DB::commit();
            return ApiResponse::success(
                $user->load('employee', 'role'),
                'Usuario dado de baja exitosamente'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                null,
                'Error al dar de baja usuario: ' . $e->getMessage(),
                500
            );
        }
    }

    private function getPendigSalary($employee, $end_date, $pending_aguinaldo, $pending_bonus_14, $pending_vacations){
        // obtener el ultimo salario del empleado para saber si necesitamos pagarle los dias faltantes laborados
        $last_payment = Payroll::where('employee_id', $employee->id)
            ->where('payroll_type_id', 1)
            ->orderBy('period_end', 'desc')
            ->first();
            
        $days_difference = 0;
        $pending_salary = 0;
        $iggsDiscountAmount = 0;
        $isrDiscountAmount = 0;
        $deductionIgss = null;
        $deductionIsr = null;

        // hacemos la diferencia de dias entre now y period_end del las_payment
        if ($last_payment && $last_payment->period_end) {
            $period_end = \Carbon\Carbon::parse($last_payment->period_end);
            $days_difference = $period_end->diffInDays($end_date, false);
            if($days_difference != null && $days_difference > 0){
                $pending_salary = ($employee->salary / 30 ) * $days_difference;
                $pending_salary = round($pending_salary, 2); 
            }
        }
        
        if($pending_salary > 0){
            $deductions = Deduction::whereIn('id', [1,2])->get();
            $bonus = Bonu::where('id', 1)->first();
            if($bonus){
                $pending_salary += $bonus->bonu_fixed_amount;
            }
            $divisionFactor = $this->getDivisionFactor($employee->contract_type_id);
            // Descuento IGSS
            $deductionIgss = $deductions->first(function ($deduction) {
                return Str::contains(strtoupper($deduction->deduction_name), 'IGSS');
            });
            if($deductionIgss) {
                $iggsDiscountAmount = ($pending_salary * ($deductionIgss->deduction_percentage / 100)) / $divisionFactor;
            }
            // Descuento ISR
            $deductionIsr = $deductions->first(function ($deduction) {
                return Str::contains(strtoupper($deduction->deduction_name), 'ISR');
            });
            if($deductionIsr && $employee->salary > 4000) {
                $isrDiscountAmount = (($pending_salary - ($iggsDiscountAmount * $divisionFactor)) * ($deductionIsr->deduction_percentage / 100)) / $divisionFactor;
            }
        }
        
        $total_deductions = $iggsDiscountAmount + $isrDiscountAmount;
        $net_salary = ($pending_salary - $total_deductions) + $pending_aguinaldo + $pending_bonus_14;
        $total_income = $pending_salary + $pending_aguinaldo + $pending_bonus_14;

        $payroll = Payroll::create([
            'employee_id' => $employee->id,
            'payroll_type_id' => 4,
            'period_start' => $employee->hire_date,
            'period_end' => $end_date,
            'total_income' => $total_income,
            'total_deductions' => $total_deductions,
            'net_salary' => $net_salary,
            'status' => 1,
            'payroll_date' => null,
            'approved_by' => null,
        ]);

        if($payroll->id){
            if ($iggsDiscountAmount > 0) {
                $this->createPayrollDeducton($payroll->id, $deductionIgss->id, $iggsDiscountAmount);
            }
            if ($isrDiscountAmount > 0) {
                $this->createPayrollDeducton($payroll->id, $deductionIsr->id, $isrDiscountAmount);
            }

            if($pending_aguinaldo > 0){
                $this->createPayrollBonu($payroll->id, 3, $pending_aguinaldo);
            }
            if($pending_bonus_14 > 0){
                $this->createPayrollBonu($payroll->id, 2, $pending_bonus_14);
            }
            if($pending_vacations > 0){
                $this->createPayrollBonu($payroll->id, 4, $pending_vacations);
            }
        }

        return $pending_salary;
    }

    private function getDivisionFactor($contractTypeId): int
    {
        return match ($contractTypeId) {
            1 => 4,
            2 => 2,
            3 => 1,
        };
    }

    private function createPayrollDeducton($payrollId, $deductionId, $amount)
    {
        PayrollDeduction::create([
            'payroll_id' => $payrollId,
            'deduction_id' => $deductionId,
            'amount' => $amount,
        ]);
    }

    private function createPayrollBonu($payrollId, $bonuId, $amount)
    {
        PayrollBonu::create([
            'payroll_id' => $payrollId,
            'bonus_id' => $bonuId,
            'amount' => $amount,
        ]);
    }

    private function getPendingBonus($employee, $end_date){
        $start_period_bonus = 0;
        $panding_bonus_14 = 0;
        // obtenemos el ultimo bono 14 para saber si necesitamos pagarle un proporcional
        $last_bonus_14 = Payroll::where('employee_id', $employee->id)
            ->where('payroll_type_id', 2)
            ->orderBy('period_end', 'desc')
            ->first();
        if($last_bonus_14 == null){
            $start_period_bonus = Carbon::parse($employee->hire_date);
        }
        if($last_bonus_14 != null){
            $start_period_bonus = \Carbon\Carbon::parse($last_bonus_14->period_end);
        }
        $days_worked = $start_period_bonus->diffInDays($end_date) + 1;
        if($days_worked <= 364){
            $panding_bonus_14 = (round($days_worked, 1) / 365) * $employee->salary;
            $panding_bonus_14 = round($panding_bonus_14, 2);
        }

        return $panding_bonus_14;
    }

    private function getPendingAguinaldo($employee, $end_date){
        $start_period_bonus = 0;
        $panding_aguinaldo = 0;
        // obtenemos el ultimo aguinaldo para saber si necesitamos pagarle un proporcional
        $last_aguinaldo = Payroll::where('employee_id', $employee->id)
            ->where('payroll_type_id', 3)
            ->orderBy('period_end', 'desc')
            ->first();
        if($last_aguinaldo == null){
            $start_period_bonus = Carbon::parse($employee->hire_date);
        }
        if($last_aguinaldo != null){
            $start_period_bonus = \Carbon\Carbon::parse($last_aguinaldo->period_end);
        }
        $days_worked = $start_period_bonus->diffInDays($end_date) + 1;
        if($days_worked <= 364){
            $panding_aguinaldo = (round($days_worked, 1) / 365) * $employee->salary;
            $panding_aguinaldo = round($panding_aguinaldo, 2);
        }
        return $panding_aguinaldo;
    }

    private function getPendingVacations($employee){
        $pending_vacations = 0;
        $amount_vacations = 0;
        $vacation_balance = $employee->vacationBalance;
        if($vacation_balance){
            $pending_vacations = $vacation_balance->available_days;
        }
        if($pending_vacations > 0){
            $salary_for_days = $employee->salary / 30;
            $amount_vacations = $pending_vacations * $salary_for_days;
            $amount_vacations = round($amount_vacations, 2);
        }
        return $amount_vacations;
    }
}