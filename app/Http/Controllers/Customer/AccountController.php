<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\View\ViewModels\CustomerViewModel;
use App\View\ViewModels\OrderViewModel;
=======
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
>>>>>>> origin/main
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Customer account overview dashboard.
     */
    public function dashboard(): View
    {
<<<<<<< HEAD
        $customer = CustomerViewModel::find(1);
        $orders = OrderViewModel::all();

        return view('customer.account.dashboard', [
            'customer' => $customer,
            'recentOrders' => array_slice($orders, 0, 3),
            'stats' => [
                'total_orders' => count($orders),
                'active_protocol' => 'Cellular Longevity & Nootropic',
                'loyalty_points' => 840,
                'tier' => 'Platinum Biohacker',
=======
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return redirect()->route('customer.auth.login');
        }

        $orders = Order::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)
              ->orWhere('customer_email', $customer->email);
        })->latest('date')->latest('id')->get();

        return view('customer.account.dashboard', [
            'customer' => $customer,
            'recentOrders' => $orders->take(5),
            'stats' => [
                'total_orders' => $orders->count(),
                'total_spent' => (float) $orders->sum('total'),
                'loyalty_points' => $customer->loyalty_points ?? 100,
                'tier' => $customer->tier,
                'saved_addresses_count' => count($customer->getAddressesList()),
                'wishlist_count' => count($customer->wishlist ?? []),
>>>>>>> origin/main
            ],
        ]);
    }

<<<<<<< HEAD
    public function profile(): View
    {
        $customer = CustomerViewModel::find(1);
        return view('customer.account.profile', ['customer' => $customer]);
    }

    public function orders(): View
    {
        $orders = OrderViewModel::all();
        return view('customer.account.orders', ['orders' => $orders]);
    }

    public function showOrder(string $orderNumber): View
    {
        $order = OrderViewModel::find($orderNumber);
        return view('customer.account.orders-show', ['order' => $order]);
    }

    public function invoices(): View
    {
        $orders = OrderViewModel::all();
        return view('customer.account.invoices', ['orders' => $orders]);
    }

    public function addresses(): View
    {
        $customer = CustomerViewModel::find(1);
        return view('customer.account.addresses', ['addresses' => $customer['addresses'] ?? []]);
    }

    public function settings(): View
    {
        $customer = CustomerViewModel::find(1);
        return view('customer.account.settings', ['customer' => $customer]);
    }
=======
    /**
     * View personal profile.
     */
    public function profile(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        return view('customer.account.profile', ['customer' => $customer]);
    }

    /**
     * Update customer profile data.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $customer->update($validated);

        // Keep default saved address in sync with primary address
        $addresses = $customer->getAddressesList();
        if (!empty($addresses)) {
            $addresses[0]['recipient'] = $validated['name'];
            $addresses[0]['phone'] = $validated['phone'];
            if (!empty($validated['address'])) {
                $addresses[0]['street'] = $validated['address'];
            }
            if (!empty($validated['city'])) {
                $addresses[0]['city'] = $validated['city'];
            }
            if (!empty($validated['country'])) {
                $addresses[0]['country'] = $validated['country'];
            }
            if (!empty($validated['postal_code'])) {
                $addresses[0]['postal_code'] = $validated['postal_code'];
            }
            $customer->saved_addresses = $addresses;
            $customer->save();
        }

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم حفظ وتحديث بيانات الملف الشخصي بنجاح.'
            : 'Personal profile dossier updated successfully.');
    }

    /**
     * Update customer security password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($validated['current_password'], $customer->password)) {
            return back()->withErrors(['current_password' => app()->getLocale() === 'ar'
                ? 'كلمة المرور الحالية غير صحيحة.'
                : 'The current password provided is incorrect.']);
        }

        $customer->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تغيير وتأمين كلمة المرور بنجاح.'
            : 'Security credentials and password updated successfully.');
    }

    /**
     * Update customer account settings & notifications.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $preferences = [
            'email_orders' => $request->boolean('email_orders'),
            'email_science' => $request->boolean('email_science'),
            'sms_orders' => $request->boolean('sms_orders'),
        ];

        $customer->notification_preferences = $preferences;
        $customer->save();

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم حفظ تفضيلات الاتصال والإشعارات بنجاح.'
            : 'Communication & alert preferences updated successfully.');
    }

    /**
     * View customer orders list with filtering.
     */
    public function orders(Request $request): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $query = Order::with('items')->where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)
              ->orWhere('customer_email', $customer->email);
        })->latest('date')->latest('id');

        $selectedStatus = $request->query('status', 'all');
        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('order_number', 'like', "%{$search}%");
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.account.orders', [
            'orders' => $orders,
            'selectedStatus' => $selectedStatus,
            'search' => $request->query('search', ''),
        ]);
    }

    /**
     * View single order details with visual milestone timeline.
     */
    public function showOrder(string $orderNumber): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        // Security check: ensure order belongs to this customer
        if ($order->customer_id && $order->customer_id !== $customer->id && $order->customer_email !== $customer->email) {
            abort(403, 'Unauthorized access to this order record.');
        }

        return view('customer.account.orders-show', [
            'order' => $order,
            'timeline' => $order->timeline,
        ]);
    }

    /**
     * Printable customer tax invoice.
     */
    public function printInvoice(string $orderNumber): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        if ($order->customer_id && $order->customer_id !== $customer->id && $order->customer_email !== $customer->email) {
            abort(403);
        }

        return view('admin.invoices.print', [
            'order' => $order,
        ]);
    }

    /**
     * 1-Click Re-order from previous order.
     */
    public function reorder(string $orderNumber): RedirectResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        // Retrieve existing session cart
        $cart = session()->get('cart', []);

        foreach ($order->items as $item) {
            $pId = $item->product_id ?? 1;
            if (isset($cart[$pId])) {
                $cart[$pId]['quantity'] += (int) $item->quantity;
            } else {
                $cart[$pId] = [
                    'id' => $pId,
                    'name_en' => $item->product_name_en,
                    'name_ar' => $item->product_name_ar,
                    'variant_en' => $item->variant_en ?? 'Standard Pack',
                    'price' => (float) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'image' => $item->image ?? '/assets/products/blue-mind.jpg',
                ];
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('customer.cart')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم إضافة جميع عناصر الطلب #{$orderNumber} إلى سلتك بنجاح."
                : "All formulations from order #{$orderNumber} have been added to your cart.");
    }

    /**
     * View customer invoices.
     */
    public function invoices(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $orders = Order::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)
              ->orWhere('customer_email', $customer->email);
        })->latest('date')->paginate(15);

        return view('customer.account.invoices', [
            'orders' => $orders,
        ]);
    }

    /**
     * View saved delivery destinations.
     */
    public function addresses(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return view('customer.account.addresses', [
            'customer' => $customer,
            'addresses' => $customer->getAddressesList(),
        ]);
    }

    /**
     * Store new saved delivery destination.
     */
    public function storeAddress(Request $request): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'recipient' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'street' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable'],
        ]);

        $list = $customer->getAddressesList();
        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            foreach ($list as &$addr) {
                $addr['is_default'] = false;
            }
        }

        $newId = count($list) > 0 ? (max(array_column($list, 'id')) + 1) : 1;

        $list[] = [
            'id' => $newId,
            'title' => $validated['title'],
            'recipient' => $validated['recipient'],
            'phone' => $validated['phone'],
            'street' => $validated['street'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'postal_code' => $validated['postal_code'] ?? '12271',
            'is_default' => $isDefault || count($list) === 0,
        ];

        $customer->saved_addresses = array_values($list);
        $customer->save();

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تمت إضافة عنوان التوصيل الجديد بنجاح.'
            : 'New delivery destination registered successfully.');
    }

    /**
     * Update an existing saved address.
     */
    public function updateAddress(Request $request, int $id): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'recipient' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'street' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable'],
        ]);

        $list = $customer->getAddressesList();
        $isDefault = $request->boolean('is_default');

        foreach ($list as &$addr) {
            if ($addr['id'] === $id) {
                $addr['title'] = $validated['title'];
                $addr['recipient'] = $validated['recipient'];
                $addr['phone'] = $validated['phone'];
                $addr['street'] = $validated['street'];
                $addr['city'] = $validated['city'];
                $addr['country'] = $validated['country'];
                $addr['postal_code'] = $validated['postal_code'] ?? '12271';
                if ($isDefault) {
                    $addr['is_default'] = true;
                }
            } elseif ($isDefault) {
                $addr['is_default'] = false;
            }
        }

        $customer->saved_addresses = array_values($list);
        $customer->save();

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تحديث عنوان التوصيل بنجاح.'
            : 'Delivery destination updated successfully.');
    }

    /**
     * Delete a saved address.
     */
    public function destroyAddress(int $id): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $list = $customer->getAddressesList();
        $filtered = array_filter($list, fn ($a) => $a['id'] !== $id);

        // Ensure at least one address remains default
        if (!empty($filtered)) {
            $hasDefault = false;
            foreach ($filtered as $a) {
                if (!empty($a['is_default'])) {
                    $hasDefault = true;
                    break;
                }
            }
            if (!$hasDefault) {
                $firstKey = array_key_first($filtered);
                $filtered[$firstKey]['is_default'] = true;
            }
        }

        $customer->saved_addresses = array_values($filtered);
        $customer->save();

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم حذف عنوان التوصيل بنجاح.'
            : 'Delivery destination deleted successfully.');
    }

    /**
     * Set default delivery address.
     */
    public function setDefaultAddress(int $id): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $list = $customer->getAddressesList();
        foreach ($list as &$addr) {
            $addr['is_default'] = ($addr['id'] === $id);
        }

        $customer->saved_addresses = array_values($list);
        $customer->save();

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تعيين العنوان كوجهة توصيل افتراضية.'
            : 'Default delivery destination updated.');
    }

    /**
     * View account security & settings.
     */
    public function settings(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        return view('customer.account.settings', ['customer' => $customer]);
    }

    /**
     * View customer wishlist / saved formulations.
     */
    public function wishlist(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        $wishlistIds = $customer->wishlist ?? [];

        $products = Product::whereIn('id', $wishlistIds)->get();

        return view('customer.account.wishlist', [
            'customer' => $customer,
            'products' => $products,
        ]);
    }

    /**
     * Toggle item in customer wishlist.
     */
    public function toggleWishlist(Request $request): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        $productId = (int) $request->input('product_id');

        $list = $customer->wishlist ?? [];

        if (in_array($productId, $list, true)) {
            $list = array_values(array_filter($list, fn ($id) => $id !== $productId));
            $msg = app()->getLocale() === 'ar' ? 'تمت إزالة المنتج من قائمة الرغبات.' : 'Product removed from your saved formulations.';
        } else {
            $list[] = $productId;
            $msg = app()->getLocale() === 'ar' ? 'تمت إضافة المنتج إلى قائمة الرغبات.' : 'Product added to your saved formulations.';
        }

        $customer->wishlist = $list;
        $customer->save();

        return back()->with('success', $msg);
    }
>>>>>>> origin/main
}
