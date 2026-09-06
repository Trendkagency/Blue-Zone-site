<?php

namespace App\Services;

use App\Models\Setting;

class TypographyService
{
    /**
     * Curated collection of high quality Arabic & Latin Google Fonts.
     *
     * @return array<string, array{name: string, label: string, category: string, weights: int[], preview_ar: string, preview_en: string, description: string}>
     */
    public static function getAvailableFonts(): array
    {
        return [
            'Mont Blanc' => [
                'name' => 'Mont Blanc',
                'label' => 'Mont Blanc & Tajawal (مون بلان مع تجوال)',
                'category' => 'Bilingual Luxury Sans',
                'weights' => [400, 500, 600, 700, 800],
                'preview_ar' => 'بلوزون — هندسة الصحة الخلوية وطول العمر',
                'preview_en' => 'MONT BLANC & TAJAWAL BILINGUAL',
                'description' => 'Ultra-clean geometric luxury typography (Mont Blanc / Montserrat) paired seamlessly with Tajawal for modern Arabic interfaces.',
            ],
            'Cairo' => [
                'name' => 'Cairo',
                'label' => 'Cairo (القاهرة)',
                'category' => 'Arabic Modern Sans',
                'weights' => [400, 500, 600, 700, 800],
                'preview_ar' => 'بلوزون — هندسة الصحة وطول العمر',
                'preview_en' => 'BLUE ZONE — Cellular Health & Longevity',
                'description' => 'A versatile, balanced contemporary typeface with broad geometric Arabic & Latin letterforms.',
            ],
            'Tajawal' => [
                'name' => 'Tajawal',
                'label' => 'Tajawal (تجوال)',
                'category' => 'Arabic Modern Sans',
                'weights' => [400, 500, 700, 800],
                'preview_ar' => 'تجربة تسوق سلسة وسريعة للعملاء',
                'preview_en' => 'Seamless digital commerce experience',
                'description' => 'Crisp, contemporary sans-serif with smooth low-contrast curves, highly legible on screens.',
            ],
            'Almarai' => [
                'name' => 'Almarai',
                'label' => 'Almarai (المراعي)',
                'category' => 'Contemporary Editorial',
                'weights' => [300, 400, 700, 800],
                'preview_ar' => 'أحدث المكملات الغذائية والحلول الحيوية',
                'preview_en' => 'Pinnacle of cellular nutrition & biology',
                'description' => 'Engineered by Dalton Maag for high-end digital editorial and enterprise interfaces.',
            ],
            'Readex Pro' => [
                'name' => 'Readex Pro',
                'label' => 'Readex Pro (ريديكس)',
                'category' => 'Clean Tech Geometric',
                'weights' => [300, 400, 500, 600, 700],
                'preview_ar' => 'واجهة تقنية متطورة وقراءة مريحة',
                'preview_en' => 'Engineered for high readability and UI speed',
                'description' => 'Variable font designed specifically to minimize reading strain on high-density screens.',
            ],
            'Alexandria' => [
                'name' => 'Alexandria',
                'label' => 'Alexandria (الإسكندرية)',
                'category' => 'Distinctive Brand Identity',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'هوية بصرية فاخرة وحضور رقمي استثنائي',
                'preview_en' => 'Elevated typographic presence for luxury brands',
                'description' => 'Character-rich geometric font with confident proportions and stunning modern appeal.',
            ],
            'IBM Plex Sans Arabic' => [
                'name' => 'IBM Plex Sans Arabic',
                'label' => 'IBM Plex Sans Arabic (آي بي إم بلكس)',
                'category' => 'Corporate & Enterprise',
                'weights' => [300, 400, 500, 600, 700],
                'preview_ar' => 'إدارة المخزون والعمليات اللوجستية بدقة',
                'preview_en' => 'Enterprise logistics and omnichannel control',
                'description' => 'IBM corporate typeface crafted to illustrate the harmony between human and machine.',
            ],
            'Noto Sans Arabic' => [
                'name' => 'Noto Sans Arabic',
                'label' => 'Noto Sans Arabic (نوتو سانس)',
                'category' => 'Universal Standard',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'تطابق عالمي متكامل مع كافة المنصات',
                'preview_en' => 'Google flagship global typography standard',
                'description' => 'Google global typographic masterpiece with harmonized glyphs across languages.',
            ],
            'Rubik' => [
                'name' => 'Rubik',
                'label' => 'Rubik (روبيك)',
                'category' => 'Friendly Modern Rounded',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'تصميم دافئ وودود يرحب بالعملاء',
                'preview_en' => 'Warm, inviting UI with subtly rounded corners',
                'description' => 'Modern sans-serif with softly rounded corners for friendly, accessible user experiences.',
            ],
            'El Messiri' => [
                'name' => 'El Messiri',
                'label' => 'El Messiri (المسيري)',
                'category' => 'Luxury & Elegant Curves',
                'weights' => [400, 500, 600, 700],
                'preview_ar' => 'أناقة كلاسيكية تليق بالمنتجات الفاخرة',
                'preview_en' => 'Classical calligraphy meets modern luxury',
                'description' => 'Harmonious blend of traditional Ruqaa calligraphy and contemporary display typography.',
            ],
            'Amiri' => [
                'name' => 'Amiri',
                'label' => 'Amiri (أميري)',
                'category' => 'Heritage & Traditional Luxury',
                'weights' => [400, 700],
                'preview_ar' => 'عراقة الخط العربي وأصالته الملكية',
                'preview_en' => 'Prestigious Naskh typography and heritage',
                'description' => 'Classical Arabic Naskh typeface revivified for premium literary and royal branding.',
            ],
            'Marhey' => [
                'name' => 'Marhey',
                'label' => 'Marhey (مرحي)',
                'category' => 'Playful Display',
                'weights' => [400, 500, 600, 700],
                'preview_ar' => 'حيوية وشغف بالصحة والنشاط اليومي',
                'preview_en' => 'Vibrant lifestyle energy and visual delight',
                'description' => 'Fluid, dynamic Arabic display typeface overflowing with motion and cheerful vitality.',
            ],
            'Changa' => [
                'name' => 'Changa',
                'label' => 'Changa (شانغا)',
                'category' => 'Bold Industrial Display',
                'weights' => [400, 500, 600, 700, 800],
                'preview_ar' => 'عناوين قوية تبرز العروض الحصرية',
                'preview_en' => 'High-impact geometric display for banners',
                'description' => 'Punchy geometric Arabic and Latin font that stands out emphatically in marketing headlines.',
            ],
            'Inter' => [
                'name' => 'Inter',
                'label' => 'Inter (إنتر)',
                'category' => 'Global UI Benchmark',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'نظام واجهات رقمية احترافي وعالمي',
                'preview_en' => 'The world-standard variable typeface for screens',
                'description' => 'The premier open-source font engineered for computerized user interfaces.',
            ],
            'Plus Jakarta Sans' => [
                'name' => 'Plus Jakarta Sans',
                'label' => 'Plus Jakarta Sans (بلس جاكرتا)',
                'category' => 'Trendy SaaS / Brand',
                'weights' => [300, 400, 500, 600, 700, 800],
                'preview_ar' => 'حداثة وأناقة تناسب المنتجات الذكية',
                'preview_en' => 'Fresh, energetic geometric styling for modern SaaS',
                'description' => 'Contemporary geometric sans with clean lines and warm, inviting humanist undertones.',
            ],
            'Outfit' => [
                'name' => 'Outfit',
                'label' => 'Outfit (أوتفت)',
                'category' => 'Sleek Geometric Aesthetic',
                'weights' => [300, 400, 500, 600, 700, 800, 900],
                'preview_ar' => 'رؤية مستقبلية لصحة الجسم والعقل',
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
        $primary = (string) Setting::get('font_family', 'Mont Blanc');
        $heading = (string) Setting::get('font_heading_family', $primary);
        $sizeBase = (string) Setting::get('font_size_base', '16px');
        $weightHeadings = (string) Setting::get('font_weight_headings', '700');
        $weightBody = (string) Setting::get('font_weight_body', '400');
        $letterSpacing = (string) Setting::get('font_letter_spacing', 'normal');
        $provider = (string) Setting::get('font_provider', 'bunny');

        $primary = $primary ?: 'Mont Blanc';
        $heading = $heading ?: $primary;

        return [
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
