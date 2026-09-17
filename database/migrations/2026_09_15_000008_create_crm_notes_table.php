<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_notes')) {
            Schema::create('crm_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

                $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->cascadeOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->cascadeOnDelete();
                $table->foreignId('company_id')->nullable()->constrained('crm_companies')->cascadeOnDelete();
                $table->foreignId('opportunity_id')->nullable()->constrained('crm_opportunities')->cascadeOnDelete();

                $table->text('body');
                $table->boolean('is_private')->default(false);

                $table->timestamps();

                $table->index(['lead_id', 'created_at']);
                $table->index(['customer_id', 'created_at']);
                $table->index(['opportunity_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_notes');
    }
};
