<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mr_visits', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('cycle_id')->constrained('products')->nullOnDelete();
        });

        Schema::create('mr_visit_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('mr_visits')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['visit_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_visit_products');

        Schema::table('mr_visits', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};
