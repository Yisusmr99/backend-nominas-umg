<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bonu extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonu_name',
        'bonu_amount',
        'bonu_fixed_amount',
    ];

}