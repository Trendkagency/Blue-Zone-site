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
        Schema::table('collection_entries', function (Blueprint $table) {
            $table->string('source_type', 30)->default('customer')->after('description'); // customer, supplier, asset, other
            $table->foreignId('supplier_id')->nullable()->after('customer_id')->constrained('suppliers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collection_entries', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['source_type', 'supplier_id']);
        });
    }
};
