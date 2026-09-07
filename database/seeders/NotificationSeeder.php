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
            // Check if already seeded to avoid duplicates
            if ($admin->notifications()->count() > 0) {
                continue;
            }

            // 1. New Order Notification (Unread)
            $admin->notify(new AdminNotification(
                'New Online Order #ORD-2026-089',
                'Customer Sarah Miller placed an order for Cellular Longevity Elixir (2x) totaling $340.00.',
                'order',
                '/admin/orders',
                'fa-solid fa-cart-shopping text-blue-500'
            ));

            // 2. Low Stock Alert (Unread)
            $admin->notify(new AdminNotification(
                'Critical Stock Alert: Mitochondrial NAD+ Booster',
                'Stock level reached 4 units remaining (safety threshold: 15 units). Restock requested.',
                'stock',
                '/admin/inventory',
                'fa-solid fa-triangle-exclamation text-amber-500'
            ));

            // 3. New POS Offline Sale (Unread)
            $admin->notify(new AdminNotification(
                'Walk-in Sale Registered #INV-1042',
                'Cash counter generated invoice for $195.00 via Riyadh Flagship Clinic.',
                'invoice',
                '/admin/invoices',
                'fa-solid fa-file-invoice-dollar text-emerald-500'
            ));

            // 4. System Backup & Integrity Check (Read)
            $admin->notify(new AdminNotification(
                'Automated Clinical DB Backup Verified',
                'Daily encrypted snapshot completed successfully without anomalies. Integrity: 100%.',
                'system',
                '/admin/settings',
                'fa-solid fa-shield-halved text-sky-500'
            ));

            // Mark the 4th notification as read for contrast
            $lastNotification = $admin->notifications()->latest()->first();
            if ($lastNotification) {
                $lastNotification->markAsRead();
            }
        }
    }
}
