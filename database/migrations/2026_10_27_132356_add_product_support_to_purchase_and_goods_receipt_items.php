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
            $table->string('item_type', 30)->default('raw_material')->after('purchase_order_id');
            $table->foreignId('raw_material_id')->nullable()->change();
            $table->foreignId('product_id')->nullable()->after('raw_material_id')->constrained('products')->nullOnDelete();
        });

        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->string('item_type', 30)->default('raw_material')->after('purchase_order_item_id');
            $table->foreignId('raw_material_id')->nullable()->change();
            $table->foreignId('product_id')->nullable()->after('raw_material_id')->constrained('products')->nullOnDelete();
            $table->string('storage_bin', 60)->nullable()->after('storage_condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            if (Schema::hasColumn('goods_receipt_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn(['product_id', 'item_type', 'storage_bin']);
            }
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn(['product_id', 'item_type']);
            }
        });
    }
};
