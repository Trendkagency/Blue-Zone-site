<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('adjustment_types')) {
            Schema::create('adjustment_types', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar')->nullable();
                $table->string('code')->unique();
                $table->enum('direction', ['increase', 'decrease', 'neutral'])->default('neutral');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_system')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });

            // Seed system default adjustment types to preserve all existing logic
            $defaults = [
                [
                    'name_en' => 'Manual Adjustment (Count Discrepancy)',
                    'name_ar' => 'تسوية جردية يدوية (فارق جرد)',
                    'code' => 'Manual Adjustment',
                    'direction' => 'neutral',
                    'description' => 'Periodic cycle count reconciliation and manual quantity audit.',
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_en' => 'Stock In (Surplus / Inbound)',
                    'name_ar' => 'إدخال مخزون جديد (توريد / فائض)',
                    'code' => 'Stock In',
                    'direction' => 'increase',
                    'description' => 'Unlogged supplier delivery, positive count surplus, or inbound restock.',
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_en' => 'Stock Out (Discrepancy Deduction)',
                    'name_ar' => 'إخراج مخزون (صرف / عجز)',
                    'code' => 'Stock Out',
                    'direction' => 'decrease',
                    'description' => 'Physical stock count shortage deduction or direct stock release.',
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_en' => 'Damaged / Shipping Breakage',
                    'name_ar' => 'تالف في المستودع / الشحن (إتلاف)',
                    'code' => 'Damaged',
                    'direction' => 'decrease',
                    'description' => 'Compromised packaging, transit breakage, or physical defect write-off.',
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_en' => 'Expired Lot (Discarded)',
                    'name_ar' => 'منتهي الصلاحية (استبعاد)',
                    'code' => 'Expired',
                    'direction' => 'decrease',
                    'description' => 'Formulations exceeding shelf-life discarded according to SFDA guidelines.',
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_en' => 'Customer Return (Restock)',
                    'name_ar' => 'مرتجع عميل إلى الرصيد',
                    'code' => 'Return',
                    'direction' => 'increase',
                    'description' => 'Returned items inspected and approved for restocking into warehouse.',
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => 6,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_en' => 'Initial Opening Balance',
                    'name_ar' => 'رصيد افتتاحي أولي',
                    'code' => 'Initial Stock',
                    'direction' => 'increase',
                    'description' => 'Baseline opening stock ledger initialization.',
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => 7,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('adjustment_types')->insert($defaults);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjustment_types');
    }
};
