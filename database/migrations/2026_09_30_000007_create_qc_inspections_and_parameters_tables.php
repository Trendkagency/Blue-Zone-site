<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qc_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_code', 50)->unique();
            $table->foreignId('raw_material_batch_id')->constrained('raw_material_batches')->cascadeOnDelete();
            $table->decimal('sample_size', 10, 4);
            $table->string('sample_uom', 10)->default('g');
            
            // Dual sign-off: verifier_id must differ from inspector_id
            $table->foreignId('inspector_id')->constrained('users');
            $table->foreignId('verifier_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('verdict', 30)->default('pending')->index();
            $table->text('waiver_reason')->nullable();
            $table->date('waiver_expires_at')->nullable();
            $table->timestamp('tested_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['verdict', 'tested_at']);
        });

        Schema::create('qc_inspection_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_inspection_id')->constrained('qc_inspections')->cascadeOnDelete();
            $table->string('parameter_name', 100);
            $table->string('specification', 150);
            $table->string('result_value', 150);
            $table->string('status', 20)->default('pass'); // pass, fail
            $table->timestamps();

            $table->index(['qc_inspection_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qc_inspection_parameters');
        Schema::dropIfExists('qc_inspections');
    }
};
