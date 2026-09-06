<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\OrderViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class OfflineSaleController extends Controller
{
    public function index(): View
    {
        $sales = OrderViewModel::offlineSales();

        return view('admin.offline-sales.index', [
            'sales' => $sales,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }

    public function create(): View
    {
        $products = ProductViewModel::all();

        return view('admin.offline-sales.create', [
            'products' => $products,
        ]);
    }

    public function show(string $id): View
    {
        $sales = OrderViewModel::offlineSales();
        $sale = null;
        foreach ($sales as $s) {
            if ($s['id'] == $id || $s['sale_number'] === $id) {
                $sale = $s;
                break;
            }
        }

        return view('admin.offline-sales.show', [
            'sale' => $sale ?? $sales[0],
        ]);
    }
}
