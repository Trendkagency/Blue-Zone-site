<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_vacancies')) {
            Schema::create('job_vacancies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->string('location_id')->nullable()->index();
                $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
                $table->string('title_en');
                $table->string('title_ar')->nullable();
                $table->text('description');
                $table->text('requirements')->nullable();
                $table->unsignedInteger('openings')->default(1);
                $table->string('employment_type')->default('full_time'); // full_time, part_time, contract
                $table->decimal('salary_min', 12, 2)->nullable();
                $table->decimal('salary_max', 12, 2)->nullable();
                $table->string('status')->default('published')->index(); // draft, published, closed, on_hold
                $table->date('opened_at')->nullable();
                $table->date('closing_at')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('candidates')) {
            Schema::create('candidates', function (Blueprint $table) {
                $table->id();
                $table->string('candidate_number')->unique();
                $table->foreignId('job_vacancy_id')->nullable()->constrained('job_vacancies')->nullOnDelete();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->index();
                $table->string('phone')->index();
                $table->string('whatsapp')->nullable();
                $table->string('cv_path')->nullable();
                $table->string('source')->nullable(); // linkedin, website, referral, agency, walk_in
                $table->string('applied_position')->nullable();
                $table->decimal('expected_salary', 12, 2)->nullable();
                $table->unsignedInteger('experience_years')->default(0);
                $table->string('education')->nullable();
                $table->string('status')->default('new')->index(); // new, reviewing, shortlisted, interview, technical_test, offer, hired, rejected
                $table->text('rejection_reason')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('converted_employee_id')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('candidate_interviews')) {
            Schema::create('candidate_interviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
                $table->foreignId('interviewer_employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->foreignId('interviewer_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('interview_type')->default('hr'); // hr, technical, management, final
                $table->dateTime('scheduled_at')->index();
                $table->string('location')->nullable();
                $table->string('meeting_url')->nullable();
                $table->decimal('score', 4, 1)->nullable(); // 0 - 100 or 0 - 10
                $table->text('evaluation_notes')->nullable();
                $table->string('result')->default('pending')->index(); // passed, failed, pending
                $table->string('status')->default('scheduled')->index(); // scheduled, completed, cancelled, rescheduled
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('job_offers')) {
            Schema::create('job_offers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
                $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->decimal('salary', 12, 2);
                $table->decimal('housing_allowance', 12, 2)->default(0.00);
                $table->decimal('transportation_allowance', 12, 2)->default(0.00);
                $table->string('employment_type')->default('full_time');
                $table->date('start_date');
                $table->unsignedInteger('probation_period_months')->default(3);
                $table->date('offer_expiry_date')->nullable()->index();
                $table->string('status')->default('draft')->index(); // draft, pending_approval, sent, accepted, rejected, expired
                $table->text('notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('onboarding_templates')) {
            Schema::create('onboarding_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('onboarding_tasks')) {
            Schema::create('onboarding_tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('onboarding_template_id')->constrained('onboarding_templates')->cascadeOnDelete();
                $table->string('title_en');
                $table->string('title_ar');
                $table->text('description')->nullable();
                $table->string('assigned_role')->nullable(); // hr, it, manager, employee
                $table->unsignedInteger('due_days')->default(1);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_required')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_onboardings')) {
            Schema::create('employee_onboardings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('onboarding_template_id')->nullable()->constrained('onboarding_templates')->nullOnDelete();
                $table->date('start_date');
                $table->date('target_completion_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->string('status')->default('in_progress')->index(); // pending, in_progress, completed, cancelled
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_onboarding_tasks')) {
            Schema::create('employee_onboarding_tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_onboarding_id')->constrained('employee_onboardings')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('assigned_to_role')->nullable();
                $table->date('due_date')->nullable();
                $table->date('completed_at')->nullable();
                $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status')->default('pending')->index(); // pending, in_progress, completed, skipped
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_onboarding_tasks');
        Schema::dropIfExists('employee_onboardings');
        Schema::dropIfExists('onboarding_tasks');
        Schema::dropIfExists('onboarding_templates');
        Schema::dropIfExists('job_offers');
        Schema::dropIfExists('candidate_interviews');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('job_vacancies');
    }
};
