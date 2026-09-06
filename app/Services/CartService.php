<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class CartService
{
    protected const SESSION_KEY = 'bluezone_cart_items';
    protected const COUPON_KEY = 'bluezone_cart_coupon';

    /**
     * Get all items currently in the cart session.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getItems(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Save items to the cart session.
     *
     * @param array<int, array<string, mixed>> $items
     */
    protected static function saveItems(array $items): void
    {
        Session::put(self::SESSION_KEY, array_values($items));
    }

    /**
     * Add a product to the cart.
     *
     * @param Product|string|int $product
     * @param int $quantity
     * @return array<string, mixed>
     */
    public static function add(Product|string|int $product, int $quantity = 1): array
    {
        if (!$product instanceof Product) {
            $product = Product::where('id', $product)
                ->orWhere('slug', (string)$product)
                ->first();
        }

        if (!$product) {
            throw new InvalidArgumentException(app()->getLocale() === 'ar' ? 'المنتج غير موجود.' : 'Product not found.');
        }

        $maxStock = (int) ($product->stock_online ?? 99);
        if ($maxStock <= 0) {
            throw new InvalidArgumentException(app()->getLocale() === 'ar' 
                ? "عذراً، تركيبة [{$product->name_ar}] نفدت من المخزون حالياً." 
                : "Formulation [{$product->name_en}] is currently out of stock.");
        }

        $items = self::getItems();
        $foundIndex = -1;

        foreach ($items as $idx => $item) {
            if ((int)$item['id'] === (int)$product->id) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex > -1) {
            $newQty = $items[$foundIndex]['quantity'] + $quantity;
            if ($newQty > $maxStock) {
                $newQty = $maxStock;
            }
            $items[$foundIndex]['quantity'] = $newQty;
            $items[$foundIndex]['total'] = round($newQty * $items[$foundIndex]['price'], 2);
        } else {
            $clampedQty = min($quantity, $maxStock);
            $items[] = [
                'id' => $product->id,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'name_en' => $product->name_en,
                'name_ar' => $product->name_ar,
                'price' => (float) $product->price,
                'quantity' => $clampedQty,
                'total' => round($clampedQty * (float) $product->price, 2),
                'image' => $product->image ?? '/assets/logo/logo-main.png',
                'max_stock' => $maxStock,
            ];
        }

        self::saveItems($items);

        return self::getSummary();
    }

    /**
     * Update quantity of an item in the cart.
     */
    public static function updateQuantity(int|string $productId, int $quantity): array
    {
        $items = self::getItems();

        if ($quantity <= 0) {
            return self::remove($productId);
        }

        foreach ($items as $idx => $item) {
            if ((int)$item['id'] === (int)$productId || (string)$item['slug'] === (string)$productId) {
                $product = Product::find($item['id']);
                $maxStock = $product ? (int)$product->stock_online : 99;

                $finalQty = min($quantity, max(1, $maxStock));
                $items[$idx]['quantity'] = $finalQty;
                $items[$idx]['total'] = round($finalQty * $items[$idx]['price'], 2);
                break;
            }
        }

        self::saveItems($items);

        return self::getSummary();
    }

    /**
     * Remove an item from the cart.
     */
    public static function remove(int|string $productId): array
    {
        $items = self::getItems();
        $filtered = array_filter($items, function ($item) use ($productId) {
            return (int)$item['id'] !== (int)$productId && (string)$item['slug'] !== (string)$productId;
        });

        self::saveItems($filtered);

        return self::getSummary();
    }

    /**
     * Clear all items and coupon from the cart.
     */
    public static function clear(): void
    {
        Session::forget([self::SESSION_KEY, self::COUPON_KEY]);
    }

    /**
     * Apply a discount promo coupon.
     */
    public static function applyCoupon(string $code): array
    {
        $normalized = strtoupper(trim($code));

        $validCoupons = [
            'WELCOME15' => ['code' => 'WELCOME15', 'percent' => 15, 'desc' => '15% Welcome Longevity Discount'],
            'LONGEVITY10' => ['code' => 'LONGEVITY10', 'percent' => 10, 'desc' => '10% Protocol Order Discount'],
            'BLUEZONE20' => ['code' => 'BLUEZONE20', 'percent' => 20, 'desc' => '20% VIP Clinical Reserve Discount'],
        ];

        if (!isset($validCoupons[$normalized])) {
            throw new InvalidArgumentException(app()->getLocale() === 'ar'
                ? "رمز الكوبون '{$code}' غير صالح أو منتهي الصلاحية."
                : "Coupon code '{$code}' is invalid or expired.");
        }

        Session::put(self::COUPON_KEY, $validCoupons[$normalized]);

        return self::getSummary();
    }

    /**
     * Remove applied coupon.
     */
    public static function removeCoupon(): array
    {
        Session::forget(self::COUPON_KEY);
        return self::getSummary();
    }

    /**
     * Get applied coupon data.
     */
    public static function getCoupon(): ?array
    {
        return Session::get(self::COUPON_KEY);
    }

    /**
     * Calculate cart subtotal.
     */
    public static function getSubtotal(): float
    {
        $items = self::getItems();
        $subtotal = 0.0;

        foreach ($items as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        return round($subtotal, 2);
    }

    /**
     * Calculate discount amount.
     */
    public static function getDiscount(): float
    {
        $coupon = self::getCoupon();
        if (!$coupon) {
            return 0.0;
        }

        $subtotal = self::getSubtotal();
        $percent = (float) ($coupon['percent'] ?? 0);

        return round(($subtotal * $percent) / 100, 2);
    }

    /**
     * Calculate shipping rate.
     */
    public static function getShipping(): float
    {
        $subtotal = self::getSubtotal();
        if ($subtotal <= 0) {
            return 0.0;
        }

        $threshold = (float) Setting::get('free_shipping_threshold', 75.00);
        $flatRate = (float) Setting::get('flat_shipping_rate', 9.99);

        return $subtotal >= $threshold ? 0.0 : $flatRate;
    }

    /**
     * Calculate dynamic VAT tax.
     */
    public static function getTax(): float
    {
        $enableTax = (bool) Setting::get('enable_tax', true);
        if (!$enableTax) {
            return 0.0;
        }

        $taxRate = (float) Setting::get('tax_percentage', 15.00);
        $taxable = max(0, self::getSubtotal() - self::getDiscount());

        return round(($taxable * $taxRate) / 100, 2);
    }

    /**
     * Calculate grand total.
     */
    public static function getTotal(): float
    {
        $subtotal = self::getSubtotal();
        if ($subtotal <= 0) {
            return 0.0;
        }

        $discount = self::getDiscount();
        $shipping = self::getShipping();
        $tax = self::getTax();

        return round(max(0, ($subtotal - $discount) + $shipping + $tax), 2);
    }

    /**
     * Get total quantity of items in cart.
     */
    public static function getCount(): int
    {
        $items = self::getItems();
        $count = 0;
        foreach ($items as $item) {
            $count += (int)($item['quantity'] ?? 1);
        }
        return $count;
    }

    /**
     * Get complete financial summary of the cart.
     *
     * @return array<string, mixed>
     */
    public static function getSummary(): array
    {
        $subtotal = self::getSubtotal();
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 75.00);
        $neededForFreeShipping = max(0.0, round($freeShippingThreshold - $subtotal, 2));

        return [
            'items' => self::getItems(),
            'count' => self::getCount(),
            'subtotal' => $subtotal,
            'discount' => self::getDiscount(),
            'coupon' => self::getCoupon(),
            'shipping' => self::getShipping(),
            'tax' => self::getTax(),
            'tax_percentage' => (float) Setting::get('tax_percentage', 15.00),
            'total' => self::getTotal(),
            'free_shipping_threshold' => $freeShippingThreshold,
            'needed_for_free_shipping' => $neededForFreeShipping,
            'free_shipping_unlocked' => $subtotal >= $freeShippingThreshold,
        ];
    }
}
