<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances';

    protected $fillable = [
        'employee_id',
        'work_date',
        'worked_hours',
        'overtime_hours',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}