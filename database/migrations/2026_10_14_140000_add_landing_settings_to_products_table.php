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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('show_on_landing')->default(true)->after('is_featured')->index();
            $table->integer('landing_order')->default(0)->after('show_on_landing');
            $table->string('landing_badge_en', 150)->nullable()->after('landing_order');
            $table->string('landing_badge_ar', 150)->nullable()->after('landing_badge_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['show_on_landing', 'landing_order', 'landing_badge_en', 'landing_badge_ar']);
        });
    }
};
