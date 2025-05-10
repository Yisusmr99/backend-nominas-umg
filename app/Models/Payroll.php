<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;
    protected $table = 'payroll';

    protected $fillable = [
        'employee_id',
        'payroll_type_id',
        'period_start',
        'period_end',
        'total_income',
        'total_deductions',
        'net_salary',
        'status',
        'payment_date',
        'approved_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollType()
    {
        return $this->belongsTo(PayrollType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payrollBonu()
    {
        return $this->hasMany(PayrollBonu::class);
    }

    public function payrollDeduction()
    {
        return $this->hasMany(PayrollDeduction::class);
    }
}