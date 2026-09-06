<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = ProductViewModel::all();
        $categories = CategoryViewModel::all();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }

    public function create(): View
    {
        $categories = CategoryViewModel::all();

        return view('admin.products.create', [
            'categories' => $categories,
        ]);
    }

    public function show(int $id): View
    {
        $product = ProductViewModel::find($id);

        if (!$product) {
            abort(404);
        }

        return view('admin.products.show', [
            'product' => $product,
        ]);
    }

    public function edit(int $id): View
    {
        $product = ProductViewModel::find($id);
        $categories = CategoryViewModel::all();

        if (!$product) {
            abort(404);
        }

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }
}
