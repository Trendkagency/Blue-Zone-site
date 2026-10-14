<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'raw_material_id',
        'ordered_qty',
        'quantity_ordered',
        'received_qty',
        'quantity_received',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'ordered_qty' => 'decimal:4',
            'received_qty' => 'decimal:4',
            'unit_price' => 'decimal:4',
            'total_price' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function getPendingQtyAttribute(): float
    {
        return max(0.0, (float) $this->ordered_qty - (float) $this->received_qty);
    }

    public function getQuantityOrderedAttribute(): float
    {
        return (float) ($this->ordered_qty ?? 0);
    }

    public function setQuantityOrderedAttribute(float|int|null $value): void
    {
        $this->attributes['ordered_qty'] = $value;
    }

    public function getQuantityReceivedAttribute(): float
    {
        return (float) ($this->received_qty ?? 0);
    }

    public function setQuantityReceivedAttribute(float|int|null $value): void
    {
        $this->attributes['received_qty'] = $value;
    }
}
