<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_segments')) {
            Schema::create('crm_segments', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->json('rules')->nullable(); // stored rule conditions
                $table->unsignedInteger('customer_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->dateTime('last_evaluated_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_segments');
    }
};
