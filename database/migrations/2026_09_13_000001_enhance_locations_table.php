<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('locations')) {
            Schema::table('locations', function (Blueprint $table) {
                if (!Schema::hasColumn('locations', 'city')) {
                    $table->string('city')->nullable()->after('address');
                }
                if (!Schema::hasColumn('locations', 'manager_name')) {
                    $table->string('manager_name')->nullable()->after('city');
                }
                if (!Schema::hasColumn('locations', 'phone')) {
                    $table->string('phone')->nullable()->after('manager_name');
                }
                if (!Schema::hasColumn('locations', 'email')) {
                    $table->string('email')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('locations', 'notes')) {
                    $table->text('notes')->nullable()->after('email');
                }
                if (!Schema::hasColumn('locations', 'capacity_units')) {
                    $table->integer('capacity_units')->default(10000)->after('notes');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('locations')) {
            Schema::table('locations', function (Blueprint $table) {
                $columns = ['city', 'manager_name', 'phone', 'email', 'notes', 'capacity_units'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('locations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
