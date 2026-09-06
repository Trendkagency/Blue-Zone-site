<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
<<<<<<< HEAD
        'product_id', 'location_id', 'location_name_en', 'location_name_ar',
        'variant_en', 'variant_ar',
        'current_stock', 'available_stock', 'reserved_stock',
        'low_stock_threshold', 'status', 'unit_cost', 'retail_price',
=======
        'product_id',
        'location_id',
        'location_name_en',
        'location_name_ar',
        'variant_en',
        'variant_ar',
        'current_stock',
        'available_stock',
        'reserved_stock',
        'low_stock_threshold',
        'status',
        'unit_cost',
        'retail_price',
>>>>>>> origin/main
    ];

    protected function casts(): array
    {
        return [
<<<<<<< HEAD
=======
            'current_stock' => 'integer',
            'available_stock' => 'integer',
            'reserved_stock' => 'integer',
            'low_stock_threshold' => 'integer',
>>>>>>> origin/main
            'unit_cost' => 'decimal:2',
            'retail_price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
<<<<<<< HEAD
=======

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    /**
     * Recompute and save status based on current stock vs threshold.
     */
    public function refreshStatus(): void
    {
        if ($this->current_stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->current_stock <= $this->low_stock_threshold) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }

        $this->available_stock = max(0, $this->current_stock - $this->reserved_stock);
        $this->save();
    }
>>>>>>> origin/main
}
