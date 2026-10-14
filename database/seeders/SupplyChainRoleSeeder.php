<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplyChainRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Update Operations Manager to include Supply Chain permissions
        $ops = Role::where('name', 'Operations Manager')->first();
        if ($ops) {
            $perms = $ops->permissions ?? [];
            $ops->permissions = array_values(array_unique(array_merge($perms, [
                'suppliers.*',
                'raw_materials.*',
                'purchase_orders.*',
                'goods_receipts.*',
                'batches.*',
                'qc.*',
                'work_orders.*',
                'supply_chain_reports.*',
            ])));
            $ops->save();
        }

        // 2. QA / QC Operations Manager
        $qaRole = Role::firstOrCreate(
            ['name' => 'QA / QC Operations Manager'],
            [
                'description' => 'Oversees analytical QC testing, COA verification, quarantine holds, and batch release.',
                'permissions' => [
                    'qc.*',
                    'batches.*',
                    'raw_materials.*',
                    'suppliers.view',
                    'suppliers.approve',
                    'goods_receipts.view',
                    'supply_chain_reports.*',
                ],
            ]
        );

        // 3. Procurement & Sourcing Specialist
        $procRole = Role::firstOrCreate(
            ['name' => 'Procurement & Sourcing Specialist'],
            [
                'description' => 'Manages vendor relations, raw material price negotiations, purchase orders, and goods intake.',
                'permissions' => [
                    'suppliers.*',
                    'purchase_orders.*',
                    'goods_receipts.*',
                    'raw_materials.view',
                    'batches.view',
                    'supply_chain_reports.*',
                ],
            ]
        );

        // 4. Cleanroom MES Production Operator
        $mesRole = Role::firstOrCreate(
            ['name' => 'Cleanroom MES Production Operator'],
            [
                'description' => 'Executes formulation work orders, cleanroom gravimetric dispensing, and material consumption.',
                'permissions' => [
                    'work_orders.*',
                    'batches.view',
                    'raw_materials.view',
                ],
            ]
        );

        // 5. Assign roles to dedicated supply chain users if they exist
        $qaUser = User::where('email', 'qa@bluezone.com')->first();
        if ($qaUser) {
            $qaUser->role_id = $qaRole->id;
            $qaUser->save();
        }

        $procUser = User::where('email', 'procurement@bluezone.com')->first();
        if ($procUser) {
            $procUser->role_id = $procRole->id;
            $procUser->save();
        }

        $chemistUser = User::where('email', 'chemist@bluezone.com')->first();
        if ($chemistUser) {
            $chemistUser->role_id = $qaRole->id;
            $chemistUser->save();
        }
    }
}
