<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendance_records')) {
            Schema::create('attendance_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('attendance_date')->index();
                $table->time('check_in')->nullable();
                $table->time('check_out')->nullable();
                $table->unsignedInteger('worked_minutes')->default(0);
                $table->unsignedInteger('late_minutes')->default(0);
                $table->unsignedInteger('early_leave_minutes')->default(0);
                $table->unsignedInteger('overtime_minutes')->default(0);
                $table->string('status')->default('present')->index(); // present, late, absent, half_day, leave, holiday, weekend, remote
                $table->string('source')->default('web')->index(); // manual, web, mobile, biometric, api
                $table->text('notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['employee_id', 'attendance_date']);
            });
        }

        if (!Schema::hasTable('overtime_requests')) {
            Schema::create('overtime_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('date')->index();
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedInteger('minutes');
                $table->text('reason');
                $table->string('status')->default('pending')->index(); // pending, approved, rejected, cancelled
                $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('payroll_status')->default('unpaid')->index(); // unpaid, paid
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('code')->unique(); // annual, sick, emergency, unpaid, maternity, paternity, personal, study, custom
                $table->unsignedInteger('annual_days')->default(21);
                $table->boolean('requires_attachment')->default(false);
                $table->boolean('requires_approval')->default(true);
                $table->boolean('carry_forward')->default(false);
                $table->boolean('is_paid')->default(true);
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('leave_balances')) {
            Schema::create('leave_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
                $table->unsignedSmallInteger('year')->index();
                $table->decimal('allocated_days', 5, 2)->default(0.00);
                $table->decimal('used_days', 5, 2)->default(0.00);
                $table->decimal('pending_days', 5, 2)->default(0.00);
                $table->decimal('remaining_days', 5, 2)->default(0.00);
                $table->timestamps();

                $table->unique(['employee_id', 'leave_type_id', 'year']);
            });
        }

        if (!Schema::hasTable('leave_requests')) {
            Schema::create('leave_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
                $table->date('start_date')->index();
                $table->date('end_date')->index();
                $table->decimal('total_days', 5, 2);
                $table->text('reason');
                $table->string('attachment_path')->nullable();
                $table->string('status')->default('pending')->index(); // pending, approved, rejected, cancelled
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('overtime_requests');
        Schema::dropIfExists('attendance_records');
    }
};
