<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Collection;

class CrmCustomerService
{
    /**
     * Compute comprehensive Customer 360 profile.
     */
    public function getCustomer360(Customer $customer): array
    {
        // 1. Order analytics (strict completed/delivered revenue)
        $deliveredOrders = $customer->orders()
            ->whereIn('status', ['delivered', 'Delivered'])
            ->orderBy('created_at', 'asc')
            ->get();

        $totalRevenue = (float) $deliveredOrders->sum(fn ($o) => (float) ($o->total ?? $o->total_amount ?? 0));
        $deliveredCount = $deliveredOrders->count();
        $averageOrderValue = $deliveredCount > 0 ? round($totalRevenue / $deliveredCount, 2) : 0.00;

        $firstOrder = $deliveredOrders->first();
        $lastOrder = $deliveredOrders->last();

        // Calculate order frequency (average days between purchases)
        $orderFrequencyDays = null;
        if ($deliveredCount >= 2 && $firstOrder && $lastOrder) {
            $totalDays = $firstOrder->created_at->diffInDays($lastOrder->created_at);
            $orderFrequencyDays = round($totalDays / ($deliveredCount - 1), 1);
        }

        // 2. Product formulation preferences from order items
        $topCategories = [];
        $topProducts = [];
        $orderIds = $deliveredOrders->pluck('id');

        if ($orderIds->isNotEmpty()) {
            $topCategories = \Illuminate\Support\Facades\DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->whereIn('order_items.order_id', $orderIds)
                ->select('categories.name_en', 'categories.name_ar', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_qty'))
                ->groupBy('categories.id', 'categories.name_en', 'categories.name_ar')
                ->orderByDesc('total_qty')
                ->take(3)
                ->get();

            $topProducts = \Illuminate\Support\Facades\DB::table('order_items')
                ->whereIn('order_items.order_id', $orderIds)
                ->select('product_name_en', 'product_name_ar', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_qty'), \Illuminate\Support\Facades\DB::raw('SUM(total) as total_spent'))
                ->groupBy('product_name_en', 'product_name_ar')
                ->orderByDesc('total_qty')
                ->take(3)
                ->get();
        }

        // 3. Smart CRM Recommendations & Retention Indicators
        $recommendations = [];
        if ($lastOrder) {
            $daysSinceLastOrder = $lastOrder->created_at->diffInDays(now());
            if ($daysSinceLastOrder > 180) {
                $recommendations[] = [
                    'level' => 'danger',
                    'icon' => 'fa-solid fa-triangle-exclamation',
                    'text_en' => "Customer inactive for {$daysSinceLastOrder} days. High churn risk; trigger reactivation campaign.",
                    'text_ar' => "العميل غير نشط منذ {$daysSinceLastOrder} يوماً. خطر فقدان العميل مرتفع، يوصى بحملة إعادة تنشيط.",
                ];
            } elseif ($daysSinceLastOrder > 90) {
                $recommendations[] = [
                    'level' => 'warning',
                    'icon' => 'fa-solid fa-clock-rotate-left',
                    'text_en' => "Customer has not ordered in {$daysSinceLastOrder} days. Approaching at-risk threshold.",
                    'text_ar' => "لم يقم العميل بالطلب منذ {$daysSinceLastOrder} يوماً. يقترب من حد الخطورة.",
                ];
            }

            if ($orderFrequencyDays && $daysSinceLastOrder >= $orderFrequencyDays) {
                $recommendations[] = [
                    'level' => 'info',
                    'icon' => 'fa-solid fa-repeat',
                    'text_en' => "Customer usually reorders every {$orderFrequencyDays} days. Cycle window reached; schedule follow-up.",
                    'text_ar' => "يطلب العميل عادة كل {$orderFrequencyDays} يوماً. حان موعد دورة الطلب الجديدة؛ جدول متابعة.",
                ];
            }
        }

        if ($totalRevenue >= 5000) {
            $recommendations[] = [
                'level' => 'success',
                'icon' => 'fa-solid fa-crown',
                'text_en' => 'High Value Longevity VIP Client. Priority concierge care recommended.',
                'text_ar' => 'عميل VIP عالي القيمة. يوصى بأولوية المتابعة الخاصة.',
            ];
        }

        // 4. Unified Timeline
        $timeline = $this->buildCustomerTimeline($customer);

        return [
            'customer' => $customer,
            'metrics' => [
                'total_revenue' => $totalRevenue,
                'delivered_orders_count' => $deliveredCount,
                'average_order_value' => $averageOrderValue,
                'first_order_date' => $firstOrder?->created_at,
                'last_order_date' => $lastOrder?->created_at,
                'order_frequency_days' => $orderFrequencyDays,
                'loyalty_points' => $customer->loyalty_points ?? 0,
                'loyalty_tier' => $customer->tier ?? 'Longevity Club',
                'lifecycle' => $customer->crm_lifecycle,
            ],
            'top_categories' => $topCategories,
            'top_products' => $topProducts,
            'recommendations' => $recommendations,
            'timeline' => $timeline,
            'open_opportunities' => $customer->crmOpportunities()->with(['stage', 'owner'])->open()->get(),
            'pending_activities' => $customer->crmActivities()->with(['assignee'])->pending()->get(),
            'recent_notes' => $customer->crmNotes()->with('user')->take(10)->get(),
            'tags' => $customer->crmTags()->get(),
        ];
    }

    /**
     * Build unified chronological customer timeline.
     */
    public function buildCustomerTimeline(Customer $customer): Collection
    {
        $events = collect();

        // Account Registration
        $events->push([
            'type' => 'registration',
            'title' => 'Customer Registered',
            'timestamp' => $customer->created_at,
            'icon' => 'fa-solid fa-user-check',
            'color' => '#10B981',
            'details' => "Account registered via {$customer->country}",
            'actor' => $customer->name,
        ]);

        // Orders
        foreach ($customer->orders as $order) {
            $events->push([
                'type' => 'order',
                'title' => "Order #{$order->order_number} ({$order->status})",
                'timestamp' => $order->created_at,
                'icon' => 'fa-solid fa-box-open',
                'color' => in_array(strtolower($order->status), ['delivered', 'completed']) ? '#10B981' : ($order->status === 'cancelled' ? '#EF4444' : '#0EA5E9'),
                'details' => "Total: " . number_format((float)($order->total ?? $order->total_amount ?? 0), 2) . " " . ($order->currency ?? \App\Services\CurrencyService::code()) . " via " . ($order->payment_method ?? 'Online'),
                'actor' => $customer->name,
            ]);
        }

        // Leads converted
        foreach ($customer->crmLeads as $lead) {
            $events->push([
                'type' => 'lead',
                'title' => "Lead Created ({$lead->lead_number})",
                'timestamp' => $lead->created_at,
                'icon' => 'fa-solid fa-bullseye',
                'color' => '#6366F1',
                'details' => "Source: " . ($lead->source?->name ?? 'Direct'),
                'actor' => $lead->owner?->name ?? 'System',
            ]);

            if ($lead->converted_at) {
                $events->push([
                    'type' => 'conversion',
                    'title' => "Lead Converted to Customer",
                    'timestamp' => $lead->converted_at,
                    'icon' => 'fa-solid fa-award',
                    'color' => '#8B5CF6',
                    'details' => "Converted successfully by sales team",
                    'actor' => $lead->owner?->name ?? 'System',
                ]);
            }
        }

        // Opportunities
        foreach ($customer->crmOpportunities as $opp) {
            $events->push([
                'type' => 'opportunity',
                'title' => "Opportunity: {$opp->name} ({$opp->opportunity_number})",
                'timestamp' => $opp->created_at,
                'icon' => 'fa-solid fa-sack-dollar',
                'color' => '#F59E0B',
                'details' => "Stage: {$opp->stage?->name}, Value: " . number_format($opp->value, 2) . " {$opp->currency}",
                'actor' => $opp->owner?->name ?? 'Staff',
            ]);

            if ($opp->won_at) {
                $events->push([
                    'type' => 'opportunity_won',
                    'title' => "Deal Won: {$opp->name}",
                    'timestamp' => $opp->won_at,
                    'icon' => 'fa-solid fa-trophy',
                    'color' => '#10B981',
                    'details' => "Deal closed successfully. Value: " . number_format($opp->value, 2) . " {$opp->currency}",
                    'actor' => $opp->owner?->name ?? 'Staff',
                ]);
            }
        }

        // Activities
        foreach ($customer->crmActivities as $act) {
            $events->push([
                'type' => 'activity',
                'title' => "Activity [{$act->activity_type}]: {$act->subject}",
                'timestamp' => $act->created_at,
                'icon' => $act->activity_type === 'call' ? 'fa-solid fa-phone' : ($act->activity_type === 'meeting' ? 'fa-solid fa-calendar-check' : 'fa-solid fa-list-check'),
                'color' => $act->status === 'completed' ? '#10B981' : '#64748B',
                'details' => "Status: {$act->status}" . ($act->outcome ? " | Outcome: {$act->outcome}" : ""),
                'actor' => $act->assignee?->name ?? 'Staff',
            ]);
        }

        // Notes
        foreach ($customer->crmNotes as $note) {
            $events->push([
                'type' => 'note',
                'title' => "Internal Note Added",
                'timestamp' => $note->created_at,
                'icon' => 'fa-solid fa-note-sticky',
                'color' => '#EC4899',
                'details' => $note->body,
                'actor' => $note->user?->name ?? 'Staff',
            ]);
        }

        return $events->sortByDesc('timestamp')->values();
    }
}
