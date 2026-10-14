<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name_en', 191);
            $table->string('name_ar', 191);
            $table->string('inci_name', 191)->nullable();
            $table->string('cas_number', 50)->nullable()->index();
            $table->string('category', 30)->default('active_ingredient')->index();
            $table->string('unit_of_measure', 10)->default('kg');
            $table->string('storage_condition', 30)->default('ambient_15_25c');
            $table->decimal('standard_cost', 12, 4)->default(0.0000);
            $table->decimal('min_reorder_qty', 12, 4)->default(0.0000);
            $table->decimal('safety_stock', 12, 4)->default(0.0000);
            $table->unsignedInteger('shelf_life_days')->default(730); // 2 years default
            $table->boolean('requires_coa')->default(true);
            $table->foreignId('default_supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();

            $table->softDeletes();
            $table->timestamps();

            // Composite indexes for MRP and search performance
            $table->index(['category', 'is_active']);
            $table->index(['name_en', 'name_ar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
