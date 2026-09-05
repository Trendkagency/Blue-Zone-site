<?php

namespace App\Services;

use App\Models\Setting;

class TaxService
{
    /**
     * Get the active VAT/Tax percentage from settings.
     */
    public static function getTaxRate(): float
    {
        if (!static::isTaxEnabled()) {
            return 0.0;
        }

        return (float) Setting::get('tax_percentage', 15.0);
    }

    /**
     * Get the official Tax Registration ID (VAT / ZATCA).
     */
    public static function getTaxNumber(): string
    {
        return (string) Setting::get('tax_number', '31004829100003');
    }

    /**
     * Check if tax calculation is globally enabled.
     */
    public static function isTaxEnabled(): bool
    {
        return (bool) Setting::get('enable_tax', true);
    }

    /**
     * Check whether catalog prices are stored inclusive of tax.
     */
    public static function pricesIncludeTax(): bool
    {
        return (bool) Setting::get('prices_include_tax', false);
    }

    /**
     * Calculate tax amount for a given taxable base amount.
     */
    public static function calculateTax(float $amount, ?float $rate = null): float
    {
        $taxRate = $rate ?? static::getTaxRate();
        if ($taxRate <= 0) {
            return 0.0;
        }

        return round(($amount * ($taxRate / 100)), 2);
    }

    /**
     * Calculate comprehensive price, tax, and margin breakdown for product formulation.
     *
     * @param float $retailPrice
     * @param float $costPrice
     * @param float|null $salePrice
     * @return array<string, mixed>
     */
    public static function breakdownPrice(float $retailPrice, float $costPrice = 0.0, ?float $salePrice = null): array
    {
        $rate = static::getTaxRate();
        $isInclusive = static::pricesIncludeTax();
        $effectivePrice = ($salePrice !== null && $salePrice > 0 && $salePrice < $retailPrice) ? $salePrice : $retailPrice;

        if ($isInclusive) {
            // Price already includes tax: Base = Price / (1 + rate/100)
            $netPrice = round($effectivePrice / (1 + ($rate / 100)), 2);
            $taxAmount = round($effectivePrice - $netPrice, 2);
            $grossPrice = $effectivePrice;
        } else {
            // Price is net: Tax is added on top
            $netPrice = $effectivePrice;
            $taxAmount = round($effectivePrice * ($rate / 100), 2);
            $grossPrice = round($netPrice + $taxAmount, 2);
        }

        $profitMargin = round($netPrice - $costPrice, 2);
        $profitMarginPct = ($netPrice > 0) ? round(($profitMargin / $netPrice) * 100, 1) : 0.0;

        return [
            'cost_price' => $costPrice,
            'effective_price' => $effectivePrice,
            'net_price' => $netPrice,
            'tax_rate' => $rate,
            'tax_amount' => $taxAmount,
            'gross_price' => $grossPrice,
            'profit_margin' => $profitMargin,
            'profit_margin_percentage' => $profitMarginPct,
            'is_inclusive' => $isInclusive,
            'tax_number' => static::getTaxNumber(),
        ];
    }

    /**
     * Compute subtotal, tax amount, and grand total for carts, checkout, POS, and orders.
     *
     * @param float $subtotal
     * @param float $discount
     * @param float $shipping
     * @return array<string, float>
     */
    public static function calculateOrderTotals(float $subtotal, float $discount = 0.0, float $shipping = 0.0): array
    {
        $taxableAmount = max(0.0, round($subtotal - $discount, 2));
        $taxAmount = static::calculateTax($taxableAmount);
        $grandTotal = round($taxableAmount + $taxAmount + $shipping, 2);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'taxable_amount' => $taxableAmount,
            'tax_rate' => static::getTaxRate(),
            'tax_amount' => $taxAmount,
            'shipping' => $shipping,
            'grand_total' => $grandTotal,
        ];
    }
}
