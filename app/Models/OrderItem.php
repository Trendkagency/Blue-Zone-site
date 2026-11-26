<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_type',
        'product_id',
        'raw_material_id',
        'product_batch_id',
        'raw_material_batch_id',
        'batch_number',
        'expiry_date',
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

    public function getExpiryDateAttribute($value): ?\Illuminate\Support\Carbon
    {
        if (empty($value)) {
            return null;
        }
        if ($value instanceof \Illuminate\Support\Carbon) {
            return $value;
        }
        try {
            return \Illuminate\Support\Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function productBatch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class);
    }

    public function rawMaterialBatch(): BelongsTo
    {
        return $this->belongsTo(RawMaterialBatch::class);
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
                $item->unit_price = round((float) $item->total / max(1, (int) $item->quantity), 2);
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

            // Auto-resolve FEFO batch & expiry if not explicitly provided
            if (!empty($item->product_batch_id) && (empty($item->batch_number) || empty($item->expiry_date))) {
                $linkedBatch = ProductBatch::find($item->product_batch_id);
                if ($linkedBatch) {
                    $item->batch_number = $item->batch_number ?: $linkedBatch->batch_number;
                    $item->expiry_date = $item->expiry_date ?: $linkedBatch->expiry_date;
                }
            } elseif (empty($item->product_batch_id) && !empty($item->product_id) && (empty($item->batch_number) || empty($item->expiry_date))) {
                $fefoBatch = ProductBatch::where('product_id', $item->product_id)
                    ->where('status', 'approved')
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhereDate('expiry_date', '>', now()->toDateString());
                    })
                    ->orderByRaw('CASE WHEN available_qty > 0 THEN 0 ELSE 1 END')
                    ->orderBy('expiry_date', 'asc')
                    ->first();
                if ($fefoBatch) {
                    $item->product_batch_id = $fefoBatch->id;
                    $item->batch_number = $item->batch_number ?: $fefoBatch->batch_number;
                    $item->expiry_date = $item->expiry_date ?: $fefoBatch->expiry_date;
                }
            }

            // Remove non-schema virtual attributes if present
            unset($item->attributes['product_name'], $item->attributes['price']);
        });
    }
}
