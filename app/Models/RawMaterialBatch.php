<?php

namespace App\Models;

use App\Enums\RawMaterialBatchStatus;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class RawMaterialBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'raw_material_id',
        'batch_number',
        'supplier_lot',
        'purchase_order_id',
        'goods_receipt_id',
        'supplier_id',
        'location_id',
        'storage_bin',
        'initial_qty',
        'current_qty',
        'available_qty',
        'reserved_qty',
        'quarantine_qty',
        'unit_cost',
        'manufacture_date',
        'expiry_date',
        'retest_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => RawMaterialBatchStatus::class,
            'initial_qty' => 'decimal:4',
            'current_qty' => 'decimal:4',
            'available_qty' => 'decimal:4',
            'reserved_qty' => 'decimal:4',
            'quarantine_qty' => 'decimal:4',
            'unit_cost' => 'decimal:4',
            'manufacture_date' => 'date',
            'expiry_date' => 'date',
            'retest_date' => 'date',
        ];
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(QcInspection::class);
    }

    public function latestInspection(): HasOne
    {
        return $this->hasOne(QcInspection::class)->latestOfMany();
    }

    public function movements(): HasMany
    {
        return $this->hasMany(RawMaterialMovement::class);
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(WorkOrderMaterialConsumption::class);
    }

    /**
     * Scope for strict FEFO ordering of available approved stock
     */
    public function scopeFefoAvailable(Builder $query): Builder
    {
        return $query->where('status', RawMaterialBatchStatus::Approved)
            ->where('available_qty', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Enforce and assert batch mathematical invariant
     */
    public function verifyInvariant(): bool
    {
        $calculated = round((float) $this->available_qty + (float) $this->reserved_qty + (float) $this->quarantine_qty, 4);
        $current = round((float) $this->current_qty, 4);

        return abs($current - $calculated) <= 0.0001;
    }

    public function assertInvariant(): void
    {
        if (! $this->verifyInvariant()) {
            $calculated = round((float) $this->available_qty + (float) $this->reserved_qty + (float) $this->quarantine_qty, 4);
            $current = round((float) $this->current_qty, 4);
            throw new DomainException("Batch invariant violation on [{$this->batch_number}]: current_qty ({$current}) != available ({$this->available_qty}) + reserved ({$this->reserved_qty}) + quarantine ({$this->quarantine_qty})");
        }
    }

    public static function generateBatchNumber(): string
    {
        $ym = date('Ym');
        $prefix = "LOT-RM-{$ym}-";

        return DB::transaction(function () use ($prefix) {
            $latest = self::where('batch_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $seq = 1;
            if ($latest && preg_match('/LOT-RM-\d{6}-(\d+)/', $latest->batch_number, $matches)) {
                $seq = ((int) $matches[1]) + 1;
            }

            return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        });
    }

    protected static function booted(): void
    {
        static::creating(function (RawMaterialBatch $batch) {
            if ($batch->initial_qty === null) {
                $batch->initial_qty = $batch->current_qty ?? 0;
            }
            if ($batch->current_qty === null) {
                $batch->current_qty = $batch->initial_qty ?? 0;
            }
            if ($batch->unit_cost === null) {
                $batch->unit_cost = $batch->rawMaterial?->standard_cost ?? 0.0000;
            }
            if ($batch->manufacture_date === null) {
                $batch->manufacture_date = now()->subMonth();
            }
            if (empty($batch->batch_number)) {
                $batch->batch_number = self::generateBatchNumber();
            }
        });
    }
}
