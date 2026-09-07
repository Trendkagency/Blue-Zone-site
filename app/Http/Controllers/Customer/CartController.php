<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use App\View\ViewModels\ProductViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Render the customer cart page.
     */
    public function index(): View
    {
        $summary = CartService::getSummary();
        $crossSell = Product::where('status', 'Active')
            ->whereNotIn('id', array_column($summary['items'], 'id'))
            ->take(3)
            ->get();

        return view('customer.cart.index', [
            'cartItems' => $summary['items'],
            'cartCount' => $summary['count'],
            'subtotal' => $summary['subtotal'],
            'discount' => $summary['discount'],
            'coupon' => $summary['coupon'],
            'shipping' => $summary['shipping'],
            'tax' => $summary['tax'],
            'taxPercentage' => $summary['tax_percentage'],
            'total' => $summary['total'],
            'freeShippingThreshold' => $summary['free_shipping_threshold'],
            'neededForFreeShipping' => $summary['needed_for_free_shipping'],
            'freeShippingUnlocked' => $summary['free_shipping_unlocked'],
            'crossSellProducts' => $crossSell,
        ]);
    }

    /**
     * Return JSON representation of cart state for drawer synchronization.
     */
    public function items(): JsonResponse
    {
        return response()->json(CartService::getSummary());
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $productId = $request->input('product_id')
            ?? $request->input('productId')
            ?? $request->input('id')
            ?? $request->input('slug')
            ?? $request->json('product_id')
            ?? $request->json('productId')
            ?? $request->json('id')
            ?? $request->json('slug');

        if (!$productId && $request->getContent()) {
            $decoded = json_decode($request->getContent(), true);
            if (is_array($decoded)) {
                $productId = $decoded['product_id'] ?? $decoded['productId'] ?? $decoded['id'] ?? $decoded['slug'] ?? null;
            }
        }

        $quantity = (int) ($request->input('quantity') ?? $request->json('quantity') ?? 1);
        $fallback = [
            'name' => $request->input('name') ?? $request->json('name'),
            'name_en' => $request->input('name_en') ?? $request->json('name_en'),
            'name_ar' => $request->input('name_ar') ?? $request->json('name_ar'),
            'price' => $request->input('price') ?? $request->json('price'),
            'image' => $request->input('image') ?? $request->json('image'),
            'slug' => $request->input('slug') ?? $request->json('slug'),
            'id' => $productId,
        ];

        try {
            $summary = CartService::add($productId, max(1, $quantity), $fallback);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => app()->getLocale() === 'ar' ? 'تمت إضافة المنتج إلى السلة بنجاح!' : 'Added to cart successfully!',
                    'cart' => $summary,
                ]);
            }

            return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تمت إضافة المنتج إلى السلة بنجاح!' : 'Added to cart!');
        } catch (\Throwable $e) {
            Log::warning('Cart Add Warning: ' . $e->getMessage(), [
                'input' => $request->all(),
                'ip' => $request->ip(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update item quantity.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $productId = $request->input('product_id')
            ?? $request->input('productId')
            ?? $request->input('id')
            ?? $request->json('product_id')
            ?? $request->json('id');
        $quantity = (int) ($request->input('quantity') ?? $request->json('quantity') ?? 1);

        $summary = CartService::updateQuantity($productId, $quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $summary,
            ]);
        }

        return redirect()->route('customer.cart');
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request): JsonResponse|RedirectResponse
    {
        $productId = $request->input('product_id')
            ?? $request->input('productId')
            ?? $request->input('id')
            ?? $request->json('product_id')
            ?? $request->json('id');
        $summary = CartService::remove($productId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $summary,
            ]);
        }

        return redirect()->route('customer.cart')->with('info', app()->getLocale() === 'ar' ? 'تم حذف العنصر من السلة.' : 'Item removed from cart.');
    }

    /**
     * Clear cart.
     */
    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        CartService::clear();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => CartService::getSummary(),
            ]);
        }

        return redirect()->route('customer.cart');
    }

    /**
     * Apply coupon code.
     */
    public function applyCoupon(Request $request): JsonResponse|RedirectResponse
    {
        $code = (string) ($request->input('code') ?? $request->json('code') ?? '');

        try {
            $summary = CartService::applyCoupon($code);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => app()->getLocale() === 'ar' ? 'تم تطبيق رمز الخصم بنجاح!' : 'Coupon applied successfully!',
                    'cart' => $summary,
                ]);
            }

            return redirect()->route('customer.cart')->with('success', app()->getLocale() === 'ar' ? 'تم تطبيق رمز الخصم بنجاح!' : 'Coupon applied!');
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('customer.cart')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove coupon code.
     */
    public function removeCoupon(Request $request): JsonResponse|RedirectResponse
    {
        $summary = CartService::removeCoupon();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $summary,
            ]);
        }

        return redirect()->route('customer.cart');
    }
}
