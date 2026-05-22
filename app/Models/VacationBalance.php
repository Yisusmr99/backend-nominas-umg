<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VacationBalance extends Model
{
    use HasFactory;
    protected $table = 'vacation_balance';

    protected $fillable = [
        'employee_id',
        'accrued_days',
        'used_days',
        'available_days',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
