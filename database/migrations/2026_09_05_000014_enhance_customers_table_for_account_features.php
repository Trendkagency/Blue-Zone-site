<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'saved_addresses')) {
                $table->json('saved_addresses')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('customers', 'wishlist')) {
                $table->json('wishlist')->nullable()->after('saved_addresses');
            }
            if (!Schema::hasColumn('customers', 'loyalty_points')) {
                $table->integer('loyalty_points')->default(100)->after('total_spent');
            }
            if (!Schema::hasColumn('customers', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('customers', 'saved_addresses')) {
                $columns[] = 'saved_addresses';
            }
            if (Schema::hasColumn('customers', 'wishlist')) {
                $columns[] = 'wishlist';
            }
            if (Schema::hasColumn('customers', 'loyalty_points')) {
                $columns[] = 'loyalty_points';
            }
            if (Schema::hasColumn('customers', 'notification_preferences')) {
                $columns[] = 'notification_preferences';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
