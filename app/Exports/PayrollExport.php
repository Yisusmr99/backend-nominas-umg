<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayrollExport implements FromCollection, WithHeadings, WithMapping
{
    protected $payrolls;

    public function __construct($payrolls)
    {
        $this->payrolls = $payrolls;
    }

    public function collection()
    {
        // dd($this->payrolls);
        return $this->payrolls;
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'DPI',
            'NIT', 
            'Puesto',
            'Tipo de Contrato',
            'Salario Base',
            'Tipo de Nómina',
            'Periodo Inicio',
            'Periodo Fin',
            'Estado',
            'Fecha de Pago',
            'Bono Decreto',
            'IGSS',
            'ISR',
            'Total Ingresos',
            'Total Deducciones',
            'Salario Neto'
        ];
    }

    public function map($payroll): array
    {
        // Default values
        $bonusAmount = 0;
        $igssAmount = 0;
        $isrAmount = 0;
        
        // Safely get employee and user data
        $employeeName = $payroll->employee && $payroll->employee->user 
            ? $payroll->employee->user->name . ' ' . $payroll->employee->user->last_name 
            : 'N/A';
        
        $employeeDpi = $payroll->employee ? $payroll->employee->dpi : 'N/A';
        $employeeNit = $payroll->employee ? $payroll->employee->nit : 'N/A';
        $employeePosition = $payroll->employee ? $payroll->employee->position : 'N/A';
        $contractTypeName = $payroll->employee && $payroll->employee->contractType 
            ? $payroll->employee->contractType->name 
            : 'N/A';
        $employeeSalary = $payroll->employee ? $payroll->employee->salary : 0;
        $payrollTypeName = $payroll->payrollType ? $payroll->payrollType->name : 'N/A';

        // Get bonus and deductions with null checks
        if ($payroll->payrollBonu && count($payroll->payrollBonu) > 0) {
            $bonusAmount = $payroll->payrollBonu[0]->amount ?? 0;
        }

        if ($payroll->payrollDeduction && count($payroll->payrollDeduction) > 0) {
            foreach ($payroll->payrollDeduction as $deduction) {
                if ($deduction->deduction && $deduction->deduction->deduction_name === 'IGSS') {
                    $igssAmount = $deduction->amount ?? 0;
                }
                if ($deduction->deduction && $deduction->deduction->deduction_name === 'ISR') {
                    $isrAmount = $deduction->amount ?? 0;
                }
            }
        }

        return [
            $employeeName,
            $employeeDpi,
            $employeeNit,
            $employeePosition,
            $contractTypeName,
            $employeeSalary,
            $payrollTypeName,
            $payroll->period_start ?? 'N/A',
            $payroll->period_end ?? 'N/A',
            $payroll->status ?? 'N/A',
            $payroll->payment_date ?? 'N/A',
            $bonusAmount,
            $igssAmount,
            $isrAmount == 0.0 ? 'N/A' : $isrAmount,
            $payroll->total_income ?? 0,
            $payroll->total_deductions ?? 0,
            $payroll->net_salary ?? 0
        ];
    }
}
