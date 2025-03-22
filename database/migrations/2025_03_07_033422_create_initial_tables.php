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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('role_has_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('contract_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('dpi');
            $table->integer('nit');
            $table->string('position');
            $table->double('salary');
            $table->date('hire_date');
            $table->date('termination_date');
            $table->boolean('is_active')->default(true);
            $table->foreignId('contract_type_id')->constrained('contract_types')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee')->onDelete('cascade');
            $table->date('work_date');
            $table->integer('worked_hours');
            $table->double('overtime_hours');
            $table->timestamps();
        });

        Schema::create('payroll_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee')->onDelete('cascade');
            $table->foreignId('payroll_type_id')->constrained('payroll_type')->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->double('total_income');
            $table->double('total_deductions');
            $table->double('net_salary');
            // campo estado, debe ser un enum que tenga los valores de 'pendiente', 'pagado', 'anulado'
            $table->enum('status', ['pendiente', 'pagado', 'anulado']);
            $table->date('payment_date');
            $table->foreignId('approved_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('bonus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('percentage');
            $table->double('fixed_amount');
            $table->timestamps();
        });

        Schema::create('payroll_bonus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payroll')->onDelete('cascade');
            $table->foreignId('bonus_id')->constrained('bonus')->onDelete('cascade');
            $table->double('amount');
            $table->timestamps();
        });

        Schema::create('deduction', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('percentage');
            $table->double('fixed_amount');
            $table->timestamps();
        });

        Schema::create('payroll_deduction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payroll')->onDelete('cascade');
            $table->foreignId('deduction_id')->constrained('deduction')->onDelete('cascade');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deduction');
        Schema::dropIfExists('payroll_deduction');
        Schema::dropIfExists('bonus');
        Schema::dropIfExists('payroll_bonus');
        Schema::dropIfExists('payroll');
        Schema::dropIfExists('payroll_type');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employee');
        Schema::dropIfExists('contract_types');
        Schema::dropIfExists('role_has_permission');
        Schema::dropIfExists('permissions');
    }
};
