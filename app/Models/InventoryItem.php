<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
        'product_id', 'location_id', 'location_name_en', 'location_name_ar',
        'variant_en', 'variant_ar',
        'current_stock', 'available_stock', 'reserved_stock',
        'low_stock_threshold', 'status', 'unit_cost', 'retail_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'retail_price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
