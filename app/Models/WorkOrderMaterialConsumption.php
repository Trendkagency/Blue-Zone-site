<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderMaterialConsumption extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'work_order_id',
        'raw_material_batch_id',
        'quantity',
        'operator_id',
        'scanned_barcode',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'created_at' => 'datetime',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(RawMaterialBatch::class, 'raw_material_batch_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * Immutable model guard
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new DomainException('Work order consumptions are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new DomainException('Work order consumptions are permanent audit records and cannot be deleted.');
        });
    }
}
