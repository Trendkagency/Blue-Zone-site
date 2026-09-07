<?php

namespace App\Services;

use App\Models\Setting;

class CurrencyService
{
    /**
     * Map of supported currencies and their localized properties.
     */
    protected static array $currencies = [
        'SAR' => [
            'name_en' => 'Saudi Riyal',
            'name_ar' => 'ريال سعودي',
            'symbol_en' => 'SAR',
            'symbol_ar' => 'ر.س',
            'position' => 'after',
            'decimals' => 2,
        ],
        'USD' => [
            'name_en' => 'US Dollar',
            'name_ar' => 'دولار أمريكي',
            'symbol_en' => '$',
            'symbol_ar' => '$',
            'position' => 'before',
            'decimals' => 2,
        ],
        'AED' => [
            'name_en' => 'UAE Dirham',
            'name_ar' => 'درهم إماراتي',
            'symbol_en' => 'AED',
            'symbol_ar' => 'د.إ',
            'position' => 'after',
            'decimals' => 2,
        ],
        'EUR' => [
            'name_en' => 'Euro',
            'name_ar' => 'يورو',
            'symbol_en' => '€',
            'symbol_ar' => '€',
            'position' => 'before',
            'decimals' => 2,
        ],
        'GBP' => [
            'name_en' => 'British Pound',
            'name_ar' => 'جنيه إسترليني',
            'symbol_en' => '£',
            'symbol_ar' => '£',
            'position' => 'before',
            'decimals' => 2,
        ],
        'KWD' => [
            'name_en' => 'Kuwaiti Dinar',
            'name_ar' => 'دينار كويتي',
            'symbol_en' => 'KWD',
            'symbol_ar' => 'د.ك',
            'position' => 'after',
            'decimals' => 2,
        ],
        'QAR' => [
            'name_en' => 'Qatari Riyal',
            'name_ar' => 'ريال قطري',
            'symbol_en' => 'QAR',
            'symbol_ar' => 'ر.ق',
            'position' => 'after',
            'decimals' => 2,
        ],
        'BHD' => [
            'name_en' => 'Bahraini Dinar',
            'name_ar' => 'دينار بحريني',
            'symbol_en' => 'BHD',
            'symbol_ar' => 'د.ب',
            'position' => 'after',
            'decimals' => 2,
        ],
        'OMR' => [
            'name_en' => 'Omani Rial',
            'name_ar' => 'ريال عماني',
            'symbol_en' => 'OMR',
            'symbol_ar' => 'ر.ع',
            'position' => 'after',
            'decimals' => 2,
        ],
        'EGP' => [
            'name_en' => 'Egyptian Pound',
            'name_ar' => 'جنيه مصري',
            'symbol_en' => 'EGP',
            'symbol_ar' => 'ج.م',
            'position' => 'after',
            'decimals' => 2,
        ],
    ];

    /**
     * Get all supported currency options for dropdowns.
     */
    public static function supportedCurrencies(): array
    {
        return self::$currencies;
    }

    /**
     * Get current active currency code (e.g. 'SAR', 'USD').
     */
    public static function code(): string
    {
        $code = Setting::get('currency', Setting::get('default_currency', 'SAR'));
        return strtoupper(trim($code ?: 'SAR'));
    }

    /**
     * Get active or specified currency symbol according to locale & overrides.
     */
    public static function symbol(?string $code = null, ?string $locale = null): string
    {
        $code = strtoupper(trim($code ?: self::code()));
        $locale = $locale ?: app()->getLocale();

        // Custom override in setting takes precedence if explicitly set and matching current code
        $customSymbol = Setting::get('currency_symbol');
        if (!empty($customSymbol) && ($code === self::code())) {
            return trim($customSymbol);
        }

        if (isset(self::$currencies[$code])) {
            $curr = self::$currencies[$code];
            return ($locale === 'ar') ? $curr['symbol_ar'] : $curr['symbol_en'];
        }

        return $code;
    }

    /**
     * Get symbol position ('before' or 'after').
     */
    public static function position(?string $code = null, ?string $locale = null): string
    {
        $code = strtoupper(trim($code ?: self::code()));
        $locale = $locale ?: app()->getLocale();

        $savedPos = Setting::get('currency_position');
        if (in_array($savedPos, ['before', 'after'], true)) {
            return $savedPos;
        }

        if (isset(self::$currencies[$code])) {
            return self::$currencies[$code]['position'];
        }

        return 'after';
    }

    /**
     * Get decimal precision.
     */
    public static function decimals(): int
    {
        $dec = Setting::get('currency_decimals');
        if ($dec !== null && is_numeric($dec)) {
            return max(0, min(4, (int) $dec));
        }
        return 2;
    }

    /**
     * Format an amount into a standard localized currency string.
     */
    public static function format(mixed $amount, ?string $code = null, ?string $locale = null): string
    {
        if ($amount === null || $amount === '') {
            $num = 0.0;
        } elseif (is_numeric($amount)) {
            $num = (float) $amount;
        } else {
            $num = (float) preg_replace('/[^0-9.-]/', '', (string) $amount);
        }

        $code = strtoupper(trim($code ?: self::code()));
        $locale = $locale ?: app()->getLocale();
        $symbol = self::symbol($code, $locale);
        $position = self::position($code, $locale);
        $decimals = self::decimals();

        $formattedNumber = number_format($num, $decimals, '.', ',');

        if ($position === 'before') {
            // E.g. $150.00 or SAR 150.00 (add space if symbol is alphabetic)
            $separator = (strlen($symbol) > 1 && !preg_match('/^\p{Sc}$/u', $symbol)) ? ' ' : '';
            return $symbol . $separator . $formattedNumber;
        }

        // E.g. 150.00 ر.س or 150.00 SAR
        return $formattedNumber . ' ' . $symbol;
    }

    /**
     * Get Javascript configuration for frontend real-time sync.
     */
    public static function jsConfig(): array
    {
        $code = self::code();
        $locale = app()->getLocale();

        return [
            'code' => $code,
            'symbol' => self::symbol($code, $locale),
            'position' => self::position($code, $locale),
            'decimals' => self::decimals(),
        ];
    }
}
