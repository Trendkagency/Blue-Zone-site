<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 
        'product_id',
        'product_name_en', 
        'product_name_ar',
        'product_name', // legacy alias for product_name_en
        'variant_en', 
        'variant_ar', 
        'sku',
        'unit_price', 
        'price', // legacy alias for unit_price
        'quantity', 
        'total', 
        'image',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Auto-fill required schema columns if legacy/generic keys are passed.
     */
    protected static function booted(): void
    {
        static::creating(function (OrderItem $item) {
            // Handle product_name_en
            if (empty($item->product_name_en)) {
                $item->product_name_en = $item->attributes['product_name'] 
                    ?? ($item->product?->name_en 
                    ?? 'Formulation Item');
            }

            // Handle product_name_ar
            if (empty($item->product_name_ar)) {
                $item->product_name_ar = $item->product?->name_ar 
                    ?? $item->product_name_en;
            }

            // Handle unit_price
            if (empty($item->unit_price) && isset($item->attributes['price'])) {
                $item->unit_price = (float) $item->attributes['price'];
            } elseif (empty($item->unit_price) && !empty($item->total) && !empty($item->quantity)) {
                $item->unit_price = round((float)$item->total / max(1, (int)$item->quantity), 2);
            }

            // Handle variant defaults
            if (empty($item->variant_en)) {
                $item->variant_en = 'Standard Pack';
            }
            if (empty($item->variant_ar)) {
                $item->variant_ar = 'العبوة القياسية';
            }

            // Handle SKU default
            if (empty($item->sku)) {
                $item->sku = $item->product?->sku ?? 'BZ-SKU';
            }

            // Handle Image default
            if (empty($item->image)) {
                $item->image = $item->product?->primary_image_url 
                    ?? $item->product?->image 
                    ?? 'assets/products/blue-mind.jpg';
            }

            // Remove non-schema virtual attributes if present
            unset($item->attributes['product_name'], $item->attributes['price']);
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
