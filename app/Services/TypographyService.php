<?php

namespace App\Services;

use App\Models\Setting;

class TypographyService
{
    /**
     * Curated, modern, high-performance typography catalog for luxury longevity & clinical aesthetic.
     *
     * @return array<string, array{
     *     name: string,
     *     label: string,
     *     category: string,
     *     weights: array<int>,
     *     preview_ar: string,
     *     preview_en: string,
     *     description: string,
     *     is_local?: bool
     * }>
     */
    public static function getAvailableFonts(): array
    {
        return [
            'Mont Blanc' => [
                'name' => 'Mont Blanc',
                'label' => 'Mont Blanc (Default Luxury Brand)',
                'category' => 'Geometric Luxury Brand',
                'weights' => [400, 500, 600, 700, 800],
                'preview_ar' => 'علوم متقدمة لإطالة العمر والنشاط الحيوي',
                'preview_en' => 'Cellular Optimization & Longevity Science',
                'description' => 'Flagship brand font delivering geometric precision and authoritative luxury tone.',
                'is_local' => true,
            ],
            'Cairo' => [
                'name' => 'Cairo',
                'label' => 'Cairo (القاهرة)',
                'category' => 'Contemporary Corporate Sans',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'أفضل جودة سريرية وأمان بيولوجي موثق',
                'preview_en' => 'Clinical excellence and premium readability across mobile & desktop',
                'description' => 'Wide, balanced proportions providing exceptional legibility on digital screens.',
            ],
            'Tajawal' => [
                'name' => 'Tajawal',
                'label' => 'Tajawal (تجوال)',
                'category' => 'Clean Tech Modern',
                'weights' => [300, 400, 500, 700, 800, 900],
                'preview_ar' => 'تصميم عصري ونقي يعكس روح الابتكار والصفاء',
                'preview_en' => 'Streamlined, modern typography crafted for clean digital interfaces',
                'description' => 'Highly versatile geometric sans-serif with a crisp, contemporary aesthetic.',
            ],
            'Almarai' => [
                'name' => 'Almarai',
                'label' => 'Almarai (المراعي)',
                'category' => 'Editorial & Clinical',
                'weights' => [300, 400, 700, 800],
                'preview_ar' => 'دقة عالية في القراءة وتوازن بصري فائق الرقي',
                'preview_en' => 'Engineered specifically for optimal Arabic digital editorial balance',
                'description' => 'Smooth, authoritative font inspired by professional publication standards.',
            ],
            'Readex Pro' => [
                'name' => 'Readex Pro',
                'label' => 'Readex Pro (ريدكس برو)',
                'category' => 'High-Legibility Science',
                'weights' => [300, 400, 500, 600, 700],
                'preview_ar' => 'مصمم خصيصاً للقراءة السريعة والمحتوى العلمي',
                'preview_en' => 'Engineered with Thomas Jockin for scientific clarity and fast perception',
                'description' => 'Variable-based high-legibility typeface ideal for medical and supplement specs.',
            ],
            'Alexandria' => [
                'name' => 'Alexandria',
                'label' => 'Alexandria (الإسكندرية)',
                'category' => 'Futuristic Minimalist',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'خط مستقبلي بلمسات نقية ومظهر فائق الحداثة',
                'preview_en' => 'Forward-looking futuristic geometry with ultra-clean counters',
                'description' => 'Geometric masterpiece giving storefronts an elite, science-backed atmosphere.',
            ],
            'IBM Plex Sans Arabic' => [
                'name' => 'IBM Plex Sans Arabic',
                'label' => 'IBM Plex Sans Arabic (آي بي إم بلكس)',
                'category' => 'Corporate & Scientific Authority',
                'weights' => [300, 400, 500, 600, 700],
                'preview_ar' => 'معايير علمية وتصميم مؤسسي محكم ورصين',
                'preview_en' => 'Engineered by IBM for technical authority and global elegance',
                'description' => 'Industrial-grade clarity, designed for clear data and medical formulations.',
            ],
            'Inter' => [
                'name' => 'Inter',
                'label' => 'Inter (إنتر - English / Bilingual)',
                'category' => 'Neutral Precision Screen',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'واجهات تفاعلية عالمية وسلاسة لا تضاهى',
                'preview_en' => 'The world-standard UI typeface for crisp micro-details',
                'description' => 'Crafted by Rasmus Andersson for high-DPI screens and luxury product cards.',
            ],
            'Plus Jakarta Sans' => [
                'name' => 'Plus Jakarta Sans',
                'label' => 'Plus Jakarta Sans (بلس جاكرتا)',
                'category' => 'Warm Luxury Modern',
                'weights' => [400, 500, 600, 700, 800],
                'preview_ar' => 'أناقة دافئة وحداثة تلائم العلامات الفاخرة',
                'preview_en' => 'Warm geometric modernism for luxury wellness brands',
                'description' => 'Clean geometric lines with friendly warmth, perfect for boutique cosmetics.',
            ],
            'Outfit' => [
                'name' => 'Outfit',
                'label' => 'Outfit (أوتفت)',
                'category' => 'Brand & Display Luxury',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'عناوين استثنائية وجاذبية بصرية ملهمة',
                'preview_en' => 'Vibrant geometric display font for high-impact hero headings',
                'description' => 'Commercial-grade aesthetic built specifically for luxury brand typography.',
            ],
            'Changa' => [
                'name' => 'Changa',
                'label' => 'Changa (شانجا)',
                'category' => 'Square Headline Accent',
                'weights' => [400, 500, 600, 700, 800],
                'preview_ar' => 'قوة الحضور وجرأة العناوين البارزة',
                'preview_en' => 'Heavy square geometry for bold impact headers',
                'description' => 'Square-based solid forms ideal for dramatic promotional banners.',
            ],
            'Vazirmatn' => [
                'name' => 'Vazirmatn',
                'label' => 'Vazirmatn (وزير متن)',
                'category' => 'Balanced Humanist Sans',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'انسيابية طبيعية ومسافات متناسقة لراحة العين',
                'preview_en' => 'Harmonious proportions crafted for extensive reading',
                'description' => 'Very comfortable for reading long clinical case studies and blog entries.',
            ],
            'Space Grotesk' => [
                'name' => 'Space Grotesk',
                'label' => 'Space Grotesk (سبيس جروتسك)',
                'category' => 'Futuristic Tech Display',
                'weights' => [400, 500, 600, 700],
                'preview_ar' => 'جماليات مستقبلية وتقنية تلفت الأنظار فوراً',
                'preview_en' => 'Forward-looking geometric beauty and symmetry',
                'description' => 'Stunning modern geometric font with flawless circular balance, perfect for tech-forward brands.',
            ],
            'Poppins' => [
                'name' => 'Poppins',
                'label' => 'Poppins (بوبينز)',
                'category' => 'Pure Geometric Sans',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'دقة هندسية ووضوح عالي في كل كلمة',
                'preview_en' => 'Crisp circular geometry and balanced aesthetic',
                'description' => 'Pure geometric sans-serif based on strict circles and clean construction.',
            ],
            'Roboto' => [
                'name' => 'Roboto',
                'label' => 'Roboto (روبوتو)',
                'category' => 'Dual Nature Modern Sans',
                'weights' => [300, 400, 500, 700, 900],
                'preview_ar' => 'مرونة عالية وأداء استثنائي عبر الويب',
                'preview_en' => 'Dependable, crystal-clear standard for modern apps',
                'description' => 'Google flagship interface font with friendly, open curves and balanced rhythms.',
            ],
        ];
    }

    /**
     * Get CSS font-family stack for seamless Arabic/Latin bilingual rendering.
     *
     * @param string $family
     * @return string
     */
    public static function getCssStack(string $family): string
    {
        if ($family === 'Mont Blanc') {
            return "'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        }

        if ($family === 'Cairo') {
            return "'Cairo', 'Tajawal', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        }

        return "'{$family}', 'Cairo', 'Tajawal', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    }

    protected static ?array $cachedConfig = null;

    /**
     * Clear the memoized typography configuration.
     */
    public static function clearConfigCache(): void
    {
        static::$cachedConfig = null;
    }

    /**
     * Alias for clearConfigCache.
     */
    public static function clearCache(): void
    {
        static::$cachedConfig = null;
    }

    /**
     * Get active typography configuration with fallbacks.
     *
     * @return array{
     *     font_family: string,
     *     font_heading_family: string,
     *     font_family_stack: string,
     *     font_heading_family_stack: string,
     *     font_size_base: string,
     *     font_weight_headings: string,
     *     font_weight_body: string,
     *     font_letter_spacing: string,
     *     font_provider: string
     * }
     */
    public static function getActiveConfig(): array
    {
        if (!app()->runningUnitTests() && static::$cachedConfig !== null) {
            return static::$cachedConfig;
        }

        $primary = (string) Setting::get('font_family', 'Mont Blanc');
        $heading = (string) Setting::get('font_heading_family', $primary);
        $sizeBase = (string) Setting::get('font_size_base', '16px');
        $weightHeadings = (string) Setting::get('font_weight_headings', '700');
        $weightBody = (string) Setting::get('font_weight_body', '400');
        $letterSpacing = (string) Setting::get('font_letter_spacing', 'normal');
        $provider = (string) Setting::get('font_provider', 'bunny');

        $primary = $primary ?: 'Mont Blanc';
        $heading = $heading ?: $primary;

        $config = [
            'font_family' => $primary,
            'font_heading_family' => $heading,
            'font_family_stack' => self::getCssStack($primary),
            'font_heading_family_stack' => self::getCssStack($heading),
            'font_size_base' => $sizeBase ?: '16px',
            'font_weight_headings' => $weightHeadings ?: '700',
            'font_weight_body' => $weightBody ?: '400',
            'font_letter_spacing' => $letterSpacing ?: 'normal',
            'font_provider' => $provider ?: 'bunny',
        ];

        if (!app()->runningUnitTests()) {
            static::$cachedConfig = $config;
        }

        return $config;
    }

    /**
     * Build Bunny Fonts stylesheet URL (fast, regional MENA edge-cached, highly reliable).
     *
     * @param array<string> $families
     * @return string
     */
    public static function buildBunnyFontsUrl(array $families): string
    {
        $unique = array_unique(array_filter($families));
        if (empty($unique)) {
            $unique = ['Mont Blanc'];
        }

        $available = self::getAvailableFonts();
        $queryParts = [];

        foreach ($unique as $family) {
            if ($family === 'Mont Blanc') {
                $queryParts[] = 'montserrat:400,500,600,700,800';
                $queryParts[] = 'tajawal:400,500,700,800';
                continue;
            }

            $meta = $available[$family] ?? null;
            if (!empty($meta['is_local'])) {
                continue;
            }
            $kebabFamily = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $family), '-'));
            $weights = $meta ? implode(',', $meta['weights']) : '400,500,600,700';

            $queryParts[] = "{$kebabFamily}:{$weights}";
        }

        if (empty($queryParts)) {
            $queryParts[] = 'montserrat:400,700';
            $queryParts[] = 'tajawal:400,700';
        }

        return 'https://fonts.bunny.net/css?family=' . implode('|', array_unique($queryParts)) . '&display=swap';
    }

    /**
     * Build Google Fonts stylesheet link tag for given font families.
     *
     * @param array<string> $families
     * @return string
     */
    public static function buildGoogleFontsUrl(array $families): string
    {
        $unique = array_unique(array_filter($families));
        if (empty($unique)) {
            $unique = ['Mont Blanc'];
        }

        $available = self::getAvailableFonts();
        $queryParts = [];

        foreach ($unique as $family) {
            if ($family === 'Mont Blanc') {
                $queryParts[] = 'family=Montserrat:wght@400;500;600;700;800';
                $queryParts[] = 'family=Tajawal:wght@400;500;700;800';
                continue;
            }

            $meta = $available[$family] ?? null;
            if (!empty($meta['is_local'])) {
                continue;
            }
            $formattedFamily = str_replace(' ', '+', trim($family));
            $weights = $meta ? implode(';', $meta['weights']) : '300;400;500;600;700;800;900';

            $queryParts[] = "family={$formattedFamily}:wght@{$weights}";
        }

        if (empty($queryParts)) {
            $queryParts[] = 'family=Montserrat:wght@400;700';
            $queryParts[] = 'family=Tajawal:wght@400;700';
        }

        return 'https://fonts.googleapis.com/css2?' . implode('&', array_unique($queryParts)) . '&display=swap';
    }

    /**
     * Get primary font URL based on configured provider (default: Bunny Fonts for maximum uptime).
     *
     * @param array<string> $families
     * @return string
     */
    public static function getPrimaryFontsUrl(array $families): string
    {
        $config = self::getActiveConfig();
        if ($config['font_provider'] === 'google') {
            return self::buildGoogleFontsUrl($families);
        }

        return self::buildBunnyFontsUrl($families);
    }

    /**
     * Get fallback font URL for resilient client-side failover.
     *
     * @param array<string> $families
     * @return string
     */
    public static function getFallbackFontsUrl(array $families): string
    {
        $config = self::getActiveConfig();
        if ($config['font_provider'] === 'google') {
            return self::buildBunnyFontsUrl($families);
        }

        return self::buildGoogleFontsUrl($families);
    }
}
