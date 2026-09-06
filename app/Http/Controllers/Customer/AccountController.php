<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\View\ViewModels\CustomerViewModel;
use App\View\ViewModels\OrderViewModel;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Customer account overview dashboard.
     */
    public function dashboard(): View
    {
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
            ],
        ]);
    }

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
}
