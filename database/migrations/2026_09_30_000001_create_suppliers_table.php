<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('type', 30)->default('ingredient_supplier')->index();
            $table->string('name_en', 191);
            $table->string('name_ar', 191);
            $table->string('contact_name', 100)->nullable();
            $table->string('email', 150);
            $table->string('phone', 50)->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('city', 100)->nullable();
            $table->text('address')->nullable();
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->boolean('gmp_certified')->default(false);
            $table->boolean('iso_certified')->default(false);
            $table->date('audit_expiry')->nullable()->index();
            $table->string('payment_terms', 50)->default('NET30');
            $table->string('currency', 3)->default('AED');
            $table->unsignedInteger('lead_time_days')->default(14);
            $table->boolean('is_active')->default(false)->index();

            // Dual Sign-Off for Supplier Activation
            $table->foreignId('qa_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('qa_approved_at')->nullable();
            $table->foreignId('procurement_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('procurement_approved_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Indexes for fast reporting & searching
            $table->index(['is_active', 'type']);
            $table->index(['name_en', 'name_ar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
