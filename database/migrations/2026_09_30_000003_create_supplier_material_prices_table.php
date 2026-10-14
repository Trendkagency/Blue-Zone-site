<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_material_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->decimal('unit_price', 12, 4);
            $table->string('currency', 3)->default('AED');
            $table->decimal('min_order_qty', 12, 4)->default(1.0000);
            $table->unsignedInteger('lead_time_days')->default(14);
            $table->boolean('is_preferred')->default(false);
            $table->string('supplier_sku', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'raw_material_id'], 'supplier_raw_material_unique');
            $table->index(['raw_material_id', 'is_preferred']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_material_prices');
    }
};
