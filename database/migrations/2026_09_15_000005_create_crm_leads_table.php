<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_leads')) {
            Schema::create('crm_leads', function (Blueprint $table) {
                $table->id();
                $table->string('lead_number', 50)->unique();

                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->foreignId('company_id')->nullable()->constrained('crm_companies')->nullOnDelete();

                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('full_name');

                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable()->index();
                $table->string('phone_normalized')->nullable()->index();
                $table->string('secondary_phone')->nullable();

                $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
                $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();

                $table->string('job_title')->nullable();
                $table->string('company_name')->nullable();

                $table->foreignId('source_id')->nullable()->constrained('crm_lead_sources')->nullOnDelete();
                $table->foreignId('campaign_id')->nullable()->constrained('crm_campaigns')->nullOnDelete();

                $table->string('status', 30)->default('new')->index(); // new, contacted, qualified, unqualified, converted, lost
                $table->string('stage', 50)->nullable();
                $table->string('priority', 20)->default('normal')->index(); // low, normal, high, urgent

                $table->decimal('estimated_value', 12, 2)->nullable();
                $table->string('currency', 10)->default('SAR');

                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('assigned_at')->nullable();

                $table->dateTime('last_contacted_at')->nullable();
                $table->dateTime('next_follow_up_at')->nullable()->index();

                $table->dateTime('converted_at')->nullable();
                $table->unsignedBigInteger('converted_customer_id')->nullable()->index();
                $table->unsignedBigInteger('converted_opportunity_id')->nullable()->index();

                $table->text('lost_reason')->nullable();
                $table->text('notes')->nullable();
                $table->integer('score')->default(0);
                $table->json('metadata')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'owner_id']);
                $table->index(['status', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
