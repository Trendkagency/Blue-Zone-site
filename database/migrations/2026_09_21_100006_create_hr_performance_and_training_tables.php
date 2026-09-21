<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('performance_cycles')) {
            Schema::create('performance_cycles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->index();
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status')->default('draft')->index(); // draft, active, evaluation, closed
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('performance_goals')) {
            Schema::create('performance_goals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('performance_cycle_id')->constrained('performance_cycles')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('target');
                $table->string('actual')->nullable();
                $table->decimal('achievement_percentage', 5, 2)->default(0.00);
                $table->string('status')->default('in_progress')->index(); // in_progress, achieved, partially_achieved, missed
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('performance_kpis')) {
            Schema::create('performance_kpis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('measurement_unit')->default('number'); // percentage, number, currency, rating
                $table->decimal('default_target', 10, 2)->default(100.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('performance_reviews')) {
            Schema::create('performance_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('performance_cycle_id')->constrained('performance_cycles')->cascadeOnDelete();
                $table->foreignId('reviewer_employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->foreignId('reviewer_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('overall_score', 4, 2)->nullable();
                $table->text('strengths')->nullable();
                $table->text('areas_for_improvement')->nullable();
                $table->text('overall_notes')->nullable();
                $table->string('status')->default('draft')->index(); // draft, submitted, approved, completed
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('performance_review_items')) {
            Schema::create('performance_review_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('performance_review_id')->constrained('performance_reviews')->cascadeOnDelete();
                $table->string('category'); // goal, kpi, core_competency, leadership
                $table->string('title');
                $table->decimal('rating', 3, 1)->default(0.0); // 1.0 to 5.0
                $table->text('comments')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_promotions')) {
            Schema::create('employee_promotions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('old_department_id')->nullable()->constrained('departments')->nullOnDelete();
                $table->foreignId('new_department_id')->nullable()->constrained('departments')->nullOnDelete();
                $table->foreignId('old_position_id')->nullable()->constrained('positions')->nullOnDelete();
                $table->foreignId('new_position_id')->nullable()->constrained('positions')->nullOnDelete();
                $table->decimal('old_salary', 12, 2);
                $table->decimal('new_salary', 12, 2);
                $table->date('effective_date')->index();
                $table->text('reason')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('training_programs')) {
            Schema::create('training_programs', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->text('description')->nullable();
                $table->string('provider')->nullable();
                $table->string('trainer')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->decimal('cost', 10, 2)->default(0.00);
                $table->unsignedInteger('duration_hours')->default(0);
                $table->string('status')->default('scheduled')->index(); // scheduled, in_progress, completed, cancelled
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_training')) {
            Schema::create('employee_training', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('training_program_id')->constrained('training_programs')->cascadeOnDelete();
                $table->dateTime('assigned_at');
                $table->dateTime('completed_at')->nullable();
                $table->string('status')->default('assigned')->index(); // assigned, in_progress, completed, failed
                $table->decimal('result_score', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('training_certificates')) {
            Schema::create('training_certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('training_program_id')->nullable()->constrained('training_programs')->nullOnDelete();
                $table->string('certificate_title');
                $table->string('certificate_number')->nullable()->index();
                $table->date('issue_date')->index();
                $table->date('expiry_date')->nullable()->index();
                $table->string('file_path');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('training_certificates');
        Schema::dropIfExists('employee_training');
        Schema::dropIfExists('training_programs');
        Schema::dropIfExists('employee_promotions');
        Schema::dropIfExists('performance_review_items');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('performance_kpis');
        Schema::dropIfExists('performance_goals');
        Schema::dropIfExists('performance_cycles');
    }
};
