<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('code')->unique();
                $table->unsignedBigInteger('manager_employee_id')->nullable()->index();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('code')->unique();
                $table->string('level')->default('entry'); // entry, intermediate, senior, lead, manager, director
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('work_schedules')) {
            Schema::create('work_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->time('start_time')->default('09:00:00');
                $table->time('end_time')->default('17:00:00');
                $table->unsignedInteger('break_minutes')->default(60);
                $table->unsignedInteger('grace_minutes')->default(15);
                $table->decimal('working_hours', 4, 2)->default(8.00);
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('work_schedule_days')) {
            Schema::create('work_schedule_days', function (Blueprint $table) {
                $table->id();
                $table->foreignId('work_schedule_id')->constrained('work_schedules')->cascadeOnDelete();
                $table->unsignedTinyInteger('day_of_week'); // 0 = Sunday, 1 = Monday, ... 6 = Saturday
                $table->boolean('is_working_day')->default(true);
                $table->unique(['work_schedule_id', 'day_of_week']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedule_days');
        Schema::dropIfExists('work_schedules');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
    }
};
