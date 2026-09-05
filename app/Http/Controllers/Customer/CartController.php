<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Render the customer cart page.
     */
    public function index(): View
    {
        $products = ProductViewModel::all();

        // Sample cart preview data
        $cartItems = [
            [
                'product' => $products[0],
                'variant' => $products[0]['variants'][0],
                'quantity' => 2,
                'unit_price' => 68.00,
                'total' => 136.00,
            ],
            [
                'product' => $products[1],
                'variant' => $products[1]['variants'][0],
                'quantity' => 1,
                'unit_price' => 74.00,
                'total' => 74.00,
            ],
        ];

        $subtotal = 210.00;
        $discount = 21.00; // 10% demo code
        $shipping = 0.00; // Over $75 threshold
        $tax = 28.35;
        $total = 217.35;

        return view('customer.cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'freeShippingThreshold' => 75.00,
            'crossSellProducts' => array_slice($products, 2, 2),
        ]);
    }
}
