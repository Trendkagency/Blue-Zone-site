<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_tags')) {
            Schema::create('crm_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->string('color', 20)->default('#0A4F78');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_taggables')) {
            Schema::create('crm_taggables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tag_id')->constrained('crm_tags')->cascadeOnDelete();
                $table->unsignedBigInteger('taggable_id');
                $table->string('taggable_type');
                $table->timestamps();

                $table->unique(['tag_id', 'taggable_id', 'taggable_type'], 'crm_taggables_unique');
                $table->index(['taggable_type', 'taggable_id'], 'crm_taggables_morph_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_taggables');
        Schema::dropIfExists('crm_tags');
    }
};
