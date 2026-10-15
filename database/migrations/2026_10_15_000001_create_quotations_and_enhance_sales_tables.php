<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add type column to products table if not present
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('type')->default('product')->after('category_id'); // product, service
            });
        }

        // 2. Create quotations table
        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quotation_number')->unique();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('customer_name');
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->string('company_name')->nullable();
                $table->date('date');
                $table->date('valid_until')->nullable();
                $table->string('status')->default('Draft'); // Draft, Sent, Approved, Converted, Expired, Rejected
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('tax', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->string('currency')->default('SAR');
                $table->text('terms_conditions')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('converted_order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 3. Create quotation_items table
        if (!Schema::hasTable('quotation_items')) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('item_type')->default('product'); // product, service
                $table->string('item_name');
                $table->string('sku')->nullable();
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(15.00);
                $table->decimal('total', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
