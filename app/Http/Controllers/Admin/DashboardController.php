<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\InventoryViewModel;
use App\View\ViewModels\OrderViewModel;
use App\View\ViewModels\ProductViewModel;
use App\View\ViewModels\ReportViewModel;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Render the comprehensive Admin Dashboard overview.
     */
    public function index(): View
    {
        $kpi = ReportViewModel::kpi();
        $salesData = ReportViewModel::salesData();
        $recentOrders = OrderViewModel::all();
        $recentMovements = array_slice(InventoryViewModel::movements(), 0, 5);
        $products = ProductViewModel::all();

        return view('admin.dashboard.index', [
            'kpi' => $kpi,
            'salesData' => $salesData,
            'recentOrders' => $recentOrders,
            'recentMovements' => $recentMovements,
            'products' => $products,
        ]);
    }
}
