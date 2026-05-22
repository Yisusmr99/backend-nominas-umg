<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    protected $table = 'employee';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dpi',
        'nit',
        'position',
        'salary',
        'hire_date',
        'termination_date',
        'is_active',
        'contract_type_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }

    // add relationships for vacation and vacation_balance
    public function vacation()
    {
        return $this->hasMany(Vacation::class);
    }
    public function vacationBalance()
    {
        return $this->hasOne(VacationBalance::class);
    }
}