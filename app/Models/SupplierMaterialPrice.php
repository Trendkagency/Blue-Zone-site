<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierMaterialPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'raw_material_id',
        'unit_price',
        'currency',
        'min_order_qty',
        'lead_time_days',
        'is_preferred',
        'supplier_sku',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:4',
            'min_order_qty' => 'decimal:4',
            'lead_time_days' => 'integer',
            'is_preferred' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
