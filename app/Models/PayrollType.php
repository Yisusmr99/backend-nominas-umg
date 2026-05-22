<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollType extends Model
{
    use HasFactory;
    protected $table = 'payroll_type';

    protected $fillable = [
        'name',
    ];
}