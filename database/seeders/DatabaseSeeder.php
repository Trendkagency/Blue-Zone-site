<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the complete BZ-OS enterprise database in strict domain dependency order.
     */
    public function run(): void
    {
        $this->call([
            WorldCountriesSeeder::class,
            BluezoneSeeder::class,
            SupplyChainRoleSeeder::class,
            SupplyChainSeeder::class,
            FinanceSeeder::class,
            CrmDummyDataSeeder::class,
            HrSeeder::class,
            MrSystemSeeder::class,
            NotificationSeeder::class,
        ]);

        // Final integrity reconciliation & backfill across Raw Material & Finished Product Batches
        Artisan::call('bz:reconcile-batches', ['--backfill' => true]);
    }
}
