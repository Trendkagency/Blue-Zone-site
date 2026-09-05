<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('location_id'); // online, offline, central_wh
            $table->string('location_name_en');
            $table->string('location_name_ar')->nullable();
            $table->string('variant_en')->nullable();
            $table->string('variant_ar')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('available_stock')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->integer('low_stock_threshold')->default(15);
            $table->string('status')->default('in_stock'); // in_stock, low_stock, out_of_stock
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('retail_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
