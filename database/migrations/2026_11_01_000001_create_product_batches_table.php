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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('batch_number', 80)->unique();
            $table->string('supplier_lot', 80)->nullable();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('goods_receipt_id')->nullable()->constrained('goods_receipts')->nullOnDelete();
            $table->string('location_id', 50)->default('central_wh');
            $table->string('storage_bin', 60)->nullable();
            
            $table->integer('initial_qty')->default(0);
            $table->integer('current_qty')->default(0);
            $table->integer('available_qty')->default(0);
            $table->integer('reserved_qty')->default(0);
            $table->integer('quarantine_qty')->default(0);
            
            $table->decimal('unit_cost', 12, 4)->default(0.0000);
            $table->date('manufacture_date');
            $table->date('expiry_date');
            
            $table->string('status', 30)->default('approved'); // approved, quarantine, rejected, recalled, expired
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'status', 'expiry_date'], 'pb_product_status_expiry_idx');
            $table->index(['location_id', 'status'], 'pb_location_status_idx');
            $table->index('batch_number', 'pb_batch_number_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
