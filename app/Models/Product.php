<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'subcategory_en',
        'subcategory_ar',
        'brand',
        'price',
        'sale_price',
        'cost_price',
        'is_featured',
        'is_best_seller',
        'is_new',
        'status',
        'rating',
        'reviews_count',
        'image',
        'images',
        'stock_online',
        'stock_offline',
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
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'rating' => 'decimal:1',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_new' => 'boolean',
            'enable_backorders' => 'boolean',
            'images' => 'array',
            'benefits_en' => 'array',
            'benefits_ar' => 'array',
            'ingredients' => 'array',
        ];
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
            return asset('assets/products/blue-mind.jpg');
        }

        $trimmed = trim($url);

        // If it contains /storage/ path (e.g. from Spatie or local uploads on any domain/port)
        if (str_contains($trimmed, '/storage/')) {
            $parsedPath = parse_url($trimmed, PHP_URL_PATH);
            if (!empty($parsedPath)) {
                return asset(ltrim($parsedPath, '/'));
            }
        }

        // If it starts with http:// or https://
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            // If it's pointing to localhost/127.0.0.1 or contains a local port mismatch
            if (str_contains($trimmed, 'localhost') || str_contains($trimmed, '127.0.0.1')) {
                $parsedPath = parse_url($trimmed, PHP_URL_PATH);
                return asset(ltrim($parsedPath ?? '', '/'));
            }
            // If current page is HTTPS and URL is insecure HTTP, upgrade to HTTPS
            if (function_exists('request') && request()?->isSecure() && str_starts_with($trimmed, 'http://')) {
                return 'https://' . substr($trimmed, 7);
            }
            return $trimmed;
        }

        // If it's a known valid asset or starts with assets/ or storage/
        if (str_starts_with($trimmed, 'assets/') || str_starts_with($trimmed, 'storage/')) {
            return asset($trimmed);
        }

        // If it's a local public file
        if (function_exists('public_path') && file_exists(public_path($trimmed))) {
            return asset(ltrim($trimmed, '/'));
        }

        // If it's a random faker text or broken placeholder without file extension
        if (!str_contains($trimmed, '/') && !str_contains($trimmed, '.')) {
            return asset('assets/products/blue-mind.jpg');
        }

        return asset(ltrim($trimmed, '/'));
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
                $urls[] = self::normalizeUrl($media->getUrl());
            }
        }

        if (empty($urls) && !empty($this->images) && is_array($this->images)) {
            foreach ($this->images as $img) {
                $urls[] = self::normalizeUrl($img);
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
        $total = $this->stock_online + $this->stock_offline;
        if ($total <= 0) {
            return 'out_of_stock';
        }
        if ($total <= $this->low_stock_threshold) {
            return 'low_stock';
        }

        return 'in_stock';
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
