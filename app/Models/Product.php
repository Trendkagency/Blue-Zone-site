<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
<<<<<<< HEAD

class Product extends Model
{
=======
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;

>>>>>>> origin/main
    protected $fillable = [
        'slug', 'sku', 'barcode', 'name_en', 'name_ar',
        'tagline_en', 'tagline_ar', 'category_id', 'subcategory_en', 'subcategory_ar',
        'brand', 'price', 'sale_price', 'cost_price',
        'is_featured', 'is_best_seller', 'is_new', 'status',
        'rating', 'reviews_count', 'image', 'images',
        'stock_online', 'stock_offline', 'low_stock_threshold',
        'short_description_en', 'short_description_ar',
        'description_en', 'description_ar',
        'usage_en', 'usage_ar', 'science_en', 'science_ar',
        'benefits_en', 'benefits_ar', 'ingredients',
        'target_gender', 'age_group', 'product_size',
        'clinical_mechanism', 'formula_details', 'contraindications', 'warnings',
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
<<<<<<< HEAD
=======
     * Localized name accessor.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && ! empty($this->name_ar) ? $this->name_ar : ($this->name_en ?? '');
    }

    /**
     * Localized short description accessor.
     */
    public function getShortDescriptionAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && ! empty($this->short_description_ar) ? $this->short_description_ar : ($this->short_description_en ?? '');
    }

    /**
     * Localized full description accessor.
     */
    public function getDescriptionAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && ! empty($this->description_ar) ? $this->description_ar : ($this->description_en ?? '');
    }

    /**
     * Register Spatie media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('primary_image')
            ->singleFile()
            ->useFallbackUrl(asset('assets/products/blue-mind.jpg'));

        $this->addMediaCollection('gallery');
        $this->addMediaCollection('documents');
    }

    /**
     * Register Spatie media conversions.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(600)
            ->nonQueued();
    }

    /**
     * Primary image URL accessor.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        if ($this->hasMedia('primary_image')) {
            return $this->getFirstMediaUrl('primary_image');
        }

        if (! empty($this->image)) {
            return str_starts_with($this->image, 'http') ? $this->image : asset($this->image);
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

        if ($this->hasMedia('gallery')) {
            foreach ($this->getMedia('gallery') as $media) {
                $urls[] = $media->getUrl();
            }
        }

        if (empty($urls) && ! empty($this->images) && is_array($this->images)) {
            foreach ($this->images as $img) {
                $urls[] = str_starts_with($img, 'http') ? $img : asset($img);
            }
        }

        if (empty($urls)) {
            $urls[] = $this->primary_image_url;
        }

        return $urls;
    }

    /**
>>>>>>> origin/main
     * Get stock status for display.
     */
    public function getStockStatusAttribute(): string
    {
        $total = $this->stock_online + $this->stock_offline;
<<<<<<< HEAD
        if ($total <= 0) return 'out_of_stock';
        if ($total <= $this->low_stock_threshold) return 'low_stock';
=======
        if ($total <= 0) {
            return 'out_of_stock';
        }
        if ($total <= $this->low_stock_threshold) {
            return 'low_stock';
        }

>>>>>>> origin/main
        return 'in_stock';
    }
}
