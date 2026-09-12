<?php

namespace App\Observers;

use App\Jobs\SendBulkFcmPushJob;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Log;

/**
 * Observer for Order Model.
 *
 * Implements the Observer design pattern to monitor order lifecycle
 * (creation, payment updates, fulfillment status transitions) and broadcast
 * real-time alerts to administrators without blocking business transactions.
 */
class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        if (!config('fcm.triggers.new_orders', true)) {
            return;
        }

        $orderNum = $order->order_number;
        $total = number_format((float)$order->total, 2);
        $customer = $order->customer_name ?: 'Valued Customer';
        $channel = ucfirst($order->channel ?: 'online');

        $title = "🛒 New {$channel} Order Placed: #{$orderNum}";
        $body = "Order total: {$total} SAR by {$customer}. Channel: {$channel}.";
        $actionUrl = "/admin/orders/{$order->id}";
        $icon = 'fa-solid fa-cart-shopping text-emerald-500';

        // 1. Create In-App Database Notifications for Administrative Operators
        $admins = $this->resolveOrderAdmins();
        foreach ($admins as $admin) {
            $admin->notify(new AdminNotification(
                $title,
                $body,
                'order',
                $actionUrl,
                $icon
            ));
        }

        // 2. Dispatch Asynchronous Queued FCM Push with transaction safety (afterCommit)
        SendBulkFcmPushJob::dispatch(
            $title,
            $body,
            [
                'type'         => 'order_created',
                'order_id'     => (string) $order->id,
                'order_number' => (string) $orderNum,
                'total'        => (string) $total,
                'channel'      => (string) $order->channel,
                'action_url'   => $actionUrl,
                'icon'         => $icon,
            ],
            'admins'
        )->afterCommit();

        Log::info("OrderObserver: Dispatched new order notifications for #{$orderNum}.");
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $statusChanged = $order->wasChanged('status');
        $paymentChanged = $order->wasChanged('payment_status');

        if (!$statusChanged && !$paymentChanged) {
            return;
        }

        $orderNum = $order->order_number;
        $status = strtolower($order->status ?? 'pending');
        $payment = strtolower($order->payment_status ?? 'pending');
        $actionUrl = "/admin/orders/{$order->id}";

        $title = null;
        $body = null;
        $icon = 'fa-solid fa-bell text-sky-500';
        $eventType = 'order_status_changed';

        if ($statusChanged) {
            $statusLabel = ucfirst($status);
            $title = "📦 Order Status Updated: #{$orderNum} → {$statusLabel}";
            $body = "Order #{$orderNum} has been marked as {$statusLabel}.";
            $icon = match ($status) {
                'processing' => 'fa-solid fa-flask-vial text-sky-500',
                'shipped'    => 'fa-solid fa-truck-fast text-indigo-500',
                'delivered'  => 'fa-solid fa-circle-check text-emerald-500',
                'cancelled'  => 'fa-solid fa-ban text-rose-500',
                default      => 'fa-solid fa-clock-rotate-left text-amber-500',
            };
            $eventType = 'order_status_' . $status;
        } elseif ($paymentChanged) {
            $title = "💳 Payment Update: #{$orderNum} ({$payment})";
            $body = "Payment status updated to " . ucfirst($payment) . " for Order #{$orderNum}.";
            $icon = $payment === 'paid' ? 'fa-solid fa-circle-check text-emerald-500' : 'fa-solid fa-circle-exclamation text-amber-500';
            $eventType = 'order_payment_' . $payment;
        }

        if ($title !== null) {
            // 1. Create In-App Database Notifications for Administrative Operators
            $admins = $this->resolveOrderAdmins();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification(
                    $title,
                    $body,
                    'order',
                    $actionUrl,
                    $icon
                ));
            }

            // 2. Dispatch Queued FCM Push with transaction safety
            SendBulkFcmPushJob::dispatch(
                $title,
                $body,
                [
                    'type'         => $eventType,
                    'order_id'     => (string) $order->id,
                    'order_number' => (string) $orderNum,
                    'status'       => (string) $status,
                    'payment'      => (string) $payment,
                    'action_url'   => $actionUrl,
                    'icon'         => $icon,
                ],
                'admins'
            )->afterCommit();

            Log::info("OrderObserver: Dispatched update notifications for Order #{$orderNum}. Status: {$status}, Payment: {$payment}.");
        }
    }

    /**
     * Resolve eligible admin recipients for orders dynamically.
     */
    protected function resolveOrderAdmins()
    {
        return User::whereHas('role', function ($query) {
            $query->whereIn('name', [
                'Super Admin',
                'super_admin',
                'Admin',
                'admin',
                'Manager',
                'Sales Staff',
            ]);
        })->get();
    }
}
