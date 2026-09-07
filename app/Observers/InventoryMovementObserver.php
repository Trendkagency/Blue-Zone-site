<?php

namespace App\Observers;

use App\Models\InventoryMovement;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

/**
 * Observer for InventoryMovement Model.
 *
 * Implements the Observer design pattern to monitor inventory transactions
 * (transfers, damages, returns, adjustments) and broadcast real-time alerts.
 */
class InventoryMovementObserver
{
    /**
     * Handle the InventoryMovement "created" event.
     */
    public function created(InventoryMovement $movement): void
    {
        $type = $movement->movement_type ?: 'Transaction';
        $typeNorm = strtolower(trim((string)$type));
        $productName = $movement->product_name_en ?? ($movement->product ? $movement->product->name_en : 'Product #' . $movement->product_id);
        $qty = abs($movement->quantity);
        $user = $movement->user ?? 'Staff';

        $actionUrl = '/admin/inventory/history';
        $icon = 'fa-solid fa-boxes-stacked text-sky-500';
        $notifType = 'stock';

        if (in_array($typeNorm, ['stock transfer', 'transfer'], true)) {
            $title = "🔄 Stock Transfer: {$qty} units of {$productName}";
            $body = "Dispatched from [{$movement->from_location}] to [{$movement->to_location}] by {$user}.";
            $actionUrl = '/admin/inventory/transfers';
            $icon = 'fa-solid fa-arrow-right-arrow-left text-cyan-500';
        } elseif (in_array($typeNorm, ['damaged', 'expired', 'issue'], true)) {
            $title = "⚠️ Product Issue Reported: {$type} Stock ({$productName})";
            $body = "{$qty} units flagged as {$type} at [{$movement->from_location}]. Recorded by {$user}." . ($movement->note ? " Reason: {$movement->note}" : "");
            $actionUrl = '/admin/inventory/history';
            $icon = 'fa-solid fa-triangle-exclamation text-rose-500';
        } elseif (in_array($typeNorm, ['manual adjustment', 'adjustment'], true)) {
            $title = "📝 Inventory Stock Adjustment: {$productName}";
            $body = "Quantity delta: {$movement->quantity} units at [{$movement->from_location}]." . ($movement->note ? " Reason: {$movement->note}" : "");
            $actionUrl = '/admin/inventory/history';
            $icon = 'fa-solid fa-pen-ruler text-amber-500';
        } elseif (in_array($typeNorm, ['stock in', 'in', 'received'], true)) {
            $title = "📦 Stock Replenishment Received: {$productName}";
            $body = "{$qty} units received at [{$movement->to_location}]. Logged by {$user}.";
            $actionUrl = '/admin/inventory';
            $icon = 'fa-solid fa-box-open text-emerald-500';
        } else {
            $title = "📦 Inventory Transaction: {$type} ({$productName})";
            $body = "{$qty} units recorded ({$type}) by {$user}.";
            $actionUrl = '/admin/inventory/history';
            $icon = 'fa-solid fa-boxes-stacked text-sky-500';
        }


        if ($title !== null) {
            // 1. Record in Database Notification ledger for Admins
            $admins = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['Super Admin', 'Admin', 'Manager', 'Inventory Staff']);
            })->orWhere('role_id', 1)->get();

            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification(
                    $title,
                    $body,
                    $notifType,
                    $actionUrl,
                    $icon
                ));
            }

            // 2. Dispatch Real-time Push via Singleton FcmService
            $fcm = FcmService::getInstance();
            $fcm->sendToAdmins($title, $body, [
                'type'             => $notifType,
                'movement_type'    => $type,
                'product_id'       => (string) $movement->product_id,
                'quantity'         => (string) $qty,
                'movement_number'  => $movement->movement_number,
                'action_url'       => $actionUrl,
            ]);

            Log::info("InventoryMovementObserver: Processed {$type} movement #{$movement->id} for {$productName}.");
        }
    }
}
