<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employee_requests')) {
            Schema::create('employee_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('request_type')->index(); // hr_request, certificate, document, schedule_change, salary_request, transfer_request, complaint, suggestion, other
                $table->string('subject');
                $table->text('description');
                $table->string('priority')->default('normal'); // low, normal, high, urgent
                $table->string('status')->default('pending')->index(); // pending, in_progress, approved, rejected, completed, cancelled
                $table->text('response_notes')->nullable();
                $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('resolved_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_transfers')) {
            Schema::create('employee_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('old_location_id')->nullable();
                $table->foreign('old_location_id')->references('id')->on('locations')->nullOnDelete();
                $table->string('new_location_id')->nullable();
                $table->foreign('new_location_id')->references('id')->on('locations')->nullOnDelete();
                $table->foreignId('old_department_id')->nullable()->constrained('departments')->nullOnDelete();
                $table->foreignId('new_department_id')->nullable()->constrained('departments')->nullOnDelete();
                $table->foreignId('old_position_id')->nullable()->constrained('positions')->nullOnDelete();
                $table->foreignId('new_position_id')->nullable()->constrained('positions')->nullOnDelete();
                $table->date('effective_date')->index();
                $table->text('reason')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_disciplinary_actions')) {
            Schema::create('employee_disciplinary_actions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('incident_date')->index();
                $table->string('incident_type'); // attendance, conduct, performance, policy_violation, other
                $table->text('description');
                $table->text('investigation_notes')->nullable();
                $table->text('employee_response')->nullable();
                $table->string('action_type')->index(); // verbal_warning, written_warning, final_warning, salary_deduction, suspension, termination_recommendation
                $table->date('action_date');
                $table->decimal('deduction_amount', 10, 2)->default(0.00);
                $table->unsignedInteger('suspension_days')->default(0);
                $table->string('attachment_path')->nullable();
                $table->string('status')->default('active')->index(); // active, resolved, appealed
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('hr_assets')) {
            Schema::create('hr_assets', function (Blueprint $table) {
                $table->id();
                $table->string('asset_code')->unique();
                $table->string('name');
                $table->string('category')->index(); // laptop, phone, tablet, sim, id_card, keys, equipment, other
                $table->string('serial_number')->nullable()->index();
                $table->string('model')->nullable();
                $table->date('purchase_date')->nullable();
                $table->decimal('cost', 10, 2)->default(0.00);
                $table->string('status')->default('available')->index(); // available, assigned, maintenance, retired
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_asset_assignments')) {
            Schema::create('employee_asset_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('hr_asset_id')->constrained('hr_assets')->cascadeOnDelete();
                $table->dateTime('assigned_at')->index();
                $table->date('expected_return_date')->nullable()->index();
                $table->dateTime('returned_at')->nullable()->index();
                $table->string('condition_on_assignment')->default('good');
                $table->string('condition_on_return')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_offboardings')) {
            Schema::create('employee_offboardings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('offboarding_type')->index(); // resignation, termination, retirement
                $table->date('submission_date')->index();
                $table->date('last_working_date')->index();
                $table->text('reason');
                $table->unsignedInteger('notice_period_days')->default(30);
                $table->string('status')->default('submitted')->index(); // submitted, under_review, approved, in_progress, completed, rejected
                $table->text('notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('exit_interviews')) {
            Schema::create('exit_interviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_offboarding_id')->constrained('employee_offboardings')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('interviewer_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('reason_for_leaving')->nullable();
                $table->unsignedTinyInteger('working_environment_rating')->default(3); // 1-5
                $table->unsignedTinyInteger('management_rating')->default(3);
                $table->unsignedTinyInteger('compensation_rating')->default(3);
                $table->text('suggestions')->nullable();
                $table->text('confidential_notes')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('final_settlements')) {
            Schema::create('final_settlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_offboarding_id')->constrained('employee_offboardings')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('basic_salary_due', 12, 2)->default(0.00);
                $table->decimal('leave_encashment_amount', 12, 2)->default(0.00);
                $table->decimal('overtime_amount', 12, 2)->default(0.00);
                $table->decimal('end_of_service_gratuity', 12, 2)->default(0.00);
                $table->decimal('advance_deductions', 12, 2)->default(0.00);
                $table->decimal('loan_deductions', 12, 2)->default(0.00);
                $table->decimal('asset_damage_deductions', 12, 2)->default(0.00);
                $table->decimal('net_settlement_amount', 12, 2)->default(0.00);
                $table->string('status')->default('draft')->index(); // draft, approved, paid
                $table->date('settlement_date')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('final_settlements');
        Schema::dropIfExists('exit_interviews');
        Schema::dropIfExists('employee_offboardings');
        Schema::dropIfExists('employee_asset_assignments');
        Schema::dropIfExists('hr_assets');
        Schema::dropIfExists('employee_disciplinary_actions');
        Schema::dropIfExists('employee_transfers');
        Schema::dropIfExists('employee_requests');
    }
};
