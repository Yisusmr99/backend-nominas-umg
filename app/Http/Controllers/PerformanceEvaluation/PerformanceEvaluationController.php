<?php

namespace App\Http\Controllers\PerformanceEvaluation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerformanceEvaluation;
use App\Helpers\ApiResponse;

class PerformanceEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $query = PerformanceEvaluation::with('employee', 'employee.user');
        
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $evaluations = $query->get();
        return ApiResponse::success($evaluations, 'Lista de evaluaciones de desempeño');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'quality_of_work' => 'required|integer',
            'achievement_of_objectives' => 'required|integer',
            'responsibility' => 'required|integer',
            'teamwork_communication' => 'required|integer',
            'proactivity' => 'required|integer',
            'start_period' => 'required|date',
            'end_period' => 'required|date'
        ]);

        $data['final_note'] = $this->PerformanceEvaluation($data);

        $evaluation = PerformanceEvaluation::create($data);
        return ApiResponse::success($evaluation, 'Evaluación de desempeño creada con éxito');
    }

    private function PerformanceEvaluation($data){
        $addNotes = $data['quality_of_work'] + $data['achievement_of_objectives'] + 
            $data['responsibility'] + $data['teamwork_communication'] + $data['proactivity'];

        return $addNotes / 5;
    }
}
