<?php

namespace App\View\ViewModels;

class ReportViewModel
{
    /**
     * Dashboard KPI statistics.
     *
     * @return array<string, mixed>
     */
    public static function kpi(): array
    {
        return [
            'total_sales' => 124890.00,
            'online_sales' => 89450.00,
            'offline_sales' => 35440.00,
            'total_orders' => 1482,
            'pending_orders' => 18,
            'total_customers' => 934,
            'total_products' => 6,
            'low_stock_count' => 2,
            'out_of_stock_count' => 1,
            'growth_rate' => '+18.4%',
            'average_order_value' => 84.27,
        ];
    }

    /**
     * Sales breakdown by channel and month.
     *
     * @return array<string, mixed>
     */
    public static function salesData(): array
    {
        return [
            'monthly' => [
                ['month' => 'Jan', 'online' => 11200, 'offline' => 4500],
                ['month' => 'Feb', 'online' => 12800, 'offline' => 4900],
                ['month' => 'Mar', 'online' => 14500, 'offline' => 5200],
                ['month' => 'Apr', 'online' => 13900, 'offline' => 5800],
                ['month' => 'May', 'online' => 16200, 'offline' => 6400],
                ['month' => 'Jun', 'online' => 18400, 'offline' => 7100],
            ],
            'categories' => [
                ['name' => 'Cellular Longevity', 'percentage' => 38, 'amount' => 47458.20],
                ['name' => 'Cognitive & Brain Health', 'percentage' => 32, 'amount' => 39964.80],
                ['name' => 'Cardiovascular Longevity', 'percentage' => 15, 'amount' => 18733.50],
                ['name' => 'Metabolic Health', 'percentage' => 9, 'amount' => 11240.10],
                ['name' => 'Immunity & Sleep', 'percentage' => 6, 'amount' => 7493.40],
            ],
        ];
    }
}
