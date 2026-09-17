<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_activities')) {
            Schema::create('crm_activities', function (Blueprint $table) {
                $table->id();
                $table->string('activity_type', 30)->index(); // task, call, meeting, email, whatsapp, note, follow_up, visit, other
                $table->string('subject');
                $table->text('description')->nullable();

                $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->foreignId('company_id')->nullable()->constrained('crm_companies')->nullOnDelete();
                $table->foreignId('opportunity_id')->nullable()->constrained('crm_opportunities')->nullOnDelete();

                $table->foreignId('assigned_to')->constrained('users');
                $table->foreignId('created_by')->constrained('users');

                $table->dateTime('scheduled_at')->nullable();
                $table->dateTime('started_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->dateTime('due_at')->nullable()->index();

                $table->string('status', 30)->default('pending')->index(); // pending, scheduled, completed, cancelled, overdue
                $table->string('priority', 20)->default('normal')->index(); // low, normal, high, urgent

                $table->text('outcome')->nullable();
                $table->json('metadata')->nullable();

                $table->timestamps();

                $table->index(['assigned_to', 'status']);
                $table->index(['activity_type', 'status']);
                $table->index(['due_at', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
    }
};
