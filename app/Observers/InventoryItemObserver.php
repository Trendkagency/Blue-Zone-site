<?php

namespace App\Observers;

use App\Jobs\SendBulkFcmPushJob;
use App\Models\InventoryItem;
use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Log;

/**
 * Observer for InventoryItem Model.
 *
 * Implements the Observer design pattern to monitor stock level changes,
 * remaining buffer limits, and trigger automated real-time alerts.
 */
class InventoryItemObserver
{
    /**
     * Handle the InventoryItem "saved" event.
     */
    public function saved(InventoryItem $item): void
    {
        // Trigger when stock or threshold changes, or upon item creation
        if (!$item->wasChanged('current_stock') && !$item->wasChanged('low_stock_threshold') && !$item->wasRecentlyCreated) {
            return;
        }

        $product = $item->product;
        $productName = $product ? $product->name_en : 'Product #' . $item->product_id;
        $locationName = $item->location_name_en ?? ucfirst((string)$item->location_id);
        $threshold = $item->low_stock_threshold ?? 15;
        $stock = $item->current_stock;

        $title = null;
        $body = null;
        $icon = null;

        if ($stock <= 0) {
            $title = "🚨 Critical Out of Stock: {$productName}";
            $body = "[{$locationName}] stock depleted (0 units remaining). Restock immediately.";
            $icon = 'fa-solid fa-circle-xmark text-rose-500';
        } elseif ($stock <= $threshold) {
            $title = "⚠️ Low Stock Remaining: {$productName}";
            $body = "[{$locationName}] only {$stock} units remaining (Safety threshold: {$threshold} units).";
            $icon = 'fa-solid fa-triangle-exclamation text-amber-500';
        }

        if ($title !== null) {
            $actionUrl = '/admin/inventory';

            // 1. Dispatch in-app Database Notification to Admin Operators
            $admins = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['Super Admin', 'super_admin', 'Admin', 'admin', 'Manager', 'Inventory Staff']);
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification(
                    $title,
                    $body,
                    'stock',
                    $actionUrl,
                    $icon
                ));
            }

            // 2. Dispatch Realtime FCM Push via Asynchronous Background Job with transaction safety (afterCommit)
            SendBulkFcmPushJob::dispatch(
                $title,
                $body,
                [
                    'type'       => 'stock',
                    'product_id' => (string) $item->product_id,
                    'stock'      => (string) $stock,
                    'threshold'  => (string) $threshold,
                    'action_url' => $actionUrl,
                    'icon'       => $icon,
                ],
                'roles',
                ['Super Admin', 'super_admin', 'Admin', 'admin', 'Manager', 'Inventory Staff']
            )->afterCommit();

            Log::info("InventoryItemObserver: Triggered stock alert for Product #{$item->product_id} at {$locationName}. Stock: {$stock}");
        }
    }
}
