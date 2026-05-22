<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('performance_evaluation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee')->onDelete('cascade');
            $table->integer('quality_of_work');
            $table->integer('achievement_of_objectives');
            $table->integer('responsibility');
            $table->integer('teamwork_communication');
            $table->integer('proactivity');
            $table->integer('final_note');
            $table->date('start_period');
            $table->date('end_period');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_evaluation');
    }
};
