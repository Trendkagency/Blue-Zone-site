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
            $table->integer('shelf_life_months')->default(24)->after('low_stock_threshold');
            $table->string('cost_calculation_mode', 30)->default('auto_bom')->after('shelf_life_months'); // auto_bom, manual_locked
            $table->decimal('packaging_overhead_cost', 12, 4)->default(0.0000)->after('cost_calculation_mode');
            $table->decimal('labor_overhead_cost', 12, 4)->default(0.0000)->after('packaging_overhead_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'shelf_life_months',
                'cost_calculation_mode',
                'packaging_overhead_cost',
                'labor_overhead_cost',
            ]);
        });
    }
};
