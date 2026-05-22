<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Employee;
use App\Models\Bonu;
use App\Models\Deduction;
use Illuminate\Support\Str;
use App\Models\Payroll;
use Illuminate\Support\Facades\DB;
use App\Models\PayrollDeduction;
use App\Models\PayrollBonu;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{

    public function index()
    {
        $payrolls = Payroll::with(['employee', 'employee.user', 'employee.contractType', 'payrollType', 'payrollBonu', 'payrollDeduction', 'payrollDeduction.deduction', 'payrollBonu.bonus'])
            ->get();
        return ApiResponse::success($payrolls, 'Lista de nómina');
    }

    public function showByEmployee(Request $request, $employeeId)
    {
        $employee = Employee::where('user_id', $employeeId)->first();
        $payrolls = Payroll::with(['employee', 'employee.user', 'employee.contractType', 'payrollType', 'payrollBonu', 'payrollDeduction', 'payrollDeduction.deduction', 'payrollBonu.bonus'])
            ->where('employee_id', $employee->id)
            ->where('status', 2)
            ->whereBetween('period_start',  [Carbon::parse($request->period_start), Carbon::parse($request->period_end)])
            ->get();
        return ApiResponse::success($payrolls, 'Lista de nómina por empleado');
    }

    public function pay(Request $request){
        $payrolls = Payroll::whereIn('id', $request->payrolls)
            ->get();
        if ($payrolls->isNotEmpty()) {
            foreach ($payrolls as $payroll) {
                $payroll->status = 2; // Cambiar el estado a pagado
                $payroll->payment_date = now(); // Establecer la fecha de nómina
                $payroll->save();
            }
            return ApiResponse::success($payrolls, 'Lista de nómina pagadas');
        } else {
            return ApiResponse::error(null, 'No se encontraron nóminas para pagar.', 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $payrolls = $this->createPayrolls($request);
            return ApiResponse::success($payrolls, 'Nómina creada correctamente');
        } catch (\Throwable $th) {
            return ApiResponse::error(null, 'Error al crear la nómina. ' . $th->getMessage(), 500);
        }
    }

    private function createPayrolls(Request $request): array
    {
        $payrolls = [];
        $employees = Employee::where('is_active', 1)
            ->where('contract_type_id', $request->contract_type_id)->get();
        if($request->payroll_type_id == 1){
            $bonus = Bonu::whereIn('id', $request->bonus)->get();
            $deductions = Deduction::whereIn('id', $request->deductions)->get();
        }
        
        try {
            DB::beginTransaction();
            $divisionFactor = $this->getDivisionFactor($request->contract_type_id);
            if ($request->payroll_type_id == 1) {
                foreach ($employees as $employee) {
                    $grossSalary = $employee->salary; //Salario base
                    foreach ($bonus as $bonu) {
                        $grossSalary += $bonu->bonu_fixed_amount;
                    }
        
                    // Descuento IGSS
                    $iggsDiscountAmount = 0;
                    $deductionIgss = $deductions->first(function ($deduction) {
                        return Str::contains(strtoupper($deduction->deduction_name), 'IGSS');
                    });
                    if($deductionIgss) {
                        $iggsDiscountAmount = ($employee->salary * ($deductionIgss->deduction_percentage / 100)) / $divisionFactor;
                    }
                    
                    // Descuento ISR
                    $isrDiscountAmount = 0; 
                    $deductionIsr = $deductions->first(function ($deduction) {
                        return Str::contains(strtoupper($deduction->deduction_name), 'ISR');
                    });
                    if($deductionIsr && $grossSalary > 4000) {
                        $isrDiscountAmount = (($grossSalary - ($iggsDiscountAmount * $divisionFactor)) * ($deductionIsr->deduction_percentage / 100)) / $divisionFactor;
                    }
        
                    $totalDeductions = $isrDiscountAmount + $iggsDiscountAmount; // Total de descuentos
                    $netSalary = $grossSalary - $totalDeductions; // Salario neto
        
                    $payroll = Payroll::create([
                        'employee_id' => $employee->id,
                        'payroll_type_id' => $request->payroll_type_id,
                        'period_start' => $request->period_start,
                        'period_end' => $request->period_end,
                        'total_income' => ($grossSalary / $divisionFactor),
                        'total_deductions' => $totalDeductions,
                        'net_salary' => $netSalary / $divisionFactor,
                        'status' => 1,
                        'payroll_date' => null,
                        'approved_by' => null,
                    ]);
    
                    if($payroll->id){
                        if ($deductionIgss) {
                            $this->createPayrollDeducton($payroll->id, $deductionIgss->id, $iggsDiscountAmount);
                        }
                        if ($deductionIsr) {
                            $this->createPayrollDeducton($payroll->id, $deductionIsr->id, $isrDiscountAmount);
                        }
                        foreach ($bonus as $bonu) {
                            $this->createPayrollBonu($payroll->id, $bonu->id, $bonu->bonu_fixed_amount);
                        }
                    }
                    $payrollWithRelations = Payroll::with(['employee', 'employee.user', 'employee.contractType' ,'payrollType', 'payrollBonu', 'payrollDeduction', 'payrollDeduction.deduction', 'payrollBonu.bonus'])
                        ->where('id', $payroll->id)
                        ->first();
                    array_push($payrolls, $payrollWithRelations);
                }
            }
            if ($request->payroll_type_id == 2 || $request->payroll_type_id == 3) {
                foreach($employees as $employee) {
                    $grossSalary = $employee->salary; //Salario base

                    $hireDate = Carbon::parse($employee->hire_date);
                    $endDate = Carbon::parse($request->period_end);
                    $daysWorked = $hireDate->diffInDays($endDate) + 1;
                    
                    if($daysWorked <= 364){
                        $netSalary = ($daysWorked / 365) * $grossSalary;
                    }else{
                        $netSalary = $grossSalary;
                    }
                    
                    $payroll = Payroll::create([
                        'employee_id' => $employee->id,
                        'payroll_type_id' => $request->payroll_type_id,
                        'period_start' => $request->period_start,
                        'period_end' => $request->period_end,
                        'total_income' => $netSalary,
                        'total_deductions' => 0,
                        'net_salary' => $netSalary,
                        'status' => 1,
                        'payroll_date' => null,
                        'approved_by' => null,
                    ]);
                    $payrollWithRelations = Payroll::with(['employee', 'employee.user', 'employee.contractType' ,'payrollType', 'payrollBonu', 'payrollDeduction', 'payrollDeduction.deduction', 'payrollBonu.bonus'])
                        ->where('id', $payroll->id)
                        ->first();
                    array_push($payrolls, $payrollWithRelations);
                }
            }
            DB::commit();
            return $payrolls;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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

    private function getDivisionFactor($contractTypeId): int
    {
        return match ($contractTypeId) {
            "1" => 4,
            "2" => 2,
            "3" => 1,
        };
    }

    public function generatePayroll()
    {
        $payrolls = [];
        $employees = Employee::where('is_active', 1)->get();
        $startDate = '2018-01-01';
        $endDate = Carbon::now()->endOfMonth();

        foreach ($employees as $employee) {
            $hireDate = Carbon::parse($employee->hire_date);
            $periods = $this->generatePayrollPeriods($employee->contract_type_id, $startDate, $endDate, $hireDate);
            
            foreach ($periods as $period) {
                $request = new Request([
                    'bonus' => ['1'],
                    'contract_type_id' => strval($employee->contract_type_id),
                    'deductions' => ['1', '2'],
                    'payroll_type_id' => 1,
                    'period_start' => $period['start'],
                    'period_end' => $period['end'],
                ]);
                
                try {
                    $createdPayrolls = $this->createPayrolls($request);
                    array_push($payrolls, ...$createdPayrolls);
                } catch (\Throwable $th) {
                    \Log::error('Error generando nómina: ' . $th->getMessage());
                }
            }
        }
        return ApiResponse::success($payrolls, 'Nómina generada correctamente');
    }

    private function generatePayrollPeriods($contractTypeId, $startDate, $endDate, $hireDate): array
    {
        $periods = [];
        
        if ($contractTypeId == 3) {
            // Monthly: full month
            $periods = $this->getMonthlyPeriods($startDate, $endDate, $hireDate);
        } elseif ($contractTypeId == 2) {
            // Biweekly: 1-15 and 16-end of month
            $periods = $this->getBiweeklyPeriods($startDate, $endDate, $hireDate);
        } elseif ($contractTypeId == 1) {
            // Weekly: Monday to Sunday
            $periods = $this->getWeeklyPeriods($startDate, $endDate, $hireDate);
        }
        
        return $periods;
    }

    private function getMonthlyPeriods($startDate, $endDate, $hireDate): array
    {
        $periods = [];
        $currentDate = Carbon::parse($startDate)->startOfMonth();
        $endDate = Carbon::parse($endDate);
        
        while ($currentDate->lte($endDate)) {
            if ($currentDate->gte($hireDate)) {
                $periods[] = [
                    'start' => $currentDate->toDateString(),
                    'end' => $currentDate->endOfMonth()->toDateString(),
                ];
            }
            $currentDate = $currentDate->endOfMonth()->addDay();
        }
        
        return $periods;
    }

    private function getBiweeklyPeriods($startDate, $endDate, $hireDate): array
    {
        $periods = [];
        $currentDate = Carbon::parse($startDate)->startOfMonth();
        $endDate = Carbon::parse($endDate);
        
        while ($currentDate->lte($endDate)) {
            // First period: 1st to 15th
            $firstStart = $currentDate->copy();
            $firstEnd = $currentDate->copy()->setDay(15);
            
            if ($firstStart->gte($hireDate) && $firstStart->lte($endDate)) {
                $periods[] = [
                    'start' => $firstStart->toDateString(),
                    'end' => $firstEnd->toDateString(),
                ];
            }
            
            // Second period: 16th to end of month
            $secondStart = $currentDate->copy()->setDay(16);
            $secondEnd = $currentDate->copy()->endOfMonth();
            
            if ($secondStart->gte($hireDate) && $secondStart->lte($endDate)) {
                $periods[] = [
                    'start' => $secondStart->toDateString(),
                    'end' => $secondEnd->toDateString(),
                ];
            }
            
            $currentDate = $currentDate->endOfMonth()->addDay();
        }
        
        return $periods;
    }

    private function getWeeklyPeriods($startDate, $endDate, $hireDate): array
    {
        $periods = [];
        $currentDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        
        // Adjust to start on Monday
        if ($currentDate->dayOfWeek !== 1) { // 1 = Monday
            $currentDate = $currentDate->startOfWeek();
        }
        
        while ($currentDate->lte($endDate)) {
            $weekStart = $currentDate->copy()->startOfWeek();
            $weekEnd = $currentDate->copy()->endOfWeek();
            
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate->copy();
            }
            
            if ($weekStart->gte($hireDate)) {
                $periods[] = [
                    'start' => $weekStart->toDateString(),
                    'end' => $weekEnd->toDateString(),
                ];
            }
            
            $currentDate = $currentDate->addWeek();
        }
        
        return $periods;
    }
}
