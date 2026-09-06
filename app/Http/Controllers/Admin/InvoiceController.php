<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\View\ViewModels\OrderViewModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * List all orders with invoices.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $query = Order::with(['items', 'customer'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        if ($orders->isEmpty() && !$search) {
            $orders = collect(OrderViewModel::all());
        }

        return view('admin.invoices.index', [
            'orders' => $orders,
            'search' => $search,
            'currentPage' => is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) ? $orders->currentPage() : 1,
            'totalPages' => is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) ? $orders->lastPage() : 1,
        ]);
    }

    /**
     * Show single invoice in Admin panel.
     */
    public function show(string $id): View
    {
        $order = is_numeric($id) 
            ? Order::with(['items', 'customer'])->find($id) 
            : Order::with(['items', 'customer'])->where('order_number', $id)->orWhere('invoice_number', $id)->first();

        if (!$order) {
            $fallback = OrderViewModel::find($id);
            if ($fallback) {
                $order = $fallback;
            } else {
                abort(404);
            }
        }

        return view('admin.invoices.show', [
            'order' => $order,
        ]);
    }

    /**
     * Render specialized high-end print view for invoice with full site & corporate metadata.
     */
    public function print(string $id): View
    {
        $order = is_numeric($id) 
            ? Order::with(['items', 'customer'])->find($id) 
            : Order::with(['items', 'customer'])->where('order_number', $id)->orWhere('invoice_number', $id)->first();

        if (!$order) {
            $fallback = OrderViewModel::find($id);
            if ($fallback) {
                $order = $fallback;
            } else {
                abort(404);
            }
        }

        $siteInfo = [
            'brand_name' => 'BLUE ZONE™ Bioceuticals Inc.',
            'brand_tagline' => 'Advanced Cellular Longevity & Precision Wellness Systems',
            'tax_number' => '31004829100003',
            'commercial_record' => 'CR-1010842910',
            'clinical_license' => 'MOH-CERT-2026-BZ884',
            'address_en' => 'King Fahd Road, Al-Olaya Towers, Level 24, Riyadh 12213, Saudi Arabia',
            'address_ar' => 'طريق الملك فهد، أبراج العليا، الطابق 24، الرياض 12213، المملكة العربية السعودية',
            'phone' => '+966 11 482 9100',
            'email' => 'care@bluezone.com',
            'website' => 'www.bluezone.com',
        ];

        return view('admin.invoices.print', [
            'order' => $order,
            'siteInfo' => $siteInfo,
        ]);
    }
}
