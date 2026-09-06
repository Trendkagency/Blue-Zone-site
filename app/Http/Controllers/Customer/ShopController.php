<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Render the catalog shop with filters and pagination.
     */
    public function index(Request $request): View
    {
        $products = ProductViewModel::all();
        $categories = CategoryViewModel::all();

        // Optional query filtering preparation
        $selectedCategory = $request->query('category');
        $search = $request->query('search');
        $sort = $request->query('sort', 'featured');

        return view('customer.shop.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
            'sort' => $sort,
            'currentPage' => 1,
            'totalPages' => 1,
            'totalItems' => count($products),
        ]);
    }
}
