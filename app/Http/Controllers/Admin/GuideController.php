<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\GoodsReceipt;
use App\Models\HeroSlide;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuideController extends Controller
{
    /**
     * Display the comprehensive interactive System Guide & Knowledge Hub.
     */
    public function index(Request $request): View
    {
        // Real-time system metrics to enrich the guide
        $systemMetrics = [
            'products_count' => Product::count(),
            'suppliers_count' => Supplier::count(),
            'warehouses_count' => Warehouse::count(),
            'purchase_orders_count' => PurchaseOrder::count(),
            'goods_receipts_count' => GoodsReceipt::count(),
            'work_orders_count' => class_exists(WorkOrder::class) ? WorkOrder::count() : 0,
            'raw_materials_count' => RawMaterial::count(),
            'orders_count' => Order::count(),
            'customers_count' => Customer::count(),
            'users_count' => User::count(),
            'hero_slides_count' => HeroSlide::count(),
        ];

        return view('admin.guide.index', compact('systemMetrics'));
    }
}
