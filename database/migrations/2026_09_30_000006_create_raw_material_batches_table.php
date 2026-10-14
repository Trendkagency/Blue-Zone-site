<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->string('batch_number', 80)->unique();
            $table->string('supplier_lot', 80)->index();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('goods_receipt_id')->nullable()->constrained('goods_receipts')->nullOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('location_id', 50)->default('central_wh');
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnUpdate();
            $table->string('storage_bin', 50)->nullable();
            
            // Invariant quantities: current_qty = available_qty + reserved_qty + quarantine_qty
            $table->decimal('initial_qty', 12, 4);
            $table->decimal('current_qty', 12, 4);
            $table->decimal('available_qty', 12, 4)->default(0.0000);
            $table->decimal('reserved_qty', 12, 4)->default(0.0000);
            $table->decimal('quarantine_qty', 12, 4)->default(0.0000);
            
            $table->decimal('unit_cost', 12, 4);
            $table->date('manufacture_date');
            $table->date('expiry_date')->index();
            $table->date('retest_date')->nullable()->index();
            $table->string('status', 30)->default('quarantine')->index();

            $table->softDeletes();
            $table->timestamps();

            // Strict composite indexes required by prompt for FEFO and Reports
            $table->index(['status', 'expiry_date'], 'rm_batch_status_expiry_idx');
            $table->index(['raw_material_id', 'status', 'expiry_date'], 'rm_batch_material_status_expiry_idx');
            $table->index(['supplier_id', 'created_at'], 'rm_batch_supplier_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_batches');
    }
};
