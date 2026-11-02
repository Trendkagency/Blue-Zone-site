<?php

namespace App\Models;

use App\Enums\StorageCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'po_item_id',
        'item_type',
        'raw_material_id',
        'product_id',
        'ordered_qty',
        'received_qty',
        'quantity_received',
        'rejected_qty',
        'supplier_lot',
        'manufacture_date',
        'expiry_date',
        'storage_condition',
        'storage_bin',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'ordered_qty' => 'decimal:4',
            'received_qty' => 'decimal:4',
            'rejected_qty' => 'decimal:4',
            'manufacture_date' => 'date',
            'expiry_date' => 'date',
            'storage_condition' => StorageCondition::class,
        ];
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getItemNameAttribute(): string
    {
        if ($this->item_type === 'product' && $this->product) {
            return app()->getLocale() === 'ar' ? ($this->product->name_ar ?? $this->product->name_en) : $this->product->name_en;
        }
        return $this->rawMaterial ? (app()->getLocale() === 'ar' ? ($this->rawMaterial->name_ar ?? $this->rawMaterial->name_en) : $this->rawMaterial->name_en) : '—';
    }

    public function getItemSkuAttribute(): string
    {
        if ($this->item_type === 'product' && $this->product) {
            return $this->product->sku ?? ('PRD-' . $this->product_id);
        }
        return $this->rawMaterial ? ($this->rawMaterial->material_code ?? ('RM-' . $this->raw_material_id)) : '—';
    }

    public function getPoItemIdAttribute(): ?int
    {
        return $this->purchase_order_item_id;
    }

    public function setPoItemIdAttribute(?int $value): void
    {
        $this->attributes['purchase_order_item_id'] = $value;
    }

    public function getQuantityReceivedAttribute(): float
    {
        return (float) ($this->received_qty ?? 0);
    }

    public function setQuantityReceivedAttribute(float|int|null $value): void
    {
        $this->attributes['received_qty'] = $value;
    }

    protected static function booted(): void
    {
        static::creating(function (GoodsReceiptItem $item) {
            if ($item->ordered_qty === null) {
                $item->ordered_qty = $item->purchaseOrderItem?->ordered_qty ?? $item->received_qty ?? 0;
            }
            if ($item->storage_condition === null) {
                if ($item->rawMaterial) {
                    $item->storage_condition = $item->rawMaterial->storage_condition;
                } else {
                    $item->storage_condition = StorageCondition::Ambient;
                }
            }
            if ($item->manufacture_date === null) {
                $item->manufacture_date = now()->subDays(14);
            }
        });
    }
}
