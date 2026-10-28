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
        Schema::create('payment_entries', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique()->index();
            $table->date('entry_date')->index();
            $table->string('category', 60)->index(); // purchase, transportation_logistics, operating_expenses, etc.
            $table->text('description')->nullable();
            $table->string('payee_type', 30)->default('supplier'); // supplier, general_payee, employee
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('purchase_invoice_id')->nullable()->constrained('purchase_invoices')->nullOnDelete();
            $table->string('supplier_code', 50)->nullable()->index();
            $table->string('payee_name', 255)->index();
            $table->decimal('amount_excl_vat', 15, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(15.00);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->foreignId('financial_account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            $table->string('payment_method', 50)->default('bank_transfer'); // bank_transfer, cash, cheque, corporate_card
            $table->string('cheque_number', 100)->nullable();
            $table->string('cheque_bank', 100)->nullable();
            $table->date('cheque_due_date')->nullable();
            $table->string('status', 30)->default('posted'); // posted, pending_clearance, voided
            $table->timestamp('cleared_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_entries');
    }
};
