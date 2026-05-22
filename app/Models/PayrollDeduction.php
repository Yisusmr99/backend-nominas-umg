<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollDeduction extends Model
{
    use HasFactory;
    protected $table = 'payroll_deduction';

    protected $fillable = [
        'payroll_id',
        'deduction_id',
        'amount',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function deduction()
    {
        return $this->belongsTo(Deduction::class);
    }
}