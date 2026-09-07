<?php

use App\Services\CurrencyService;

if (!function_exists('format_currency')) {
    /**
     * Helper to format currency dynamically across the system.
     */
    function format_currency(mixed $amount, ?string $code = null, ?string $locale = null): string
    {
        return CurrencyService::format($amount, $code, $locale);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Helper to get dynamic currency symbol.
     */
    function currency_symbol(?string $code = null, ?string $locale = null): string
    {
        return CurrencyService::symbol($code, $locale);
    }
}

if (!function_exists('currency_code')) {
    /**
     * Helper to get dynamic currency code.
     */
    function currency_code(): string
    {
        return CurrencyService::code();
    }
}
