<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('product_id')->constrained('products');
            $table->string('target_finished_lot', 80)->unique();
            $table->unsignedInteger('planned_qty');
            $table->unsignedInteger('produced_qty')->default(0);
            $table->unsignedInteger('rejected_qty')->default(0);
            $table->string('status', 30)->default('draft')->index();
            $table->string('location_id', 50)->default('central_wh');
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnUpdate();
            $table->dateTime('scheduled_start')->index();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->decimal('total_material_cost', 14, 4)->default(0.0000);
            $table->decimal('unit_cost', 12, 4)->default(0.0000);
            $table->foreignId('created_by')->constrained('users');
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['product_id', 'status']);
        });

        // Immutable append-only consumption log
        Schema::create('work_order_material_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->foreignId('raw_material_batch_id')->constrained('raw_material_batches');
            $table->decimal('quantity', 12, 4);
            $table->foreignId('operator_id')->constrained('users');
            $table->string('scanned_barcode', 100)->index();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['work_order_id', 'raw_material_batch_id'], 'womc_order_batch_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_material_consumptions');
        Schema::dropIfExists('work_orders');
    }
};
