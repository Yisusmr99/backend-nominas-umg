<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rol extends Model
{
    protected $table = 'roles';
    use HasFactory;

    protected $fillable = ['name'];
}