<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_pipelines')) {
            Schema::create('crm_pipelines', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('crm_pipeline_stages')) {
            Schema::create('crm_pipeline_stages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pipeline_id')->constrained('crm_pipelines')->cascadeOnDelete();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug');
                $table->unsignedTinyInteger('probability')->default(0); // 0 - 100 %
                $table->boolean('is_won')->default(false);
                $table->boolean('is_lost')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->string('color', 20)->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['pipeline_id', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_pipeline_stages');
        Schema::dropIfExists('crm_pipelines');
    }
};
