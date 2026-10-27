<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('locations')) {
            DB::table('locations')
                ->where('id', 'offline')
                ->orWhere('code', 'LOC-POS')
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('locations')) {
            DB::table('locations')
                ->where('id', 'offline')
                ->orWhere('code', 'LOC-POS')
                ->update([
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
        }
    }
};
