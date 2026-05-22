<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Helpers\ApiResponse;
use App\Exports\PayrollExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $payrolls = Payroll::with(['employee', 'employee.user', 'employee.contractType', 'payrollType', 'payrollBonu', 'payrollDeduction', 'payrollDeduction.deduction', 'payrollBonu.bonus'])
            ->whereBetween('period_start', [$request->period_start, $request->period_end])
            ->where('status', 2);
        if ($request->employee_id) {
            $payrolls->where('employee_id', $request->employee_id);
        }
        if($request->contract_type_id){
            $payrolls->whereHas('employee', function($query) use ($request) {
                $query->where('contract_type_id', $request->contract_type_id);
            });
        }
        $payrolls = $payrolls->get();
        return ApiResponse::success($payrolls, 'Lista de nómina');
    }

    public function exportExcel(Request $request)
    {
        $payrolls = Payroll::with(['employee', 'employee.user', 'employee.contractType', 'payrollType', 'payrollBonu', 'payrollDeduction', 'payrollDeduction.deduction', 'payrollBonu.bonus'])
            ->whereBetween('period_start', [$request->period_start, $request->period_end])
            ->where('status', 2);
            
        if ($request->employee_id) {
            $payrolls->where('employee_id', $request->employee_id);
        }
        
        $payrolls = $payrolls->get();
        
        return Excel::download(new PayrollExport($payrolls), 'nomina.xlsx');
    }
}