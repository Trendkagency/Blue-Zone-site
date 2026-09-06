<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Render product details with separated customer and professional information.
     */
    public function show(string $slug): View
    {
        $product = ProductViewModel::find($slug);

        if (!$product) {
            abort(404);
        }

        $allProducts = ProductViewModel::all();
        $relatedProducts = array_filter($allProducts, fn ($p) => $p['id'] !== $product['id']);

        return view('customer.products.show', [
            'product' => $product,
            'relatedProducts' => array_slice($relatedProducts, 0, 3),
        ]);
    }
}
