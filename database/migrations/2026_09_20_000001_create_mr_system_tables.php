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
        // 1. Contact Specialties lookup
        Schema::create('contact_specialties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Configurable Contact Classifications (A+, A, B, C...)
        Schema::create('contact_classifications', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('required_visits')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Medical Contacts (Doctors / Clinics)
        Schema::create('mr_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('specialty_id')->nullable()->constrained('contact_specialties')->nullOnDelete();
            $table->foreignId('classification_id')->nullable()->constrained('contact_classifications')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('region')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('hospital_clinic_name')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['latitude', 'longitude']);
            $table->index('is_active');
        });

        // 4. GPS Configurations (Global & Per-MR override)
        Schema::create('mr_gps_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('allowed_radius_m')->default(150);
            $table->boolean('is_gps_required')->default(true);
            $table->boolean('mock_detection_enabled')->default(true);
            $table->timestamps();
        });

        // 5. Visit Cycles (e.g. Monthly / Quarterly)
        Schema::create('mr_visit_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active'); // active, upcoming, closed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // 6. Contact Assignments (Rep to Doctor portfolio per Cycle)
        Schema::create('mr_contact_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_id')->constrained('mr_visit_cycles')->cascadeOnDelete();
            $table->foreignId('mr_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('mr_contacts')->cascadeOnDelete();
            $table->unsignedInteger('target_visits')->default(1);
            $table->unsignedInteger('visits_done')->default(0);
            $table->unsignedInteger('target_points')->default(0);
            $table->unsignedInteger('achieved_points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['cycle_id', 'mr_id', 'contact_id'], 'mr_assignment_unique');
            $table->index(['cycle_id', 'mr_id']);
        });

        // 7. Scheduled Visits (MR Calendar slots)
        Schema::create('mr_scheduled_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->nullable()->constrained('mr_contact_assignments')->cascadeOnDelete();
            $table->foreignId('mr_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('mr_contacts')->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('mr_visit_cycles')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('planned'); // planned, rescheduled, cancelled, completed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['mr_id', 'scheduled_at']);
        });

        // 8. Executed Visits (GPS checkin/out tracking)
        Schema::create('mr_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_visit_id')->nullable()->constrained('mr_scheduled_visits')->nullOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('mr_contact_assignments')->nullOnDelete();
            $table->foreignId('mr_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('mr_contacts')->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('mr_visit_cycles')->cascadeOnDelete();
            $table->dateTime('checkin_at');
            $table->decimal('checkin_lat', 10, 8)->nullable();
            $table->decimal('checkin_lng', 11, 8)->nullable();
            $table->decimal('checkin_accuracy_m', 8, 2)->nullable();
            $table->dateTime('checkout_at')->nullable();
            $table->decimal('checkout_lat', 10, 8)->nullable();
            $table->decimal('checkout_lng', 11, 8)->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->decimal('distance_from_contact_m', 10, 2)->nullable();
            $table->boolean('gps_verified')->default(false);
            $table->string('gps_flag')->default('verified'); // verified, distance_exceeded, gps_disabled, mock_suspected
            $table->string('outcome')->default('completed'); // completed, cancelled, doctor_busy, sample_delivered, follow_up_needed
            $table->text('notes')->nullable();
            $table->json('device_meta')->nullable();
            $table->timestamps();

            $table->index(['mr_id', 'cycle_id', 'checkin_at']);
            $table->index('gps_verified');
        });

        // 9. Representative Daily Activity Logs
        Schema::create('mr_rep_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mr_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('mr_visit_cycles')->cascadeOnDelete();
            $table->date('log_date');
            $table->boolean('is_reported')->default(false);
            $table->dateTime('first_checkin_at')->nullable();
            $table->dateTime('last_checkout_at')->nullable();
            $table->unsignedInteger('total_visits_count')->default(0);
            $table->timestamps();

            $table->unique(['mr_id', 'log_date']);
            $table->index(['cycle_id', 'is_reported']);
        });

        // 10. Pre-computed Performance Snapshots
        Schema::create('mr_rep_performance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mr_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('mr_visit_cycles')->cascadeOnDelete();
            $table->unsignedInteger('total_assigned_contacts')->default(0);
            $table->unsignedInteger('unique_contacts_visited')->default(0);
            $table->decimal('coverage_rate_pct', 5, 2)->default(0);
            $table->unsignedInteger('planned_visits')->default(0);
            $table->unsignedInteger('visits_done')->default(0);
            $table->decimal('visit_compliance_pct', 5, 2)->default(0);
            $table->unsignedInteger('verified_visits')->default(0);
            $table->decimal('gps_accuracy_pct', 5, 2)->default(0);
            $table->unsignedInteger('target_points')->default(0);
            $table->unsignedInteger('achieved_points')->default(0);
            $table->decimal('points_achieved_pct', 5, 2)->default(0);
            $table->unsignedInteger('unreported_days_count')->default(0);
            $table->dateTime('calculated_at');
            $table->timestamps();

            $table->unique(['mr_id', 'cycle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mr_rep_performance_snapshots');
        Schema::dropIfExists('mr_rep_daily_logs');
        Schema::dropIfExists('mr_visits');
        Schema::dropIfExists('mr_scheduled_visits');
        Schema::dropIfExists('mr_contact_assignments');
        Schema::dropIfExists('mr_visit_cycles');
        Schema::dropIfExists('mr_gps_configs');
        Schema::dropIfExists('mr_contacts');
        Schema::dropIfExists('contact_classifications');
        Schema::dropIfExists('contact_specialties');
    }
};
