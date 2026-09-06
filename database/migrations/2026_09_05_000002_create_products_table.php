<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('tagline_en')->nullable();
            $table->string('tagline_ar')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subcategory_en')->nullable();
            $table->string('subcategory_ar')->nullable();
            $table->string('brand')->default('Blue Zone Bioceuticals');
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_best_seller')->default(false);
            $table->boolean('is_new')->default(false);
            $table->string('status')->default('active'); // active, inactive, draft
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->integer('stock_online')->default(0);
            $table->integer('stock_offline')->default(0);
            $table->integer('low_stock_threshold')->default(15);
            $table->text('short_description_en')->nullable();
            $table->text('short_description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('usage_en')->nullable();
            $table->text('usage_ar')->nullable();
            $table->text('science_en')->nullable();
            $table->text('science_ar')->nullable();
            $table->json('benefits_en')->nullable();
            $table->json('benefits_ar')->nullable();
            $table->json('ingredients')->nullable();
            $table->string('target_gender')->default('Unisex');
            $table->string('age_group')->default('18+');
            $table->string('product_size')->nullable();
            $table->text('clinical_mechanism')->nullable();
            $table->text('formula_details')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('warnings')->nullable();
            $table->boolean('enable_backorders')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
