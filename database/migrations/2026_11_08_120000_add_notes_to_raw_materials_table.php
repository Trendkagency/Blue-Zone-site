<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            if (!Schema::hasColumn('raw_materials', 'notes')) {
                $table->text('notes')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            if (Schema::hasColumn('raw_materials', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
