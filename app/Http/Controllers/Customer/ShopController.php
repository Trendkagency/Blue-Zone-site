<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
=======
use App\Models\Category;
use App\Models\Product;
>>>>>>> origin/main
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
<<<<<<< HEAD
        $products = ProductViewModel::all();
        $categories = CategoryViewModel::all();

        // Optional query filtering preparation
        $selectedCategory = $request->query('category');
        $search = $request->query('search');
        $sort = $request->query('sort', 'featured');
=======
        $categorySlug = $request->query('category');
        $search = $request->query('search');
        $sort = $request->query('sort', 'featured');
        $goal = $request->query('goal');

        $query = Product::with('category')->where('is_active', true);

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('short_description_en', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($goal) {
            $query->where('health_goal', $goal);
        }

        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->orderBy('sort_order', 'asc');
                break;
        }

        $products = $query->get();

        // Fallback to ViewModel if database records are empty
        if ($products->isEmpty() && !$search && !$categorySlug) {
            $products = collect(ProductViewModel::all());
        }

        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        if ($categories->isEmpty()) {
            $categories = collect(CategoryViewModel::all());
        }
>>>>>>> origin/main

        return view('customer.shop.index', [
            'products' => $products,
            'categories' => $categories,
<<<<<<< HEAD
            'selectedCategory' => $selectedCategory,
=======
            'selectedCategory' => $categorySlug,
>>>>>>> origin/main
            'search' => $search,
            'sort' => $sort,
            'currentPage' => 1,
            'totalPages' => 1,
<<<<<<< HEAD
            'totalItems' => count($products),
=======
            'totalItems' => $products->count(),
>>>>>>> origin/main
        ]);
    }
}
