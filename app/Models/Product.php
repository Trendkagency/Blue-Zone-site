<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Product extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;
    protected $fillable = [
        'slug',
        'sku',
        'barcode',
        'name_en',
        'name_ar',
        'tagline_en',
        'tagline_ar',
        'category_id',
        'type',
        'subcategory_en',
        'subcategory_ar',
        'brand',
        'price',
        'sale_price',
        'is_featured',
        'show_on_landing',
        'landing_order',
        'landing_badge_en',
        'landing_badge_ar',
        'is_best_seller',
        'is_new',
        'status',
        'rating',
        'reviews_count',
        'image',
        'images',
        'stock_online',
        'low_stock_threshold',
        'short_description_en',
        'short_description_ar',
        'description_en',
        'description_ar',
        'usage_en',
        'usage_ar',
        'science_en',
        'science_ar',
        'benefits_en',
        'benefits_ar',
        'ingredients',
        'target_gender',
        'age_group',
        'product_size',
        'clinical_mechanism',
        'formula_details',
        'contraindications',
        'warnings',
        'enable_backorders',
        'section_settings',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'rating' => 'decimal:1',
            'is_featured' => 'boolean',
            'show_on_landing' => 'boolean',
            'landing_order' => 'integer',
            'is_best_seller' => 'boolean',
            'is_new' => 'boolean',
            'enable_backorders' => 'boolean',
            'images' => 'array',
            'benefits_en' => 'array',
            'benefits_ar' => 'array',
            'ingredients' => 'array',
            'section_settings' => 'array',
        ];
    }

    /**
     * Generate a unique URL slug based on the product name.
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = 'formulation-' . Str::lower(Str::random(6));
        }

        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if formulation currently has an active promotional discount.
     */
    public function getHasDiscountAttribute(): bool
    {
        return !empty($this->sale_price) && (float) $this->sale_price < (float) $this->price && (float) $this->sale_price > 0;
    }

    /**
     * Calculate discount percentage (e.g. 20 for 20% off).
     */
    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->has_discount || (float) $this->price <= 0) {
            return 0;
        }

        return (int) round((((float) $this->price - (float) $this->sale_price) / (float) $this->price) * 100);
    }

    /**
     * Calculate direct monetary savings amount.
     */
    public function getDiscountAmountAttribute(): float
    {
        if (!$this->has_discount) {
            return 0.0;
        }

        return max(0.0, (float) $this->price - (float) $this->sale_price);
    }

    /**
     * Get active commercial price taking discounts into account.
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->has_discount ? (float) $this->sale_price : (float) $this->price;
    }

    /**
     * Localized discount badge string (e.g. "-20% OFF" or "خصم 20%").
     */
    public function getDiscountBadgeLabelAttribute(): string
    {
        if (!$this->has_discount) {
            return '';
        }

        $pct = $this->discount_percentage;
        $isAr = app()->getLocale() === 'ar';

        if ($pct > 0) {
            return $isAr ? "خصم {$pct}%" : "-{$pct}% OFF";
        }

        $savings = number_format($this->discount_amount, 2);
        return $isAr ? "وفر \${$savings}" : "SAVE \${$savings}";
    }

    /**
     * Scope query to active products configured to display on the landing page.
     */
    public function scopeForLanding($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'active')->orWhere('is_active', true);
        })
        ->where('show_on_landing', true)
        ->orderBy('landing_order', 'asc')
        ->orderBy('sort_order', 'asc')
        ->orderByDesc('is_featured');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function heroSlides(): HasMany
    {
        return $this->hasMany(HeroSlide::class, 'product_id');
    }

    /**
     * Localized name accessor.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && !empty($this->name_ar) ? $this->name_ar : ($this->name_en ?? '');
    }

    /**
     * Localized short description accessor.
     */
    public function getShortDescriptionAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && !empty($this->short_description_ar) ? $this->short_description_ar : ($this->short_description_en ?? '');
    }

    /**
     * Localized full description accessor.
     */
    public function getDescriptionAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && !empty($this->description_ar) ? $this->description_ar : ($this->description_en ?? '');
    }

    /**
     * Localized tagline accessor.
     */
    public function getTaglineAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && !empty($this->tagline_ar) ? $this->tagline_ar : ($this->tagline_en ?? '');
    }

    /**
     * Localized science description accessor.
     */
    public function getScienceAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && !empty($this->science_ar) ? $this->science_ar : ($this->science_en ?? '');
    }

    /**
     * Localized benefits array accessor.
     *
     * @return array<int, string>
     */
    public function getBenefitsAttribute(): array
    {
        $locale = app()->getLocale();
        $benefits = $locale === 'ar' && !empty($this->benefits_ar) ? $this->benefits_ar : ($this->benefits_en ?? []);

        return is_array($benefits) ? $benefits : [];
    }

    /**
     * Localized usage instruction accessor.
     */
    public function getUsageAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && !empty($this->usage_ar) ? $this->usage_ar : ($this->usage_en ?? '');
    }

    /**
     * Normalize any product image URL to prevent Mixed Content, port 8000 mismatch, or broken faker strings.
     */
    public static function normalizeUrl(?string $url): string
    {
        if (empty($url) || !is_string($url)) {
            return '/assets/products/blue-mind.jpg';
        }

        $trimmed = trim($url);

        // If it starts with http:// or https://
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            $parsedPath = parse_url($trimmed, PHP_URL_PATH);
            if (!empty($parsedPath)) {
                $cleanedPath = '/' . ltrim($parsedPath, '/');
                if (str_starts_with($cleanedPath, '/public/storage/')) {
                    $cleanedPath = substr($cleanedPath, 7);
                }
                // Strip foreign domain if path is internal storage, uploads, assets, or images
                if (str_starts_with($cleanedPath, '/storage/') || str_starts_with($cleanedPath, '/uploads/') || str_starts_with($cleanedPath, '/assets/') || str_starts_with($cleanedPath, '/images/')) {
                    return $cleanedPath;
                }
            }

            // If it's pointing to a local or dev domain (localhost, 127.0.0.1, .test)
            if (str_contains($trimmed, 'localhost') || str_contains($trimmed, '127.0.0.1') || str_contains($trimmed, '.test')) {
                return '/' . ltrim($parsedPath ?? '', '/');
            }

            // If current page is HTTPS and URL is insecure HTTP, upgrade to HTTPS
            if (function_exists('request') && request()?->isSecure() && str_starts_with($trimmed, 'http://')) {
                return 'https://' . substr($trimmed, 7);
            }
            return $trimmed;
        }

        // If it contains /storage/ path
        if (str_contains($trimmed, '/storage/')) {
            $parsedPath = parse_url($trimmed, PHP_URL_PATH);
            if (!empty($parsedPath)) {
                $cleaned = '/' . ltrim($parsedPath, '/');
                if (str_starts_with($cleaned, '/public/storage/')) {
                    $cleaned = substr($cleaned, 7);
                }
                return $cleaned;
            }
        }

        // If it starts with public/storage/
        if (str_starts_with($trimmed, 'public/storage/')) {
            return '/' . substr($trimmed, 7);
        }

        // If it starts with slash already
        if (str_starts_with($trimmed, '/')) {
            if (str_starts_with($trimmed, '/public/storage/')) {
                return substr($trimmed, 7);
            }
            return $trimmed;
        }

        // If it's a known valid asset or starts with assets/ or storage/ or uploads/
        if (str_starts_with($trimmed, 'assets/') || str_starts_with($trimmed, 'storage/') || str_starts_with($trimmed, 'uploads/')) {
            return '/' . $trimmed;
        }

        // If it's a raw filename without directory (e.g. 'blue-mind.webp' or 'blue-cell.webp')
        if (!str_contains($trimmed, '/') && (str_ends_with($trimmed, '.webp') || str_ends_with($trimmed, '.jpg') || str_ends_with($trimmed, '.png') || str_ends_with($trimmed, '.jpeg'))) {
            if (function_exists('public_path') && file_exists(public_path('assets/products/' . $trimmed))) {
                return '/assets/products/' . $trimmed;
            }
        }

        // If it's a random faker text or broken placeholder without file extension
        if (!str_contains($trimmed, '/') && !str_contains($trimmed, '.')) {
            return '/assets/products/blue-mind.jpg';
        }

        return '/' . ltrim($trimmed, '/');
    }

    /**
     * Primary image URL accessor.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        if (method_exists($this, 'hasMedia') && $this->hasMedia('primary_image')) {
            $url = $this->getFirstMediaUrl('primary_image');
            if (!empty($url)) {
                return self::normalizeUrl($url);
            }
        }

        if (!empty($this->image)) {
            return self::normalizeUrl($this->image);
        }

        return asset('assets/products/blue-mind.jpg');
    }

    /**
     * Get all gallery image URLs.
     *
     * @return array<int, string>
     */
    public function getGalleryUrlsAttribute(): array
    {
        $urls = [];

        if (method_exists($this, 'hasMedia') && $this->hasMedia('gallery')) {
            foreach ($this->getMedia('gallery') as $media) {
                $norm = self::normalizeUrl($media->getUrl());
                if (!empty($norm) && !in_array($norm, $urls, true)) {
                    $urls[] = $norm;
                }
            }
        }

        if (!empty($this->images) && is_array($this->images)) {
            foreach ($this->images as $img) {
                $norm = self::normalizeUrl($img);
                if (!empty($norm) && !in_array($norm, $urls, true)) {
                    $urls[] = $norm;
                }
            }
        }

        if (empty($urls)) {
            $urls[] = $this->primary_image_url;
        }

        return array_values(array_unique($urls));
    }

    /**
     * Get stock status for display.
     */
    public function getStockStatusAttribute(): string
    {
        $total = (int) $this->stock_online;
        if ($this->relationLoaded('inventoryItems')) {
            $total += (int) $this->inventoryItems->where('location_id', 'central_wh')->sum('quantity');
        }
        if ($total <= 0) {
            return 'out_of_stock';
        }
        if ($total <= $this->low_stock_threshold) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Resolve section customization settings for this product, merging product overrides with global settings.
     *
     * @param string $key Section key ('dossier', 'sec1', 'sec2', 'sec3', 'sec4', 'sec5', 'sec6', 'buy_box', 'related')
     * @return array
     */
    public function getSectionConfig(string $key): array
    {
        $productSettings = is_array($this->section_settings) ? ($this->section_settings[$key] ?? []) : [];
        $isRtl = app()->getLocale() === 'ar';

        // Global defaults from Setting model with fallback defaults
        $defaultLabels = [
            'dossier' => [
                'badge_en' => 'CLINICAL DOSSIER & BIO-MOLECULAR MONOGRAPH',
                'badge_ar' => 'الملف الطبي والمواصفات السريرية المعتمدة',
                'title_en' => 'Comprehensive Formulation Specifications',
                'title_ar' => 'التفاصيل الدقيقة والخصائص الدوائية للمستحضر',
            ],
            'sec1' => [
                'badge_en' => 'Standardized Monograph',
                'badge_ar' => 'النقاء والجرعات القياسية',
                'title_en' => 'Active Bio-Compounds & Potencies',
                'title_ar' => 'المكونات الفعالة والجرعات المعيارية',
            ],
            'sec2' => [
                'badge_en' => 'Physiological Biomarkers',
                'badge_ar' => 'الأثر الفسيولوجي المباشر والمستدام',
                'title_en' => 'Clinical Benefits & Physiological Targets',
                'title_ar' => 'الفوائد السريرية والمسارات البيولوجية',
            ],
            'sec3' => [
                'badge_en' => 'Synergistic Protocol',
                'badge_ar' => 'الجرعات والتكامل الحيوي',
                'title_en' => 'Clinical Protocol & Usage Guidelines',
                'title_ar' => 'بروتوكول الاستخدام والتوجيهات السريرية',
            ],
            'sec4' => [
                'badge_en' => 'Molecular Pharmacokinetics',
                'badge_ar' => 'الأبحاث الصيدلانية وطول العمر',
                'title_en' => 'Cellular Science & Mechanism of Action',
                'title_ar' => 'الآلية العلمية الدوائية المتقدمة',
            ],
            'sec5' => [
                'badge_en' => 'Clinical Safety & Warnings',
                'badge_ar' => 'الأمان والنزاهة الصيدلانية',
                'title_en' => 'Safety Profile, Warnings & Precautions',
                'title_ar' => 'إرشادات السلامة، التحذيرات وموانع الاستعمال',
            ],
            'sec6' => [
                'badge_en' => 'Batch Certification',
                'badge_ar' => 'بيانات التوثيق والضمان',
                'title_en' => 'Monograph & Supply Specifications',
                'title_ar' => 'المواصفات الفنية، التعبئة وضمان النقاء',
            ],
            'buy_box' => [
                'badge_en' => 'Purchase Information',
                'badge_ar' => 'بيانات الشراء',
                'title_en' => 'Order & Quick Checkout',
                'title_ar' => 'طلب المستحضر والشراء السريع',
            ],
            'related' => [
                'badge_en' => 'SYNERGISTIC LONGEVITY PROTOCOLS',
                'badge_ar' => 'التكامل الحيوي',
                'title_en' => 'Complementary Formulations',
                'title_ar' => 'تركيبات متكاملة تعزز النتائج',
            ],
        ];

        $globalEnabled = (bool) Setting::get("pdetail_{$key}_enabled", true);

        $enabledDesktop = array_key_exists('enabled_desktop', $productSettings)
            ? (bool) $productSettings['enabled_desktop'] 
            : $globalEnabled;

        $enabledMobile = array_key_exists('enabled_mobile', $productSettings)
            ? (bool) $productSettings['enabled_mobile'] 
            : $globalEnabled;

        $defaultOpen = array_key_exists('default_open', $productSettings)
            ? (bool) $productSettings['default_open'] 
            : ($key === 'sec1'); // Section 1 opens by default

        $badgeEn = !empty($productSettings['badge_en']) 
            ? $productSettings['badge_en'] 
            : Setting::get("pdetail_{$key}_badge_en", $defaultLabels[$key]['badge_en'] ?? '');
        $badgeAr = !empty($productSettings['badge_ar']) 
            ? $productSettings['badge_ar'] 
            : Setting::get("pdetail_{$key}_badge_ar", $defaultLabels[$key]['badge_ar'] ?? '');
        $titleEn = !empty($productSettings['title_en']) 
            ? $productSettings['title_en'] 
            : Setting::get("pdetail_{$key}_title_en", $defaultLabels[$key]['title_en'] ?? '');
        $titleAr = !empty($productSettings['title_ar']) 
            ? $productSettings['title_ar'] 
            : Setting::get("pdetail_{$key}_title_ar", $defaultLabels[$key]['title_ar'] ?? '');

        $badge = $isRtl && !empty($badgeAr) ? $badgeAr : ($badgeEn ?: $badgeAr);
        $title = $isRtl && !empty($titleAr) ? $titleAr : ($titleEn ?: $titleAr);

        $customContentEnabled = !empty($productSettings['custom_content_enabled']);
        $customContent = $isRtl && !empty($productSettings['custom_content_ar']) 
            ? $productSettings['custom_content_ar'] 
            : ($productSettings['custom_content_en'] ?? $productSettings['custom_content_ar'] ?? null);

        $contentScreenMode = $productSettings['content_screen_mode'] ?? 'unified';
        $customContentMobileEn = $productSettings['custom_content_mobile_en'] ?? '';
        $customContentMobileAr = $productSettings['custom_content_mobile_ar'] ?? '';
        $customContentMobile = $isRtl && !empty($customContentMobileAr)
            ? $customContentMobileAr
            : ($customContentMobileEn ?: $customContentMobileAr ?: null);

        $isVisible = $globalEnabled && ($enabledDesktop || $enabledMobile);

        $responsiveClass = '';
        if (!$enabledDesktop && !$enabledMobile) {
            $responsiveClass = 'hidden';
            $isVisible = false;
        } elseif ($enabledDesktop && !$enabledMobile) {
            $responsiveClass = 'hidden md:block';
        } elseif (!$enabledDesktop && $enabledMobile) {
            $responsiveClass = 'block md:hidden';
        }

        $bundleConfig = ($key === 'related') ? [
            'enabled' => !empty($productSettings['bundle']['enabled']),
            'discount_type' => $productSettings['bundle']['discount_type'] ?? 'percentage',
            'discount_value' => (float) ($productSettings['bundle']['discount_value'] ?? 15.0),
            'badge_en' => $productSettings['bundle']['badge_en'] ?? 'Optimal Synergistic Protocol',
            'badge_ar' => $productSettings['bundle']['badge_ar'] ?? 'البروتوكول التكاملي الموصى به',
            'product_ids' => array_map('intval', (array) ($productSettings['bundle']['product_ids'] ?? [])),
        ] : null;

        return [
            'enabled_desktop' => $enabledDesktop,
            'enabled_mobile' => $enabledMobile,
            'is_visible' => $isVisible,
            'responsive_class' => $responsiveClass,
            'default_open' => $defaultOpen,
            'badge' => $badge,
            'badge_en' => $badgeEn,
            'badge_ar' => $badgeAr,
            'title' => $title,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,
            'custom_content_enabled' => $customContentEnabled,
            'content_screen_mode' => $contentScreenMode,
            'custom_content' => $customContent,
            'custom_content_en' => $productSettings['custom_content_en'] ?? '',
            'custom_content_ar' => $productSettings['custom_content_ar'] ?? '',
            'custom_content_mobile' => $customContentMobile,
            'custom_content_mobile_en' => $customContentMobileEn,
            'custom_content_mobile_ar' => $customContentMobileAr,
            'bundle' => $bundleConfig,
        ];
    }

    /**
     * Get calculated Synergistic Protocol Bundle data for storefront display & 1-click cart addition.
     */
    public function getSynergisticBundle(): ?array
    {
        $relatedConfig = $this->getSectionConfig('related');
        $bundle = $relatedConfig['bundle'] ?? null;

        if (empty($bundle) || empty($bundle['enabled']) || empty($bundle['product_ids'])) {
            return null;
        }

        $bundleProducts = self::whereIn('id', $bundle['product_ids'])
            ->where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->where('status', 'active')->orWhere('is_active', true);
            })
            ->get();

        if ($bundleProducts->isEmpty()) {
            return null;
        }

        $allProducts = collect([$this])->concat($bundleProducts);
        $totalOriginalPrice = $allProducts->sum(fn($p) => (float) ($p->sale_price ?: $p->price));

        $discountValue = (float) $bundle['discount_value'];
        $discountType = $bundle['discount_type'] ?? 'percentage';

        if ($discountType === 'percentage') {
            $discountAmount = ($totalOriginalPrice * $discountValue) / 100.0;
        } else {
            $discountAmount = min($totalOriginalPrice, $discountValue);
        }

        $bundleFinalPrice = max(0, $totalOriginalPrice - $discountAmount);
        $savingsPercentage = $totalOriginalPrice > 0 ? round(($discountAmount / $totalOriginalPrice) * 100) : 0;

        $isRtl = app()->getLocale() === 'ar';
        $badge = $isRtl && !empty($bundle['badge_ar']) 
            ? $bundle['badge_ar'] 
            : (!empty($bundle['badge_en']) ? $bundle['badge_en'] : ($isRtl ? "وفر {$savingsPercentage}% مع البروتوكول" : "Save {$savingsPercentage}% with Protocol"));

        return [
            'enabled' => true,
            'primary' => $this,
            'items' => $bundleProducts,
            'all_products' => $allProducts,
            'total_original' => $totalOriginalPrice,
            'discount_amount' => $discountAmount,
            'final_price' => $bundleFinalPrice,
            'savings_percentage' => $savingsPercentage,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'badge' => $badge,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('primary_image')
            ->singleFile();

        $this->addMediaCollection('gallery');

        $this->addMediaCollection('documents');
    }


    public function reviews($products): HasMany
    {
    }

}
