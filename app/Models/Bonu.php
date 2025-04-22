<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bonu extends Model
{
    use HasFactory;
    protected $table = 'bonus';

    protected $fillable = [
        'bonu_name',
        'bonu_percentage',
        'bonu_fixed_amount',
    ];

}