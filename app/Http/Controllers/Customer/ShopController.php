<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
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

        return view('customer.shop.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $categorySlug,
            'search' => $search,
            'sort' => $sort,
            'currentPage' => 1,
            'totalPages' => 1,
            'totalItems' => $products->count(),
        ]);
    }
}
