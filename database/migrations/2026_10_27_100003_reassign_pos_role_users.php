<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles') || !Schema::hasTable('users')) {
            return;
        }

        // Target replacement role: Sales Staff (default ID: 3)
        $salesStaffRole = DB::table('roles')->where('id', 3)->first() 
            ?? DB::table('roles')->where('name', 'like', '%Sales%')->first();

        if (!$salesStaffRole) {
            return;
        }

        $posRoles = DB::table('roles')
            ->where('name', 'like', '%POS%')
            ->orWhere('name', 'like', '%Retail%')
            ->orWhere('name', 'like', '%نقاط البيع%')
            ->get();

        foreach ($posRoles as $role) {
            DB::table('users')
                ->where('role_id', $role->id)
                ->update([
                    'role_id' => $salesStaffRole->id,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Reassignment is idempotent; historical role assignments are tracked in BACKUP.md
    }
};
