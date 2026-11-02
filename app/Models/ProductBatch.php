<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'batch_number',
        'supplier_lot',
        'work_order_id',
        'goods_receipt_id',
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
        'status',
    ];

    protected function casts(): array
    {
        return [
            'initial_qty' => 'integer',
            'current_qty' => 'integer',
            'available_qty' => 'integer',
            'reserved_qty' => 'integer',
            'quarantine_qty' => 'integer',
            'unit_cost' => 'decimal:4',
            'manufacture_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope for strict FEFO (First Expired, First Out) ordering of available approved finished stock
     */
    public function scopeFefoAvailable(Builder $query): Builder
    {
        return $query->where('status', 'approved')
            ->where('available_qty', '>', 0)
            ->where('expiry_date', '>', now()->toDateString())
            ->orderBy('expiry_date', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Scope near-expiry batches within a given day window (e.g. 90 days)
     */
    public function scopeNearExpiry(Builder $query, int $days = 90): Builder
    {
        $today = now()->toDateString();
        $targetDate = now()->addDays($days)->toDateString();

        return $query->where('status', 'approved')
            ->where('available_qty', '>', 0)
            ->whereBetween('expiry_date', [$today, $targetDate])
            ->orderBy('expiry_date', 'asc');
    }

    /**
     * Scope expired batches
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('expiry_date', '<=', now()->toDateString())
                ->orWhere('status', 'expired');
        });
    }

    /**
     * Calculate remaining days until expiration. Negative means already expired.
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->expiry_date) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->expiry_date->startOfDay(), false);
    }

    /**
     * Get visual status badge configuration
     */
    public function getExpiryBadgeAttribute(): array
    {
        $days = $this->days_until_expiry;

        if ($days <= 0 || $this->status === 'expired') {
            return [
                'level' => 'expired',
                'color' => 'rose',
                'label_en' => 'Expired',
                'label_ar' => 'منتهي الصلاحية',
                'days' => $days,
            ];
        }

        if ($days <= 30) {
            return [
                'level' => 'critical',
                'color' => 'orange',
                'label_en' => "Expires in {$days}d",
                'label_ar' => "ينتهي خلال {$days} يوم",
                'days' => $days,
            ];
        }

        if ($days <= 90) {
            return [
                'level' => 'warning',
                'color' => 'amber',
                'label_en' => "Near Expiry ({$days}d)",
                'label_ar' => "قريب الانتهاء ({$days} يوم)",
                'days' => $days,
            ];
        }

        return [
            'level' => 'optimal',
            'color' => 'emerald',
            'label_en' => "Optimal ({$days}d)",
            'label_ar' => "صالح ({$days} يوم)",
            'days' => $days,
        ];
    }

    /**
     * Enforce mathematical invariant: current = available + reserved + quarantine
     */
    public function verifyInvariant(): bool
    {
        $calculated = (int) $this->available_qty + (int) $this->reserved_qty + (int) $this->quarantine_qty;
        $current = (int) $this->current_qty;

        return $current === $calculated;
    }

    public function assertInvariant(): void
    {
        if (!$this->verifyInvariant()) {
            $calculated = (int) $this->available_qty + (int) $this->reserved_qty + (int) $this->quarantine_qty;
            $current = (int) $this->current_qty;
            throw new DomainException("ProductBatch invariant violation on [{$this->batch_number}]: current_qty ({$current}) != available ({$this->available_qty}) + reserved ({$this->reserved_qty}) + quarantine ({$this->quarantine_qty})");
        }
    }

    /**
     * Deduct allocated quantity with automatic status management
     */
    public function deductQuantity(int $quantity, ?string $reason = null): void
    {
        if ($quantity <= 0) {
            return;
        }

        if ($this->available_qty < $quantity) {
            throw new DomainException("Cannot deduct {$quantity} units from batch {$this->batch_number}. Available: {$this->available_qty}.");
        }

        $this->available_qty -= $quantity;
        $this->current_qty -= $quantity;
        $this->assertInvariant();
        $this->save();
    }

    /**
     * Generate unique Finished Goods Batch Number: LOT-FG-YYYYMM-XXXX
     */
    public static function generateBatchNumber(): string
    {
        $ym = date('Ym');
        $prefix = "LOT-FG-{$ym}-";

        return DB::transaction(function () use ($prefix) {
            $latest = self::where('batch_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $seq = 1;
            if ($latest && preg_match('/LOT-FG-\d{6}-(\d+)/', $latest->batch_number, $matches)) {
                $seq = ((int) $matches[1]) + 1;
            }

            return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        });
    }

    protected static function booted(): void
    {
        static::creating(function (ProductBatch $batch) {
            if (empty($batch->batch_number)) {
                $batch->batch_number = self::generateBatchNumber();
            }

            if ($batch->initial_qty === null) {
                $batch->initial_qty = $batch->current_qty ?? 0;
            }
            if ($batch->current_qty === null) {
                $batch->current_qty = $batch->initial_qty ?? 0;
            }
            if ($batch->available_qty === null && $batch->status === 'approved') {
                $batch->available_qty = $batch->current_qty;
            } elseif ($batch->quarantine_qty === null && $batch->status === 'quarantine') {
                $batch->quarantine_qty = $batch->current_qty;
            }

            if ($batch->unit_cost === null) {
                $batch->unit_cost = $batch->product?->cost_price ?? 0.0000;
            }

            if ($batch->manufacture_date === null) {
                $batch->manufacture_date = now()->toDateString();
            }

            if ($batch->expiry_date === null && $batch->product) {
                $batch->expiry_date = $batch->product->calculateDefaultExpiryDate($batch->manufacture_date);
            }
        });
    }
}

