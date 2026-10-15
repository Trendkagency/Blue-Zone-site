<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesController extends Controller
{
    /**
     * Commercial Products & Services Catalog Hub.
     */
    public function productsServices(Request $request): View
    {
        $type = $request->query('type', 'all'); // all, product, service
        $categoryId = $request->query('category_id', 'all');
        $search = $request->query('search');

        $query = Product::with(['category', 'inventoryItems']);

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $items = $query->latest('id')->paginate(16)->withQueryString();

        // Commercial Intelligence KPIs
        $allProducts = Product::all();
        $totalItems = $allProducts->count();
        $productsCount = $allProducts->where('type', '!=', 'service')->count();
        $servicesCount = $allProducts->where('type', 'service')->count();

        $totalRetailValuation = 0.0;
        $margins = [];

        foreach ($allProducts as $p) {
            $price = (float) $p->price;
            $cost = (float) ($p->cost_price ?? ($price * 0.45));
            $totalStock = (int) $p->stock_online + (int) $p->stock_offline;
            $totalRetailValuation += ($totalStock * $price);
            if ($price > 0) {
                $margins[] = (($price - $cost) / $price) * 100;
            }
        }

        $avgMargin = count($margins) > 0 ? round(array_sum($margins) / count($margins), 1) : 55.0;

        $categories = Category::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.sales.products-services', [
            'items' => $items,
            'categories' => $categories,
            'totalItems' => $totalItems,
            'productsCount' => $productsCount,
            'servicesCount' => $servicesCount,
            'totalRetailValuation' => $totalRetailValuation,
            'avgMargin' => $avgMargin,
            'selectedType' => $type,
            'selectedCategory' => $categoryId,
            'search' => $search,
            'currentPage' => $items->currentPage(),
            'totalPages' => $items->lastPage(),
        ]);
    }

    /**
     * Unified Sales Orders Command Center (Online, POS, and B2B Quotations).
     */
    public function orders(Request $request): View
    {
        $channel = $request->query('channel', 'all'); // all, online, offline, quotation
        $status = $request->query('status', 'all');
        $paymentStatus = $request->query('payment_status', 'all');
        $search = $request->query('search');

        $query = Order::with(['items', 'customer'])->latest('id');

        if ($channel && $channel !== 'all') {
            $query->where('channel', $channel);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Revenue & Order KPIs
        $allOrders = Order::all();
        $totalRevenue = (float) $allOrders->sum('total');
        $totalOrdersCount = $allOrders->count();
        $pendingProcessingCount = $allOrders->whereIn('status', ['Pending', 'Confirmed', 'Processing'])->count();
        $avgOrderValue = $totalOrdersCount > 0 ? round($totalRevenue / $totalOrdersCount, 2) : 0.0;

        $onlineCount = $allOrders->where('channel', 'online')->count();
        $offlineCount = $allOrders->where('channel', 'offline')->count();
        $quotationCount = $allOrders->where('channel', 'quotation')->count();

        return view('admin.sales.orders', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'totalOrdersCount' => $totalOrdersCount,
            'pendingProcessingCount' => $pendingProcessingCount,
            'avgOrderValue' => $avgOrderValue,
            'onlineCount' => $onlineCount,
            'offlineCount' => $offlineCount,
            'quotationCount' => $quotationCount,
            'selectedChannel' => $channel,
            'selectedStatus' => $status,
            'selectedPayment' => $paymentStatus,
            'search' => $search,
            'currentPage' => $orders->currentPage(),
            'totalPages' => $orders->lastPage(),
        ]);
    }
}
