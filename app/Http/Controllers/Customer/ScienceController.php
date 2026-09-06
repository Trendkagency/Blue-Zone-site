<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class ScienceController extends Controller
{
    /**
     * Display the clinical and formulation science details specifically for the requested product.
     *
     * @param string $slug Product slug identifier
     * @return View
     */
    public function show(string $slug): View
    {
        try {
            // Check if product exists in database but is inactive
            $dbProduct = Product::where('slug', $slug)->first();
            if ($dbProduct && (! $dbProduct->is_active || $dbProduct->status === 'inactive')) {
                abort(404, 'Product science dossier not found.');
            }

            // Safely fetch active product with eager loaded category to avoid N+1 queries
            $product = Product::with('category')
                ->where('is_active', true)
                ->where('slug', $slug)
                ->first();
        } catch (\Throwable) {
            $product = null;
        }

        // Fallback to ViewModel if database is seeding or transitioning
        if (! $product) {
            $fallback = ProductViewModel::find($slug);
            if ($fallback && ($fallback['status'] ?? 'active') === 'active') {
                $product = $fallback;
            } else {
                abort(404, 'Product science dossier not found.');
            }
        }

        $productId = is_array($product) ? ($product['id'] ?? 0) : $product->id;

        // Eager-load related active products for the science cross-navigation carousel
        try {
            $relatedProducts = Product::with('category')
                ->where('id', '!=', $productId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->take(4)
                ->get();
        } catch (\Throwable) {
            $relatedProducts = collect();
        }

        if ($relatedProducts->isEmpty()) {
            $all = ProductViewModel::all();
            $filtered = array_filter($all, fn ($p) => ($p['slug'] ?? $p['id'] ?? '') !== $slug);
            $relatedProducts = collect(array_slice($filtered, 0, 4));
        }

        return view('customer.pages.science-details', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
