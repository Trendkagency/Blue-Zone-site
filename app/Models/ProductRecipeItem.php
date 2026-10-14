<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecipeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'raw_material_id',
        'quantity_per_unit',
        'quantity',
        'loss_percentage',
        'is_critical',
        'instructions',
    ];

    protected function casts(): array
    {
        return [
            'quantity_per_unit' => 'decimal:6',
            'loss_percentage' => 'decimal:2',
            'is_critical' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    /**
     * Calculate required theoretical quantity for a given production batch size
     */
    public function calculateRequiredQuantity(int $batchUnits): float
    {
        $base = (float) $this->quantity_per_unit * $batchUnits;
        $scrapMultiplier = 1 + ((float) $this->loss_percentage / 100);

        return round($base * $scrapMultiplier, 4);
    }

    public function getQuantityAttribute(): float
    {
        return (float) ($this->quantity_per_unit ?? 0);
    }

    public function setQuantityAttribute(float|int|null $value): void
    {
        $this->attributes['quantity_per_unit'] = $value;
    }
}
