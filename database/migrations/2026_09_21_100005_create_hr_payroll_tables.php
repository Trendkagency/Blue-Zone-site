<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('salary_structures')) {
            Schema::create('salary_structures', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('salary_components')) {
            Schema::create('salary_components', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salary_structure_id')->nullable()->constrained('salary_structures')->nullOnDelete();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('code')->unique();
                $table->string('type')->default('earning'); // earning, deduction
                $table->string('calculation_type')->default('fixed'); // fixed, percentage
                $table->decimal('default_value', 12, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_salary_assignments')) {
            Schema::create('employee_salary_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('salary_component_id')->constrained('salary_components')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('effective_date');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payroll_periods')) {
            Schema::create('payroll_periods', function (Blueprint $table) {
                $table->id();
                $table->string('name')->index(); // e.g. "September 2026"
                $table->date('start_date')->index();
                $table->date('end_date')->index();
                $table->decimal('total_gross', 14, 2)->default(0.00);
                $table->decimal('total_deductions', 14, 2)->default(0.00);
                $table->decimal('total_net', 14, 2)->default(0.00);
                $table->string('status')->default('draft')->index(); // draft, processing, pending_approval, approved, finalized, paid, cancelled
                $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('finalized_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('payroll_records')) {
            Schema::create('payroll_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('basic_salary', 12, 2)->default(0.00);
                $table->decimal('total_allowances', 12, 2)->default(0.00);
                $table->decimal('overtime_amount', 12, 2)->default(0.00);
                $table->decimal('bonus_amount', 12, 2)->default(0.00);
                $table->decimal('gross_salary', 12, 2)->default(0.00);
                $table->decimal('total_deductions', 12, 2)->default(0.00);
                $table->decimal('advance_deduction', 12, 2)->default(0.00);
                $table->decimal('loan_deduction', 12, 2)->default(0.00);
                $table->decimal('net_salary', 12, 2)->default(0.00);
                $table->string('status')->default('calculated')->index(); // calculated, approved, paid
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['payroll_period_id', 'employee_id']);
            });
        }

        if (!Schema::hasTable('payroll_items')) {
            Schema::create('payroll_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_record_id')->constrained('payroll_records')->cascadeOnDelete();
                $table->string('name');
                $table->string('type'); // earning, deduction
                $table->decimal('amount', 12, 2);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payslips')) {
            Schema::create('payslips', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_record_id')->constrained('payroll_records')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('payslip_number')->unique();
                $table->decimal('gross_salary', 12, 2);
                $table->decimal('net_salary', 12, 2);
                $table->dateTime('generated_at');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('salary_advances')) {
            Schema::create('salary_advances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('request_date')->index();
                $table->date('approved_date')->nullable();
                $table->date('repayment_start_date')->nullable();
                $table->unsignedInteger('installments')->default(1);
                $table->decimal('installment_amount', 12, 2)->default(0.00);
                $table->decimal('remaining_amount', 12, 2)->default(0.00);
                $table->string('status')->default('pending')->index(); // pending, approved, active, completed, rejected
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_loans')) {
            Schema::create('employee_loans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('start_date')->index();
                $table->unsignedInteger('installments')->default(1);
                $table->decimal('installment_amount', 12, 2);
                $table->decimal('remaining_balance', 12, 2);
                $table->string('status')->default('active')->index(); // active, completed, defaulted
                $table->text('notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_loans');
        Schema::dropIfExists('salary_advances');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_records');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('employee_salary_assignments');
        Schema::dropIfExists('salary_components');
        Schema::dropIfExists('salary_structures');
    }
};
