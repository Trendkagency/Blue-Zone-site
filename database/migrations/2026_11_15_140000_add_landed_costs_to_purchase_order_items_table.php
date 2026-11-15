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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'allocated_landed_cost')) {
                $table->decimal('allocated_landed_cost', 12, 2)->default(0.00)->after('total_price');
            }
            if (!Schema::hasColumn('purchase_order_items', 'adjustment_cost')) {
                $table->decimal('adjustment_cost', 12, 2)->default(0.00)->after('allocated_landed_cost');
            }
            if (!Schema::hasColumn('purchase_order_items', 'landed_unit_cost')) {
                $table->decimal('landed_unit_cost', 12, 4)->default(0.0000)->after('adjustment_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['allocated_landed_cost', 'adjustment_cost', 'landed_unit_cost']);
        });
    }
};
