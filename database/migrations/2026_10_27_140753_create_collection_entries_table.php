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
        Schema::create('collection_entries', function (Blueprint $table) {
            $table->id();
            $table->string('collection_number', 50)->unique();
            $table->date('entry_date');
            $table->string('category', 50)->default('sales'); // non_operational_revenue, sales, online_sales_auto, equities
            $table->text('description')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_code', 50)->nullable();
            $table->string('customer_source', 255)->nullable();
            $table->decimal('amount_excl_vat', 15, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(15.00);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->foreignId('financial_account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            $table->string('payment_method', 50)->default('bank_transfer'); // bank 1, bank 2, cash, cheque, online_gateway
            $table->string('cheque_number', 100)->nullable();
            $table->string('cheque_bank', 100)->nullable();
            $table->date('cheque_due_date')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->boolean('is_auto_synced')->default(false);
            $table->string('status', 30)->default('posted'); // posted, draft, void
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['entry_date', 'category']);
            $table->index(['order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_entries');
    }
};
