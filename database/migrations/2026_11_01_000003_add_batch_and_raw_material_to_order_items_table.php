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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('item_type', 30)->default('product')->after('order_id'); // product, raw_material
            $table->foreignId('raw_material_id')->nullable()->after('product_id')->constrained('raw_materials')->nullOnDelete();
            $table->foreignId('product_batch_id')->nullable()->after('raw_material_id')->constrained('product_batches')->nullOnDelete();
            $table->foreignId('raw_material_batch_id')->nullable()->after('product_batch_id')->constrained('raw_material_batches')->nullOnDelete();
            $table->string('batch_number', 80)->nullable()->after('raw_material_batch_id');
            $table->date('expiry_date')->nullable()->after('batch_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['raw_material_id']);
            $table->dropForeign(['product_batch_id']);
            $table->dropForeign(['raw_material_batch_id']);
            $table->dropColumn([
                'item_type',
                'raw_material_id',
                'product_batch_id',
                'raw_material_batch_id',
                'batch_number',
                'expiry_date',
            ]);
        });
    }
};
