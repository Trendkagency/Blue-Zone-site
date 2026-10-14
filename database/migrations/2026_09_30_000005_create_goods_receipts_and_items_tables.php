<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number', 50)->unique();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('location_id', 50)->default('central_wh');
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnUpdate();
            $table->date('received_date')->index();
            $table->foreignId('receiver_id')->constrained('users');
            $table->string('carrier_name', 100)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->decimal('temperature_logger_reading', 5, 2)->nullable();
            $table->boolean('temperature_within_range')->default(true);
            $table->boolean('seal_verified')->default(true);
            $table->unsignedInteger('container_count')->default(1);
            $table->text('discrepancy_notes')->nullable();
            $table->string('status', 30)->default('received')->index();
            $table->timestamps();

            $table->index(['purchase_order_id', 'received_date']);
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->decimal('ordered_qty', 12, 4);
            $table->decimal('received_qty', 12, 4);
            $table->decimal('rejected_qty', 12, 4)->default(0.0000);
            $table->string('supplier_lot', 100);
            $table->date('manufacture_date');
            $table->date('expiry_date')->index();
            $table->string('storage_condition', 30)->default('ambient_15_25c');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
    }
};
