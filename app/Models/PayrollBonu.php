<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollBonu extends Model
{
    use HasFactory;
    protected $table = 'payroll_bonus';

    protected $fillable = [
        'payroll_id',
        'bonus_id',
        'amount',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function bonus()
    {
        return $this->belongsTo(Bonus::class);
    }
}