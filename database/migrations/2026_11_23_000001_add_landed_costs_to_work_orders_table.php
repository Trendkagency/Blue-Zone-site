<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('work_orders', 'landed_costs')) {
                $table->json('landed_costs')->nullable()->after('total_material_cost');
            }
            if (!Schema::hasColumn('work_orders', 'total_landed_cost')) {
                $table->decimal('total_landed_cost', 14, 4)->default(0.0000)->after('landed_costs');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            if (Schema::hasColumn('work_orders', 'total_landed_cost')) {
                $table->dropColumn('total_landed_cost');
            }
            if (Schema::hasColumn('work_orders', 'landed_costs')) {
                $table->dropColumn('landed_costs');
            }
        });
    }
};
