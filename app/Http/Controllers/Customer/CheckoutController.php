<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Render the multi-step customer checkout page.
     */
    public function index(): View
    {
        $products = ProductViewModel::all();

        $orderSummary = [
            'items' => [
                [
                    'name_en' => 'BLUE MIND',
                    'name_ar' => 'بلو مايند',
                    'variant_en' => 'Standard 30-Day Protocol (60 Capsules)',
                    'variant_ar' => 'بروتوكول 30 يوماً القياسي (60 كبسولة)',
                    'quantity' => 2,
                    'price' => 68.00,
                    'total' => 136.00,
                    'image' => '/assets/products/blue-mind.jpg',
                ],
                [
                    'name_en' => 'BLUE CELL',
                    'name_ar' => 'بلو سيل',
                    'variant_en' => '30-Day Cell Reserve (60 Capsules)',
                    'variant_ar' => 'احتياطي خلايا 30 يوماً (60 كبسولة)',
                    'quantity' => 1,
                    'price' => 74.00,
                    'total' => 74.00,
                    'image' => '/assets/products/blue-cell.jpg',
                ],
            ],
            'subtotal' => 210.00,
            'discount' => 21.00,
            'coupon_code' => 'LONGEVITY10',
            'shipping' => 0.00,
            'tax' => 28.35,
            'total' => 217.35,
        ];

        return view('customer.checkout.index', [
            'summary' => $orderSummary,
        ]);
    }
}
