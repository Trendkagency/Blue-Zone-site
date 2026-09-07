<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Render product details with separated customer and professional information.
     */
    public function show(string $slug): View
    {
        $aliases = [
            'blue-energy' => 'blue-cell',
            'blue-immunity' => 'blue-defense',
            'blue-restore' => 'blue-sleep',
            'blue-calm' => 'blue-mind',
        ];
        $slug = $aliases[$slug] ?? $slug;

        try {
            $product = Product::with('category')->where('slug', $slug)->first();
        } catch (\Throwable) {
            $product = null;
        }

        if (!$product) {
            $fallback = ProductViewModel::find($slug);
            if ($fallback) {
                $product = $fallback;
            } else {
                abort(404);
            }
        }

        $productId = is_array($product) ? ($product['id'] ?? 0) : $product->id;

        try {
            $relatedProducts = Product::where('id', '!=', $productId)
                ->where('is_active', true)
                ->take(3)
                ->get();
        } catch (\Throwable) {
            $relatedProducts = collect();
        }

        if ($relatedProducts->isEmpty()) {
            $allProducts = ProductViewModel::all();
            $related = array_filter($allProducts, fn ($p) => ($p['id'] ?? '') !== $slug);
            $relatedProducts = collect(array_slice($related, 0, 3));
        }

        return view('customer.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
