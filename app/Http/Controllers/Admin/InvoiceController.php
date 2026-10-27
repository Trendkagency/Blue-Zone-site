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
            'totalCount' => is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) ? $orders->total() : count($orders),
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

        return view('admin.invoices.show', [
            'order' => $order,
            'siteInfo' => $siteInfo,
            'invoiceTemplate' => \App\Services\InvoiceTemplateService::getTemplate(),
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

    /**
     * Render specialized Avery / Thermal Shipping & Package Label for invoice.
     */
    public function averyLabel(string $id): View
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

        $orderNumber = is_array($order) ? ($order['order_number'] ?? $id) : ($order->order_number ?? $id);

        return view('admin.orders.avery-label', [
            'order' => $order,
            'orderNumber' => $orderNumber,
        ]);
    }

    /**
     * Update invoice details and live template configuration in real-time.
     */
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $order = is_numeric($id) 
            ? Order::find($id) 
            : Order::where('order_number', $id)->orWhere('invoice_number', $id)->first();

        if ($order) {
            $validated = $request->validate([
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:100',
                'customer_email' => 'nullable|string|max:255',
                'shipping_address' => 'nullable',
                'invoice_number' => 'nullable|string|max:100',
                'date' => 'nullable|string|max:50',
                'payment_method' => 'nullable|string|max:100',
                'payment_status' => 'nullable|string|max:100',
                'payment_transaction_id' => 'nullable|string|max:100',
                'discount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            if (isset($validated['shipping_address'])) {
                if (is_string($validated['shipping_address'])) {
                    $validated['shipping_address'] = ['street' => $validated['shipping_address']];
                }
            }

            $order->update(array_filter($validated, fn($val) => $val !== null));

            if ($request->has('discount')) {
                $order->total = max(0, (float)$order->subtotal - (float)$order->discount + (float)$order->tax);
                $order->save();
            }
        }

        if ($request->has('sections_order') && is_array($request->input('sections_order'))) {
            $tpl = \App\Services\InvoiceTemplateService::getTemplate();
            $tpl['sections_order'] = $request->input('sections_order');
            \App\Services\InvoiceTemplateService::saveTemplate($tpl);
        }

        if ($request->has('template_overrides') && is_array($request->input('template_overrides'))) {
            $tpl = \App\Services\InvoiceTemplateService::getTemplate();
            $merged = array_replace_recursive($tpl, $request->input('template_overrides'));
            \App\Services\InvoiceTemplateService::saveTemplate($merged);
        }

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم تحديث بيانات الفاتورة والترتيب بنجاح في الوقت الحقيقي!'
                : 'Invoice details and layout updated in real-time successfully!',
            'order' => $order,
        ]);
    }
}

