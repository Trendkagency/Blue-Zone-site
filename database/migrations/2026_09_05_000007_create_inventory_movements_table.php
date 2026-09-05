<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name_en');
            $table->string('product_name_ar')->nullable();
            $table->string('sku')->nullable();
            $table->string('movement_type'); // Stock In, Stock Out, Stock Transfer, Adjustment, Sale, Return
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->integer('quantity');
            $table->integer('previous_qty')->nullable();
            $table->integer('new_qty')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('user')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
