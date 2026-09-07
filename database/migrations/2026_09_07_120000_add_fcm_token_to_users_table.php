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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'fcm_token')) {
                $table->text('fcm_token')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'fcm_device_info')) {
                $table->json('fcm_device_info')->nullable()->after('fcm_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'fcm_device_info')) {
                $table->dropColumn('fcm_device_info');
            }
            if (Schema::hasColumn('users', 'fcm_token')) {
                $table->dropColumn('fcm_token');
            }
        });
    }
};
