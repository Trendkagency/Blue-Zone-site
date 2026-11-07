<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierQuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_quotation_id',
        'raw_material_id',
        'product_id',
        'item_type',
        'item_name',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'total_price',
        'notes',
    ];

    protected static function booted(): void
    {
        static::saving(function (SupplierQuotationItem $item) {
            if (empty($item->item_name)) {
                if ($item->rawMaterial) {
                    $item->item_name = $item->rawMaterial->name_en ?? $item->rawMaterial->name_ar ?? 'Raw Material';
                } elseif ($item->raw_material_id && ($rm = RawMaterial::find($item->raw_material_id))) {
                    $item->item_name = $rm->name_en ?? $rm->name_ar ?? 'Raw Material';
                } elseif ($item->product) {
                    $item->item_name = $item->product->name_en ?? $item->product->name_ar ?? 'Product';
                } elseif ($item->product_id && ($p = Product::find($item->product_id))) {
                    $item->item_name = $p->name_en ?? $p->name_ar ?? 'Product';
                } else {
                    $item->item_name = 'Quoted Supply Item';
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_price' => 'decimal:4',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotation::class, 'supplier_quotation_id');
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
