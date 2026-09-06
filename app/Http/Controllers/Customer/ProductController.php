<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
=======
use App\Models\Product;
>>>>>>> origin/main
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Render product details with separated customer and professional information.
     */
    public function show(string $slug): View
    {
<<<<<<< HEAD
        $product = ProductViewModel::find($slug);

        if (!$product) {
            abort(404);
        }

        $allProducts = ProductViewModel::all();
        $relatedProducts = array_filter($allProducts, fn ($p) => $p['id'] !== $product['id']);

        return view('customer.products.show', [
            'product' => $product,
            'relatedProducts' => array_slice($relatedProducts, 0, 3),
=======
        $product = Product::with('category')->where('slug', $slug)->first();

        if (!$product) {
            $fallback = ProductViewModel::find($slug);
            if ($fallback) {
                $product = $fallback;
            } else {
                abort(404);
            }
        }

        $productId = is_array($product) ? ($product['id'] ?? 0) : $product->id;

        $relatedProducts = Product::where('id', '!=', $productId)
            ->where('is_active', true)
            ->take(3)
            ->get();

        if ($relatedProducts->isEmpty()) {
            $allProducts = ProductViewModel::all();
            $related = array_filter($allProducts, fn ($p) => ($p['id'] ?? '') !== $slug);
            $relatedProducts = collect(array_slice($related, 0, 3));
        }

        return view('customer.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
>>>>>>> origin/main
        ]);
    }
}
