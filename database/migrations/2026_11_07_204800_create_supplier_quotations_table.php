<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('supplier_quotations')) {
            Schema::create('supplier_quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quote_number', 50)->unique();
                $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
                $table->string('rfq_reference', 100)->nullable();
                $table->date('quote_date');
                $table->date('valid_until')->nullable();
                $table->string('status', 30)->default('draft')->index(); // draft, pending, accepted, rejected, converted
                $table->string('currency', 3)->default('SAR');
                $table->decimal('exchange_rate', 12, 4)->default(1.0000);
                $table->decimal('subtotal', 14, 2)->default(0.00);
                $table->decimal('tax_amount', 14, 2)->default(0.00);
                $table->decimal('estimated_freight', 14, 2)->default(0.00);
                $table->decimal('estimated_customs', 14, 2)->default(0.00);
                $table->decimal('grand_total', 14, 2)->default(0.00);
                $table->string('payment_terms', 100)->nullable();
                $table->unsignedInteger('lead_time_days')->default(14);
                $table->text('notes')->nullable();
                $table->foreignId('converted_po_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->softDeletes();
                $table->timestamps();

                $table->index(['supplier_id', 'status']);
                $table->index(['quote_date', 'valid_until']);
            });
        }

        if (!Schema::hasTable('supplier_quotation_items')) {
            Schema::create('supplier_quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_quotation_id')->constrained('supplier_quotations')->cascadeOnDelete();
                $table->foreignId('raw_material_id')->nullable()->constrained('raw_materials')->nullOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('item_type', 30)->default('raw_material'); // raw_material, packaging, product
                $table->string('item_name', 255);
                $table->decimal('quantity', 14, 4)->default(1.0000);
                $table->decimal('unit_price', 14, 4)->default(0.0000);
                $table->decimal('tax_rate', 5, 2)->default(15.00);
                $table->decimal('tax_amount', 14, 2)->default(0.00);
                $table->decimal('total_price', 14, 2)->default(0.00);
                $table->string('notes', 255)->nullable();
                $table->timestamps();
            });
        }

        // Add supplier_quotation_id to purchase_orders if not present
        if (Schema::hasTable('purchase_orders') && !Schema::hasColumn('purchase_orders', 'supplier_quotation_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->foreignId('supplier_quotation_id')->nullable()->after('supplier_id')->constrained('supplier_quotations')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('purchase_orders') && Schema::hasColumn('purchase_orders', 'supplier_quotation_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropForeign(['supplier_quotation_id']);
                $table->dropColumn('supplier_quotation_id');
            });
        }

        Schema::dropIfExists('supplier_quotation_items');
        Schema::dropIfExists('supplier_quotations');
    }
};
