<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'product_name_en',
        'product_name_ar',
        'sku',
        'variant',
        'movement_type',
        'from_location',
        'to_location',
        'quantity',
        'previous_qty',
        'new_qty',
        'date',
        'time',
        'user',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',

    public function getMovementNumberAttribute(): string
    {
        return 'MOV-' . str_pad((string)$this->id, 5, '0', STR_PAD_LEFT);
    }
}
