<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vacation extends Model
{
    use HasFactory;
    protected $table = 'vacation';

    protected $fillable = [
        'employee_id',
        'total_days',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
