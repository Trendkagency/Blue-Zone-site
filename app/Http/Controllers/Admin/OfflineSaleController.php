<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\InventoryService;
use App\View\ViewModels\OrderViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class OfflineSaleController extends Controller
{
    public function index(Request $request): View
    {
        $dbSales = Order::where('channel', 'offline')->with('items')->latest()->paginate(15);

        if ($dbSales->isNotEmpty()) {
            $sales = $dbSales->map(function ($order) {
                return [
                    'id' => $order->id,
                    'sale_number' => $order->order_number,
                    'invoice_number' => $order->invoice_number,
                    'store_location' => 'Riyadh Flagship Boutique',
                    'cashier' => $order->notes ? StrAfter($order->notes, 'Cashier: ') : 'Senior Longevity Specialist',
                    'customer_name' => $order->customer_name,
                    'payment_method' => $order->payment_method,
                    'subtotal' => (float) $order->subtotal,
                    'discount' => (float) $order->discount,
                    'tax' => (float) $order->tax,
                    'total' => (float) $order->total,
                    'date' => $order->date?->format('Y-m-d') ?? now()->toDateString(),
                    'time' => $order->created_at?->format('H:i') ?? '12:00',
                ];
            })->toArray();
            $currentPage = $dbSales->currentPage();
            $totalPages = $dbSales->lastPage();
        } else {
            $sales = OrderViewModel::offlineSales();
            $currentPage = 1;
            $totalPages = 1;
        }

        return view('admin.offline-sales.index', [
            'sales' => $sales,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name_en')->get();
        $customers = Customer::orderBy('name')->get();

        return view('admin.offline-sales.create', [
            'products' => $products,
            'customers' => $customers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'product_id' => 'required|exists:products,id',
            'variant' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];
        $unitPrice = isset($validated['unit_price']) && (float)$validated['unit_price'] > 0
            ? (float) $validated['unit_price']
            : (float) $product->price;

        $discount = isset($validated['discount']) ? (float) $validated['discount'] : 0.00;
        $subtotal = $unitPrice * $qty;
        $discountedSubtotal = max(0, $subtotal - $discount);
        $tax = round($discountedSubtotal * 0.15, 2); // 15% VAT
        $total = $discountedSubtotal + $tax;

        $orderNum = 'POS-' . date('Ymd') . '-' . rand(100, 999);
        $invNum = 'INV-POS-' . date('Ymd') . '-' . rand(100, 999);
        $variant = $validated['variant'] ?? 'Standard Pack';
        $userName = auth()->user()?->name ?? 'POS Cashier';

        try {
            // Deduct offline stock & record movement
            InventoryService::processOfflineSale(
                product: $product,
                quantity: $qty,
                orderNumber: $orderNum,
                userName: $userName,
                variant: $variant
            );

            // Create Order
            $order = Order::create([
                'order_number' => $orderNum,
                'invoice_number' => $invNum,
                'channel' => 'offline',
                'customer_name' => !empty($validated['customer_name']) ? $validated['customer_name'] : 'Walk-In Boutique VIP',
                'customer_phone' => !empty($validated['customer_phone']) ? $validated['customer_phone'] : '+966 50 000 0000',
                'customer_email' => !empty($validated['customer_email']) ? $validated['customer_email'] : 'walkin@bluezone.com',
                'date' => now()->toDateString(),
                'status' => 'delivered',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => 0.00,
                'tax' => $tax,
                'total' => $total,
                'notes' => "Direct POS counter sale at Flagship Boutique. Cashier: {$userName}",
            ]);

            // Create Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name_en' => $product->name_en,
                'product_name_ar' => $product->name_ar,
                'variant_en' => $variant,
                'sku' => $product->sku,
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'total' => $discountedSubtotal,
                'image' => $product->image ?? 'assets/products/blue-mind.jpg',
            ]);

            return redirect()->route('admin.invoices.print', $order->id)
                ->with('status', app()->getLocale() === 'ar'
                    ? "تم تسجيل البيع بنجاح وخصم {$qty} وحدة من مخزون المعرض."
                    : "Offline sale #{$orderNum} recorded and {$qty} units deducted from boutique stock.");
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['sale_error' => $e->getMessage()]);
        }
    }

    public function show(string $id): View
    {
        $dbOrder = is_numeric($id) ? Order::with('items')->find($id) : Order::with('items')->where('order_number', $id)->first();

        if ($dbOrder) {
            $sale = [
                'id' => $dbOrder->id,
                'sale_number' => $dbOrder->order_number,
                'invoice_number' => $dbOrder->invoice_number,
                'store_location' => 'Riyadh Flagship Boutique',
                'cashier' => 'Senior Longevity Specialist',
                'customer_name' => $dbOrder->customer_name,
                'payment_method' => $dbOrder->payment_method,
                'total' => (float) $dbOrder->total,
                'subtotal' => (float) $dbOrder->subtotal,
                'discount' => (float) $dbOrder->discount,
                'tax' => (float) $dbOrder->tax,
                'date' => $dbOrder->date?->format('Y-m-d') ?? now()->toDateString(),
                'time' => $dbOrder->created_at?->format('H:i') ?? '12:00',
                'items' => $dbOrder->items->map(fn ($i) => [
                    'product_name_en' => $i->product_name_en,
                    'variant_en' => $i->variant_en,
                    'sku' => $i->sku,
                    'quantity' => $i->quantity,
                    'unit_price' => (float) $i->unit_price,
                    'total' => (float) $i->total,
                ])->toArray(),
            ];
        } else {
            $sales = OrderViewModel::offlineSales();
            $sale = null;
            foreach ($sales as $s) {
                if ($s['id'] == $id || $s['sale_number'] === $id) {
                    $sale = $s;
                    break;
                }
            }
        }

        return view('admin.offline-sales.show', [
            'sale' => $sale ?? ($sales[0] ?? []),
        ]);
    }
}
