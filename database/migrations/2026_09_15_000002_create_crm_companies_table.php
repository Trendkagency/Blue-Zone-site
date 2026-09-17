<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_companies')) {
            Schema::create('crm_companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('legal_name')->nullable();
                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable()->index();
                $table->string('website')->nullable();
                $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
                $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
                $table->text('address')->nullable();
                $table->string('industry')->nullable();
                $table->string('company_size')->nullable(); // e.g. 1-10, 11-50, 51-200, 200+
                $table->string('tax_number')->nullable();
                $table->string('registration_number')->nullable();
                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 30)->default('active')->index();
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_companies');
    }
};
