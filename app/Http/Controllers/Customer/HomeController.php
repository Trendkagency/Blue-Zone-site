<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\ContentViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Render the Blue Zone flagship homepage.
     */
    public function index(): View
    {
        $allProducts = ProductViewModel::all();
        $featuredProducts = array_filter($allProducts, fn ($p) => $p['is_featured'] ?? false);
        $bestSellers = array_filter($allProducts, fn ($p) => $p['is_best_seller'] ?? false);
        $categories = CategoryViewModel::all();
        $content = ContentViewModel::all();

        return view('customer.home.index', [
            'featuredProducts' => $featuredProducts,
            'bestSellers' => $bestSellers,
            'categories' => $categories,
            'hero' => $content['hero'],
            'zones' => $content['zones'],
            'faqs' => $content['faqs'],
        ]);
    }
}
