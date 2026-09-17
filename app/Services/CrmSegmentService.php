<?php

namespace App\Services;

use App\Models\CrmSegment;
use App\Models\Customer;
use Illuminate\Support\Collection;

class CrmSegmentService
{
    /**
     * Evaluate a dynamic customer segment based on rules.
     */
    public function getCustomersForSegment(CrmSegment $segment): Collection
    {
        $query = Customer::query()->where('status', 'active');
        $slug = $segment->slug;
        $rules = $segment->rules ?? [];

        switch ($slug) {
            case 'high-value':
                $threshold = $rules['min_spent'] ?? 3000;
                $query->where('total_spent', '>=', $threshold);
                break;

            case 'repeat-customers':
                $minOrders = $rules['min_orders'] ?? 2;
                $query->where('total_orders', '>=', $minOrders);
                break;

            case 'at-risk':
                $days = $rules['days_inactive'] ?? 90;
                $thresholdDate = now()->subDays($days);
                $query->whereHas('orders', function ($q) use ($thresholdDate) {
                    $q->where('status', 'delivered');
                })->whereDoesntHave('orders', function ($q) use ($thresholdDate) {
                    $q->where('status', 'delivered')->where('created_at', '>=', $thresholdDate);
                });
                break;

            case 'inactive':
                $days = $rules['days_inactive'] ?? 180;
                $thresholdDate = now()->subDays($days);
                $query->whereDoesntHave('orders', function ($q) use ($thresholdDate) {
                    $q->where('status', 'delivered')->where('created_at', '>=', $thresholdDate);
                });
                break;

            case 'vip':
                $query->where(function ($q) {
                    $q->where('total_spent', '>=', 5000)
                      ->orWhere('tier', 'Platinum')
                      ->orWhere('tier', 'Gold');
                });
                break;

            default:
                if (!empty($rules['min_spent'])) {
                    $query->where('total_spent', '>=', $rules['min_spent']);
                }
                if (!empty($rules['min_orders'])) {
                    $query->where('total_orders', '>=', $rules['min_orders']);
                }
                break;
        }

        return $query->get();
    }

    /**
     * Refresh counts for all segments.
     */
    public function refreshSegmentCounts(): void
    {
        $segments = CrmSegment::active()->get();
        foreach ($segments as $segment) {
            $count = $this->getCustomersForSegment($segment)->count();
            $segment->update([
                'customer_count' => $count,
                'last_evaluated_at' => now(),
            ]);
        }
    }
}
