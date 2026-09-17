<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_campaigns')) {
            Schema::create('crm_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('type', 30)->default('social')->index();
                $table->string('status', 30)->default('active')->index();
                $table->dateTime('start_at')->nullable();
                $table->dateTime('end_at')->nullable();
                $table->decimal('budget', 12, 2)->default(0.00);
                $table->string('currency', 10)->default('SAR');
                
                // UTM Parameters for Attribution
                $table->string('utm_source')->nullable()->index();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable()->index();
                $table->string('utm_content')->nullable();
                $table->string('utm_term')->nullable();

                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedInteger('target_leads')->default(0);
                $table->unsignedInteger('converted_leads')->default(0);
                $table->decimal('revenue_generated', 14, 2)->default(0.00);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_campaigns');
    }
};
