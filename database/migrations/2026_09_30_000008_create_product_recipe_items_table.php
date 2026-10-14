<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->decimal('quantity_per_unit', 12, 6);
            $table->decimal('loss_percentage', 5, 2)->default(1.50);
            $table->boolean('is_critical')->default(true);
            $table->string('instructions', 255)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'raw_material_id']);
            $table->index(['product_id', 'is_critical']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recipe_items');
    }
};
