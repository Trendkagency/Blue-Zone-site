<?php

namespace App\Services;

use App\Models\CrmActivity;
use App\Models\CrmOpportunity;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CrmAutomationService
{
    /**
     * Trigger follow-up activity when an order is delivered (Idempotent).
     */
    public function handleOrderDelivered(Order $order): ?CrmActivity
    {
        if (!$order->customer_id) {
            return null;
        }

        // Idempotency check: don't create duplicate follow-up for same order
        $existing = CrmActivity::where('customer_id', $order->customer_id)
            ->where('subject', 'like', "%Order #{$order->order_number}%")
            ->first();

        if ($existing) {
            return $existing;
        }

        $followUpDays = (int) Setting::get('crm_follow_up_days', 7);
        $dueAt = now()->addDays($followUpDays);

        // Assign to first available admin/sales agent or default admin
        $assignedTo = User::where('status', 'active')->first()?->id ?? 1;

        $activity = CrmActivity::create([
            'activity_type' => 'follow_up',
            'subject' => "Follow-up: Order #{$order->order_number} Post-Delivery Check-in",
            'description' => "Check with client on their experience with Blue Zone longevity formulation delivery.",
            'customer_id' => $order->customer_id,
            'assigned_to' => $assignedTo,
            'created_by' => $assignedTo,
            'due_at' => $dueAt,
            'status' => 'pending',
            'priority' => 'normal',
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'automation' => 'order_delivered_follow_up',
            ],
        ]);

        Log::info("CRM Automation: Scheduled post-delivery follow-up for order #{$order->order_number}");

        return $activity;
    }

    /**
     * Generate reactivation task for inactive customer (Idempotent).
     */
    public function handleInactiveCustomer(Customer $customer, int $daysInactive = 90): ?CrmActivity
    {
        // Guard: check if reactivation task was already created within the last 30 days
        $existing = CrmActivity::where('customer_id', $customer->id)
            ->where('subject', 'like', '%Reactivation%')
            ->where('created_at', '>=', now()->subDays(30))
            ->first();

        if ($existing) {
            return null;
        }

        $assignedTo = User::where('status', 'active')->first()?->id ?? 1;

        $activity = CrmActivity::create([
            'activity_type' => 'call',
            'subject' => "Reactivation Outreach: {$customer->name}",
            'description' => "Customer has not placed an order in {$daysInactive}+ days. Contact client with personalized longevity formulation replenishment offer.",
            'customer_id' => $customer->id,
            'assigned_to' => $assignedTo,
            'created_by' => $assignedTo,
            'due_at' => now()->addDays(2),
            'status' => 'pending',
            'priority' => 'high',
            'metadata' => [
                'automation' => 'customer_reactivation',
                'days_inactive' => $daysInactive,
            ],
        ]);

        Log::info("CRM Automation: Created reactivation task for customer #{$customer->id}");

        return $activity;
    }
}
