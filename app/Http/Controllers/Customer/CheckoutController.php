<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\InventoryService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Render the multi-step customer checkout page.
     */
    public function index(): View|RedirectResponse
    {
        $summary = CartService::getSummary();

        if (empty($summary['items'])) {
            return redirect()->route('customer.cart')
                ->with('error', app()->getLocale() === 'ar' 
                    ? 'سلتك فارغة حالياً. يُرجى اختيار التركيبات للمتابعة إلى الدفع.' 
                    : 'Your protocol cart is empty. Please select formulations before checking out.');
        }

        $activeGateways = PaymentService::getActiveGateways();
        $defaultGateway = PaymentService::getDefaultGateway();

        // Customer prefill if authenticated
        $customer = auth('customer')->user();
        $prefill = [
            'full_name' => $customer?->name ?? '',
            'email' => $customer?->email ?? '',
            'phone' => $customer?->phone ?? '',
            'address' => '',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'postal_code' => '',
        ];

        return view('customer.checkout.index', [
            'summary' => $summary,
            'activeGateways' => $activeGateways,
            'defaultGateway' => $defaultGateway,
            'prefill' => $prefill,
        ]);
    }

    /**
     * Process checkout form submission, order generation, and payment initialization.
     */
    public function store(Request $request): RedirectResponse
    {
        $summary = CartService::getSummary();

        if (empty($summary['items'])) {
            return redirect()->route('customer.cart')
                ->with('error', app()->getLocale() === 'ar' 
                    ? 'سلتك فارغة حالياً.' 
                    : 'Your cart is empty.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', 'in:stripe,card,cod'],
        ]);

        // Verify stock online for each item
        foreach ($summary['items'] as $item) {
            $product = Product::find($item['id']);
            if (!$product || (int)$product->stock_online < (int)$item['quantity']) {
                $pName = app()->getLocale() === 'ar' ? ($product?->name_ar ?? $item['name_ar']) : ($product?->name_en ?? $item['name_en']);
                return redirect()->route('customer.cart')
                    ->with('error', app()->getLocale() === 'ar'
                        ? "الكمية المطلوبة من تركيبة [{$pName}] غير متوفرة في المخزون المباشر."
                        : "The requested quantity for formulation [{$pName}] exceeds available online stock.");
            }
        }

        try {
            $order = DB::transaction(function () use ($validated, $summary) {
                $orderNumber = (string) Setting::get('order_prefix', 'BZ-') . strtoupper(substr(uniqid(), -6));
                $invoiceNumber = (string) Setting::get('invoice_prefix', 'INV-') . date('Ymd') . '-' . rand(1000, 9999);

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'invoice_number' => $invoiceNumber,
                    'channel' => 'online',
                    'customer_name' => $validated['full_name'],
                    'customer_email' => $validated['email'],
                    'customer_phone' => $validated['phone'],
                    'customer_id' => auth('customer')->id(),
                    'date' => now()->toDateString(),
                    'status' => 'Pending',
                    'payment_method' => $validated['payment_method'],
                    'payment_gateway' => $validated['payment_method'],
                    'payment_status' => 'Pending',
                    'subtotal' => $summary['subtotal'],
                    'discount' => $summary['discount'],
                    'coupon_code' => $summary['coupon']['code'] ?? null,
                    'shipping' => $summary['shipping'],
                    'tax' => $summary['tax'],
                    'total' => $summary['total'],
                    'shipping_address' => [
                        'street' => $validated['address'],
                        'city' => $validated['city'],
                        'country' => $validated['country'],
                        'postal_code' => $validated['postal_code'] ?? null,
                    ],
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Create Order Items & Deduct online inventory
                foreach ($summary['items'] as $item) {
                    $product = Product::find($item['id']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'product_name_en' => $item['name_en'],
                        'product_name_ar' => $item['name_ar'] ?? $item['name_en'],
                        'variant_en' => 'Standard 30-Day Protocol',
                        'variant_ar' => 'بروتوكول 30 يوماً القياسي',
                        'sku' => $item['sku'] ?? $product?->sku ?? 'BZ-MED',
                        'unit_price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'image' => $item['image'] ?? '/assets/logo/logo-main.png',
                    ]);

                    if ($product) {
                        InventoryService::adjustStock(
                            $product,
                            'online',
                            -(int)$item['quantity'],
                            'Online Order',
                            "Online Order #{$orderNumber}",
                            $validated['full_name']
                        );
                    }
                }

                return $order;
            });

            // Process Payment
            $paymentResult = PaymentService::process($validated['payment_method'], $order, $request->all());

            // Clear Cart upon successful order generation
            CartService::clear();

            if (!empty($paymentResult['redirect_url'])) {
                return redirect()->to($paymentResult['redirect_url'])
                    ->with('success', $paymentResult['message'] ?? 'Order placed successfully!');
            }

            return redirect()->route('customer.checkout.confirmation', $order->order_number)
                ->with('success', $paymentResult['message'] ?? 'Order placed successfully!');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Render the order confirmation receipt page.
     */
    public function confirmation(string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        return view('customer.checkout.confirmation', [
            'order' => $order,
        ]);
    }
}
