<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\View\ViewModels\OrderViewModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /**
     * Publicly track and verify order & invoice authenticity via QR code scan.
     */
    public function track(Request $request, string $identifier): View
    {
        $order = is_numeric($identifier)
            ? Order::with(['items', 'customer'])->find($identifier)
            : Order::with(['items', 'customer'])->where('order_number', $identifier)->orWhere('invoice_number', $identifier)->first();

        if (!$order) {
            $fallback = OrderViewModel::find($identifier);
            if ($fallback) {
                $order = $fallback;
            } else {
                abort(404, app()->getLocale() === 'ar' ? 'لم يتم العثور على الطلب أو الفاتورة.' : 'Order or invoice not found.');
            }
        }

        $orderNumber = is_array($order) ? ($order['order_number'] ?? $identifier) : ($order->order_number ?? $identifier);
        $invoiceNumber = is_array($order) ? ($order['invoice_number'] ?? $orderNumber) : ($order->invoice_number ?? $orderNumber);
        $verificationUrl = route('orders.track', $orderNumber);

        // Site corporate info
        $siteInfo = [
            'brand_name' => 'BLUE ZONE™ Bioceuticals Inc.',
            'tax_number' => '31004829100003',
            'commercial_record' => 'CR-1010842910',
            'clinical_license' => 'MOH-CERT-2026-BZ884',
            'phone' => '+966 11 482 9100',
            'email' => 'care@bluezone.com',
            'website' => 'www.bluezone.com',
        ];

        return view('public.orders.verify', [
            'order' => $order,
            'orderNumber' => $orderNumber,
            'invoiceNumber' => $invoiceNumber,
            'verificationUrl' => $verificationUrl,
            'siteInfo' => $siteInfo,
        ]);
    }

    /**
     * Download or view printable tax invoice for the tracked order.
     */
    public function downloadInvoice(Request $request, string $identifier)
    {
        $order = is_numeric($identifier)
            ? Order::with(['items', 'customer'])->find($identifier)
            : Order::with(['items', 'customer'])->where('order_number', $identifier)->orWhere('invoice_number', $identifier)->first();

        if (!$order) {
            $fallback = OrderViewModel::find($identifier);
            if ($fallback) {
                $order = $fallback;
            } else {
                abort(404);
            }
        }

        $siteInfo = [
            'brand_name' => 'BLUE ZONE™ Bioceuticals Inc.',
            'tax_number' => '31004829100003',
            'commercial_record' => 'CR-1010842910',
            'clinical_license' => 'MOH-CERT-2026-BZ884',
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
