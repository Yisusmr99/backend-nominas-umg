<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deduction extends Model
{
    use HasFactory;
    protected $table = 'deduction';

    protected $fillable = [
        'deduction_name',
        'deduction_percentage',
        'deduction_fixed_amount',
    ];
}