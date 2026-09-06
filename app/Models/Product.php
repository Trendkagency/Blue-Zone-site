<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

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
     * Compute total available stock across all channels.
     */
    public function getTotalStockAttribute(): int
    {
        return (int) ($this->stock_online + $this->stock_offline);
    }

    /**
     * Compute stock badge status.
     */
    public function getStockStatusAttribute(): string
    {
        $total = $this->total_stock;

        if ($total <= 0) {
            return 'out_of_stock';
        }
        if ($total <= ($this->low_stock_threshold ?? 5)) {
            return 'low_stock';
        }

        return 'in_stock';
    }
}
