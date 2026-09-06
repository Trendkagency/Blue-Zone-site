<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\OrderViewModel;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $orders = OrderViewModel::all();

        return view('admin.invoices.index', [
            'orders' => $orders,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }

    public function show(string $id): View
    {
        $order = OrderViewModel::find($id);

        if (!$order) {
            abort(404);
        }

        return view('admin.invoices.show', [
            'order' => $order,
        ]);
    }
}
