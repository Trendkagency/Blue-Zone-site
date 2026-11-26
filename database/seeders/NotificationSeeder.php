<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = User::where('email', 'admin@bluezone.com')->orWhere('role_id', 1)->get();

        if ($admins->isEmpty()) {
            $firstUser = User::first();
            if ($firstUser) {
                $admins = collect([$firstUser]);
            }
        }

        foreach ($admins as $admin) {
            if ($admin->notifications()->count() > 0) {
                continue;
            }

            // 1. New Order Notification
            $admin->notify(new AdminNotification(
                'New Online Order #BZ-10492',
                'Dr. Zaid Al-Harbi placed an order for Blue Joint (2x) + Blue Mind (1x) — batch FEFO assigned.',
                'order',
                '/admin/orders',
                'fa-solid fa-cart-shopping text-blue-500'
            ));

            // 2. Near-Expiry & FEFO Batch Advisory
            $admin->notify(new AdminNotification(
                'FEFO Batch Expiry Watch: Earlier-Lot Priority Queue',
                'Early-expiry lots in Online Fulfillment Hub (LOT-FG-202506-0001..0004) are queued as #1 OUT.',
                'stock',
                '/admin/product-batches',
                'fa-solid fa-clock text-amber-500'
            ));

            // 3. New Tax Invoice Generated
            $admin->notify(new AdminNotification(
                'Tax Invoice Issued #INV-2026-0841',
                'Official electronic tax invoice issued with batch & expiry traceability.',
                'invoice',
                '/admin/invoices',
                'fa-solid fa-file-invoice-dollar text-emerald-500'
            ));

            // 4. Manufacturing Release Notification
            $note4 = $admin->notify(new AdminNotification(
                'Work Order Completed #WO-2026-0001 (500 units)',
                'Finished batch LOT-FG-202610-0001 released to Central Logistics Warehouse with landed cost absorbed.',
                'system',
                '/admin/work-orders',
                'fa-solid fa-industry text-indigo-500'
            ));
        }
    }
}
