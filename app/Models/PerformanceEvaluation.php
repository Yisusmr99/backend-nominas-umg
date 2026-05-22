<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceEvaluation extends Model
{
    protected $table = 'performance_evaluation';
    
    protected $fillable = [
        'employee_id',
        'quality_of_work',
        'achievement_of_objectives',
        'responsibility',
        'teamwork_communication',
        'proactivity',
        'final_note',
        'start_period',
        'end_period'
    ];

    protected $casts = [
        'start_period' => 'date',
        'end_period' => 'date',
        'quality_of_work' => 'integer',
        'achievement_of_objectives' => 'integer',
        'responsibility' => 'integer',
        'teamwork_communication' => 'integer',
        'proactivity' => 'integer'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
