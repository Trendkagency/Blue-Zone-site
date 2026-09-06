<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\CustomerViewModel;
use App\View\ViewModels\OrderViewModel;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = CustomerViewModel::all();

        return view('admin.customers.index', [
            'customers' => $customers,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function show(int $id): View
    {
        $customer = CustomerViewModel::find($id);
        $orders = OrderViewModel::all();

        return view('admin.customers.show', [
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }
}
