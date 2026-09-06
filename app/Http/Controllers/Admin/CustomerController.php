<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\View\ViewModels\CustomerViewModel;
use App\View\ViewModels\OrderViewModel;
=======
use App\Models\Customer;
use App\Models\Order;
use App\View\ViewModels\CustomerViewModel;
use App\View\ViewModels\OrderViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
>>>>>>> origin/main
use Illuminate\View\View;

class CustomerController extends Controller
{
<<<<<<< HEAD
    public function index(): View
    {
        $customers = CustomerViewModel::all();

        return view('admin.customers.index', [
            'customers' => $customers,
            'currentPage' => 1,
            'totalPages' => 1,
=======
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $isTrashed = $status === 'trashed';

        $query = Customer::withCount('orders')->with('orders')->latest();

        if ($isTrashed) {
            $query->onlyTrashed();
        } elseif ($status && in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $trashedCount = Customer::onlyTrashed()->count();
        $activeCount = Customer::count();
        $totalDbCount = Customer::withTrashed()->count();
        $dbCustomers = $query->paginate(15)->withQueryString();

        if ($totalDbCount > 0) {
            $customers = $dbCustomers->map(function ($c) {
                $totalSpent = $c->orders->sum('total');
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'email' => $c->email,
                    'phone' => $c->phone ?? '+966 50 000 0000',
                    'city' => $c->city ?? 'Riyadh',
                    'country' => $c->country ?? 'Saudi Arabia',
                    'tier' => $c->tier ?? 'Member',
                    'orders_count' => $c->orders_count,
                    'total_spent' => (float) $totalSpent,
                    'status' => $c->status ?? 'active',
                    'registered_at' => $c->created_at?->format('M Y') ?? 'Jan 2026',
                    'deleted_at' => $c->deleted_at,
                ];
            })->toArray();
            $currentPage = $dbCustomers->currentPage();
            $totalPages = $dbCustomers->lastPage();
        } else {
            $customers = CustomerViewModel::all();
            $currentPage = 1;
            $totalPages = 1;
        }

        return view('admin.customers.index', [
            'customers' => $customers,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'trashedCount' => $trashedCount,
            'activeCount' => $activeCount,
            'isTrashed' => $isTrashed,
            'currentStatus' => $status,
>>>>>>> origin/main
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

<<<<<<< HEAD
    public function show(int $id): View
    {
        $customer = CustomerViewModel::find($id);
        $orders = OrderViewModel::all();
=======
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
            'password' => 'nullable|string|min:8',
        ]);

        $password = !empty($validated['password']) ? Hash::make($validated['password']) : Hash::make('Customer@123');

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $password,
            'phone' => $validated['phone'] ?? '+966 50 000 0000',
            'city' => $validated['city'] ?? 'Riyadh',
            'country' => $validated['country'] ?? 'Saudi Arabia',
            'address' => $validated['address'] ?? '',
            'postal_code' => $validated['postal_code'] ?? '11564',
            'status' => $validated['status'],
            'registered_at' => now(),
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم تسجيل العميل [{$customer->name}] بنجاح في قاعدة العملاء!" 
                : "Customer [{$customer->name}] created successfully!");
    }

    public function show(int $id): View
    {
        $dbCustomer = Customer::with('orders.items')->find($id);

        if ($dbCustomer) {
            $addresses = [];
            if (!empty($dbCustomer->address)) {
                $addresses[] = [
                    'id' => 1,
                    'is_default' => true,
                    'title' => app()->getLocale() === 'ar' ? 'العنوان الأساسي' : 'Primary Address',
                    'recipient' => $dbCustomer->name,
                    'street' => $dbCustomer->address,
                    'city' => $dbCustomer->city ?? 'Riyadh',
                    'country' => $dbCustomer->country ?? 'Saudi Arabia',
                    'postal_code' => $dbCustomer->postal_code ?? '',
                    'phone' => $dbCustomer->phone ?? '',
                ];
            }

            $customer = [
                'id' => $dbCustomer->id,
                'name' => $dbCustomer->name,
                'email' => $dbCustomer->email,
                'phone' => $dbCustomer->phone ?? '+966 50 000 0000',
                'city' => $dbCustomer->city ?? 'Riyadh',
                'country' => $dbCustomer->country ?? 'Saudi Arabia',
                'address' => $dbCustomer->address ?? '',
                'postal_code' => $dbCustomer->postal_code ?? '',
                'tier' => $dbCustomer->tier ?? 'Member',
                'orders_count' => $dbCustomer->orders->count(),
                'total_spent' => (float) $dbCustomer->orders->sum('total'),
                'status' => $dbCustomer->status ?? 'active',
                'registered_at' => $dbCustomer->created_at?->format('M Y') ?? 'Jan 2026',
                'addresses' => $addresses,
            ];
            $orders = $dbCustomer->orders->map(function ($o) {
                return [
                    'id' => $o->id,
                    'order_number' => $o->order_number,
                    'invoice_number' => $o->invoice_number,
                    'date' => $o->date?->format('Y-m-d') ?? now()->toDateString(),
                    'status' => $o->status,
                    'payment_status' => $o->payment_status,
                    'total' => (float) $o->total,
                ];
            })->toArray();
        } else {
            $customer = CustomerViewModel::find($id) ?? CustomerViewModel::all()[0] ?? [];
            if (!isset($customer['addresses'])) {
                $customer['addresses'] = [];
            }
            $orders = OrderViewModel::all();
        }
>>>>>>> origin/main

        return view('admin.customers.show', [
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }
<<<<<<< HEAD
=======

    public function edit(int $id): View
    {
        $dbCustomer = Customer::find($id);

        if ($dbCustomer) {
            $customer = [
                'id' => $dbCustomer->id,
                'name' => $dbCustomer->name,
                'email' => $dbCustomer->email,
                'phone' => $dbCustomer->phone ?? '+966 50 000 0000',
                'city' => $dbCustomer->city ?? 'Riyadh',
                'country' => $dbCustomer->country ?? 'Saudi Arabia',
                'address' => $dbCustomer->address ?? '',
                'postal_code' => $dbCustomer->postal_code ?? '11564',
                'status' => $dbCustomer->status ?? 'active',
            ];
        } else {
            $customers = CustomerViewModel::all();
            $customer = null;
            foreach ($customers as $c) {
                if ($c['id'] === $id) {
                    $customer = $c;
                    break;
                }
            }
        }

        return view('admin.customers.edit', [
            'customer' => $customer ?? ($customers[0] ?? []),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '+966 50 000 0000',
            'city' => $validated['city'] ?? 'Riyadh',
            'country' => $validated['country'] ?? 'Saudi Arabia',
            'address' => $validated['address'] ?? '',
            'postal_code' => $validated['postal_code'] ?? '11564',
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $customer->update($updateData);

        return redirect()->route('admin.customers.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم تحديث بيانات ملف العميل [{$customer->name}] بنجاح!" 
                : "Customer [{$customer->name}] profile updated successfully!");
    }

    public function destroy(int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);
        $name = $customer->name;
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم نقل العميل [{$name}] إلى سلة المحذوفات بنجاح." 
                : "Customer [{$name}] moved to trash successfully.");
    }

    public function restore(int $id): RedirectResponse
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $name = $customer->name;
        $customer->restore();

        return redirect()->route('admin.customers.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم استعادة حساب العميل [{$name}] بنجاح!" 
                : "Customer [{$name}] restored successfully!");
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $name = $customer->name;

        // Preserve orders by unlinking foreign key
        Order::where('customer_id', $customer->id)->update(['customer_id' => null]);

        $customer->forceDelete();

        return redirect()->route('admin.customers.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم الحذف النهائي لحساب العميل [{$name}] نهائياً!" 
                : "Customer [{$name}] permanently deleted!");
    }
>>>>>>> origin/main
}
