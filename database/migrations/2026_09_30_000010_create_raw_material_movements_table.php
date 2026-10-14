<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_batch_id')->constrained('raw_material_batches')->cascadeOnDelete();
            $table->string('movement_type', 30)->index();
            $table->string('from_location', 100);
            $table->string('to_location', 100);
            $table->decimal('quantity', 12, 4);
            $table->decimal('previous_qty', 12, 4);
            $table->decimal('new_qty', 12, 4);
            $table->foreignId('user_id')->constrained('users');
            $table->string('reference_type', 50)->nullable();
            $table->string('reference_id', 50)->nullable();
            $table->string('note', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['raw_material_batch_id', 'movement_type', 'created_at'], 'rmm_batch_type_created_idx');
            $table->index(['reference_type', 'reference_id'], 'rmm_ref_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_movements');
    }
};
