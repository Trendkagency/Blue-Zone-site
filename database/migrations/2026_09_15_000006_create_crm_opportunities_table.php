<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_opportunities')) {
            Schema::create('crm_opportunities', function (Blueprint $table) {
                $table->id();
                $table->string('opportunity_number', 50)->unique();

                $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->foreignId('company_id')->nullable()->constrained('crm_companies')->nullOnDelete();

                $table->string('name');
                $table->text('description')->nullable();

                $table->foreignId('pipeline_id')->constrained('crm_pipelines')->cascadeOnDelete();
                $table->foreignId('stage_id')->constrained('crm_pipeline_stages')->cascadeOnDelete();

                $table->foreignId('owner_id')->constrained('users');
                $table->foreignId('source_id')->nullable()->constrained('crm_lead_sources')->nullOnDelete();
                $table->foreignId('campaign_id')->nullable()->constrained('crm_campaigns')->nullOnDelete();

                $table->decimal('value', 14, 2)->default(0.00);
                $table->string('currency', 10)->default('SAR');
                $table->unsignedTinyInteger('probability')->default(0); // 0-100 %

                $table->date('expected_close_date')->nullable()->index();
                $table->string('status', 30)->default('open')->index(); // open, won, lost

                $table->dateTime('won_at')->nullable();
                $table->dateTime('lost_at')->nullable();
                $table->text('lost_reason')->nullable();

                $table->dateTime('last_activity_at')->nullable();
                $table->dateTime('next_follow_up_at')->nullable();

                $table->json('metadata')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['pipeline_id', 'stage_id']);
                $table->index(['status', 'owner_id']);
                $table->index(['customer_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_opportunities');
    }
};
