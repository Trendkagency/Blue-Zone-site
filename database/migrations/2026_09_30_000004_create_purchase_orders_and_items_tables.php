<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->date('order_date')->index();
            $table->date('expected_date')->nullable()->index();
            $table->string('destination_location_id', 50)->default('central_wh');
            $table->foreign('destination_location_id')->references('id')->on('locations')->cascadeOnUpdate();
            $table->string('status', 30)->default('draft')->index();
            $table->decimal('subtotal', 14, 2)->default(0.00);
            $table->decimal('freight_cost', 14, 2)->default(0.00);
            $table->decimal('customs_duties', 14, 2)->default(0.00);
            $table->decimal('tax_amount', 14, 2)->default(0.00);
            $table->decimal('grand_total', 14, 2)->default(0.00);
            $table->string('currency', 3)->default('AED');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('supplier_reference', 100)->nullable();
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Composite indexes for reports
            $table->index(['supplier_id', 'order_date']);
            $table->index(['status', 'expected_date']);
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->decimal('ordered_qty', 12, 4);
            $table->decimal('received_qty', 12, 4)->default(0.0000);
            $table->decimal('unit_price', 12, 4);
            $table->decimal('total_price', 14, 2);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['purchase_order_id', 'raw_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
