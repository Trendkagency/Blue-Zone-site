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
use Illuminate\Support\Str;
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
                    'store_location' => 'Riyadh Central Warehouse',
                    'cashier' => ($order->notes && Str::contains($order->notes, 'Cashier: ')) ? Str::after($order->notes, 'Cashier: ') : 'Senior Longevity Specialist',
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
            'totalCount' => $dbSales->isNotEmpty() ? $dbSales->total() : count($sales),
        ]);
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->with('category')->orderBy('name_en')->get();
        $customers = Customer::orderBy('name')->get();
        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.offline-sales.create', [
            'products' => $products,
            'customers' => $customers,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $cartItemsRaw = $request->input('cart_items');
        $cartItems = null;

        if (!empty($cartItemsRaw)) {
            $cartItems = is_string($cartItemsRaw) ? json_decode($cartItemsRaw, true) : $cartItemsRaw;
        }

        if (!empty($cartItems) && is_array($cartItems)) {
            // Multi-item cart checkout
            $validated = $request->validate([
                'customer_name' => 'nullable|string',
                'customer_phone' => 'nullable|string',
                'customer_email' => 'nullable|email',
                'discount' => 'nullable|numeric|min:0',
                'payment_method' => 'required|string',
                'amount_tendered' => 'nullable|numeric|min:0',
            ]);

            $userName = auth()->user()?->name ?? 'POS Cashier';
            $orderNum = 'POS-' . date('Ymd') . '-' . rand(100, 999);
            $invNum = 'INV-POS-' . date('Ymd') . '-' . rand(100, 999);
            $discount = isset($validated['discount']) ? (float) $validated['discount'] : 0.00;

            try {
                $order = \Illuminate\Support\Facades\DB::transaction(function () use ($cartItems, $validated, $orderNum, $invNum, $discount, $userName) {
                    $subtotal = 0;
                    $processedItems = [];

                    foreach ($cartItems as $item) {
                        $productId = $item['product_id'] ?? null;
                        $qty = (int) ($item['quantity'] ?? 1);
                        if (!$productId || $qty <= 0) continue;

                        $product = Product::findOrFail($productId);
                        $unitPrice = isset($item['unit_price']) && (float) $item['unit_price'] > 0
                            ? (float) $item['unit_price']
                            : (float) $product->price;

                        $itemSubtotal = $unitPrice * $qty;
                        $subtotal += $itemSubtotal;

                        $variant = $item['variant'] ?? 'Standard Pack (60 Caps)';

                        // Process offline sale deduction
                        InventoryService::processOfflineSale(
                            product: $product,
                            quantity: $qty,
                            orderNumber: $orderNum,
                            userName: $userName,
                            variant: $variant
                        );

                        $processedItems[] = [
                            'product' => $product,
                            'variant' => $variant,
                            'unit_price' => $unitPrice,
                            'quantity' => $qty,
                            'total' => $itemSubtotal,
                        ];
                    }

                    if (empty($processedItems)) {
                        throw new InvalidArgumentException(app()->getLocale() === 'ar' ? 'سلة المشتريات فارغة.' : 'Cart is empty.');
                    }

                    $discountedSubtotal = max(0, $subtotal - $discount);
                    $tax = round($discountedSubtotal * 0.15, 2);
                    $total = $discountedSubtotal + $tax;

                    $order = Order::create([
                        'order_number' => $orderNum,
                        'invoice_number' => $invNum,
                        'channel' => 'offline',
                        'customer_name' => !empty($validated['customer_name']) ? $validated['customer_name'] : 'Walk-In Warehouse VIP',
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
                        'notes' => "Direct POS counter sale at POS Warehouse. Cashier: {$userName}",
                    ]);

                    foreach ($processedItems as $pItem) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $pItem['product']->id,
                            'product_name_en' => $pItem['product']->name_en,
                            'product_name_ar' => $pItem['product']->name_ar,
                            'variant_en' => $pItem['variant'],
                            'sku' => $pItem['product']->sku,
                            'unit_price' => $pItem['unit_price'],
                            'quantity' => $pItem['quantity'],
                            'total' => $pItem['total'],
                            'image' => $pItem['product']->image ?? 'assets/products/blue-mind.jpg',
                        ]);
                    }

                    return $order;
                });

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'invoice_number' => $order->invoice_number,
                        'total' => $order->total,
                        'tax' => $order->tax,
                        'subtotal' => $order->subtotal,
                        'discount' => $order->discount,
                        'print_url' => route('admin.invoices.print', $order->id),
                        'message' => app()->getLocale() === 'ar'
                            ? "تم تسجيل البيع بنجاح للطلب #{$order->order_number}."
                            : "Offline sale #{$order->order_number} recorded successfully.",
                    ]);
                }

                return redirect()->route('admin.invoices.print', $order->id)
                    ->with('status', app()->getLocale() === 'ar'
                        ? "تم تسجيل البيع بنجاح للطلب #{$order->order_number}."
                        : "Offline sale #{$order->order_number} recorded successfully.");
            } catch (InvalidArgumentException $e) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
                }
                return back()->withInput()->withErrors(['sale_error' => $e->getMessage()]);
            }
        }

        // Single item fallback (backwards compatibility)
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
                'customer_name' => !empty($validated['customer_name']) ? $validated['customer_name'] : 'Walk-In Warehouse VIP',
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
                'notes' => "Direct POS counter sale at POS Warehouse. Cashier: {$userName}",
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

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'invoice_number' => $order->invoice_number,
                    'total' => $order->total,
                    'print_url' => route('admin.invoices.print', $order->id),
                    'message' => app()->getLocale() === 'ar'
                        ? "تم تسجيل البيع بنجاح وخصم {$qty} وحدة من مخزون المستودع."
                        : "Offline sale #{$orderNum} recorded and {$qty} units deducted from warehouse stock.",
                ]);
            }

            return redirect()->route('admin.invoices.print', $order->id)
                ->with('status', app()->getLocale() === 'ar'
                    ? "تم تسجيل البيع بنجاح وخصم {$qty} وحدة من مخزون المستودع."
                    : "Offline sale #{$orderNum} recorded and {$qty} units deducted from warehouse stock.");
        } catch (InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
            }
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
                'store_location' => 'Riyadh Central Warehouse',
                'cashier' => ($dbOrder->notes && Str::contains($dbOrder->notes, 'Cashier: ')) ? Str::after($dbOrder->notes, 'Cashier: ') : 'Senior Longevity Specialist',
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
