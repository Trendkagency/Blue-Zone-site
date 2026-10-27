<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Strip offline sales permissions from roles JSON column
        if (Schema::hasTable('roles')) {
            $roles = DB::table('roles')->get();
            foreach ($roles as $r) {
                if (empty($r->permissions)) {
                    continue;
                }
                $perms = is_string($r->permissions) ? json_decode($r->permissions, true) : $r->permissions;
                if (!is_array($perms)) {
                    continue;
                }

                $filtered = array_values(array_filter($perms, function ($p) {
                    return !in_array($p, [
                        'manage_offline_sales',
                        'offline_sales.*',
                        'offline_sales.view',
                        'offline_sales.create',
                        'offline_sales.edit',
                        'offline_sales.delete',
                        'offline_sales',
                        'pos',
                        'pos.*',
                    ], true);
                }));

                if (count($filtered) !== count($perms)) {
                    DB::table('roles')->where('id', $r->id)->update([
                        'permissions' => json_encode($filtered),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 2. Strip from users JSON column if present
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'permissions')) {
            $users = DB::table('users')->whereNotNull('permissions')->get();
            foreach ($users as $u) {
                $perms = is_string($u->permissions) ? json_decode($u->permissions, true) : $u->permissions;
                if (!is_array($perms)) {
                    continue;
                }

                $filtered = array_values(array_filter($perms, function ($p) {
                    return !in_array($p, [
                        'manage_offline_sales',
                        'offline_sales.*',
                        'offline_sales.view',
                        'offline_sales.create',
                        'offline_sales.edit',
                        'offline_sales.delete',
                        'offline_sales',
                        'pos',
                        'pos.*',
                    ], true);
                }));

                if (count($filtered) !== count($perms)) {
                    DB::table('users')->where('id', $u->id)->update([
                        'permissions' => json_encode($filtered),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3. Spatie permissions table fallback if exists
        if (Schema::hasTable('permissions')) {
            $permIds = DB::table('permissions')
                ->where('name', 'like', '%offline_sales%')
                ->orWhere('name', 'like', '%manage_offline_sales%')
                ->pluck('id');

            if (Schema::hasTable('role_has_permissions')) {
                DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
            }
            if (Schema::hasTable('model_has_permissions')) {
                DB::table('model_has_permissions')->whereIn('permission_id', $permIds)->delete();
            }
            DB::table('permissions')->whereIn('id', $permIds)->delete();
        }

        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public function down(): void
    {
        // Re-adding permissions in down() if needed
    }
};
