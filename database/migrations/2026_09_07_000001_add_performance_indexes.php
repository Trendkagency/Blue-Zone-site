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
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'is_active', 'deleted_at'], 'products_status_active_idx');
            $table->index(['is_featured', 'status'], 'products_featured_status_idx');
            $table->index(['is_best_seller', 'status'], 'products_bestseller_status_idx');
            $table->index(['category_id', 'status'], 'products_category_status_idx');
            $table->index('sort_order', 'products_sort_order_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['is_active', 'deleted_at'], 'categories_active_idx');
            $table->index(['parent_id', 'sort_order'], 'categories_parent_sort_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'orders_status_created_idx');
            $table->index('payment_status', 'orders_payment_status_idx');
            $table->index(['customer_id', 'created_at'], 'orders_customer_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_active_idx');
            $table->dropIndex('products_featured_status_idx');
            $table->dropIndex('products_bestseller_status_idx');
            $table->dropIndex('products_category_status_idx');
            $table->dropIndex('products_sort_order_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_active_idx');
            $table->dropIndex('categories_parent_sort_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_created_idx');
            $table->dropIndex('orders_payment_status_idx');
            $table->dropIndex('orders_customer_created_idx');
        });
    }
};
