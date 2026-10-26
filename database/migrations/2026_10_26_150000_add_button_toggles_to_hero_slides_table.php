<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            if (!Schema::hasColumn('hero_slides', 'show_primary_cta')) {
                $table->boolean('show_primary_cta')->default(true)->after('primary_cta_style');
            }
            if (!Schema::hasColumn('hero_slides', 'show_secondary_cta')) {
                $table->boolean('show_secondary_cta')->default(true)->after('secondary_cta_style');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            if (Schema::hasColumn('hero_slides', 'show_primary_cta')) {
                $table->dropColumn('show_primary_cta');
            }
            if (Schema::hasColumn('hero_slides', 'show_secondary_cta')) {
                $table->dropColumn('show_secondary_cta');
            }
        });
    }
};
