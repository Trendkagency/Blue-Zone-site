<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create locations table for extensible multi-location / warehouse architecture
        if (!Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->string('id')->primary(); // slug code: 'online', 'offline', 'central_wh'
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('code')->unique();
                $table->string('type')->default('warehouse'); // 'online', 'offline', 'warehouse', 'branch'
                $table->string('address')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed default core locations
            DB::table('locations')->insert([
                [
                    'id' => 'online',
                    'name_en' => 'Online Fulfillment Hub',
                    'name_ar' => 'مستودع الطلبات الإلكترونية',
                    'code' => 'LOC-ONL',
                    'type' => 'online',
                    'address' => 'Riyadh Central Logistics Park - Zone 4',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 'offline',
                    'name_en' => 'Flagship Boutique / POS',
                    'name_ar' => 'المتجر الرئيسي / المبيعات المباشرة',
                    'code' => 'LOC-POS',
                    'type' => 'offline',
                    'address' => 'King Fahd Road, Luxury Retail District, Riyadh',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 'central_wh',
                    'name_en' => 'Central Quarantine Warehouse',
                    'name_ar' => 'المستودع المركزي الرئيسي',
                    'code' => 'LOC-CWH',
                    'type' => 'warehouse',
                    'address' => 'Industrial City 2, Temperature Controlled Depots',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. Enhance inventory_movements table with variant column if missing
        if (Schema::hasTable('inventory_movements')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_movements', 'variant')) {
                    $table->string('variant')->nullable()->after('sku');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_movements')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_movements', 'variant')) {
                    $table->dropColumn('variant');
                }
            });
        }

        Schema::dropIfExists('locations');
    }
};
