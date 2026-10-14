<?php

namespace App\Models;

use App\Enums\WorkOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'wo_number',
        'product_id',
        'target_finished_lot',
        'product_batch_number',
        'planned_qty',
        'target_quantity',
        'produced_qty',
        'rejected_qty',
        'status',
        'location_id',
        'scheduled_start',
        'scheduled_date',
        'started_at',
        'completed_at',
        'total_material_cost',
        'unit_cost',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => WorkOrderStatus::class,
            'planned_qty' => 'integer',
            'produced_qty' => 'integer',
            'rejected_qty' => 'integer',
            'scheduled_start' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'total_material_cost' => 'decimal:4',
            'unit_cost' => 'decimal:4',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(WorkOrderMaterialConsumption::class);
    }

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $prefix = "WO-{$year}-";

        return DB::transaction(function () use ($prefix) {
            $latest = self::where('order_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $seq = 1;
            if ($latest && preg_match('/WO-\d{4}-(\d+)/', $latest->order_number, $matches)) {
                $seq = ((int) $matches[1]) + 1;
            }

            return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function generateFinishedLot(): string
    {
        $ym = date('Ym');
        $prefix = "LOT-FG-{$ym}-";

        return DB::transaction(function () use ($prefix) {
            $latest = self::where('target_finished_lot', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $seq = 1;
            if ($latest && preg_match('/LOT-FG-\d{6}-(\d+)/', $latest->target_finished_lot, $matches)) {
                $seq = ((int) $matches[1]) + 1;
            }

            return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        });
    }

    public function getTargetQuantityAttribute(): ?int
    {
        return $this->planned_qty;
    }

    public function setTargetQuantityAttribute(?int $value): void
    {
        $this->attributes['planned_qty'] = $value;
    }

    public function getProductBatchNumberAttribute(): ?string
    {
        return $this->target_finished_lot;
    }

    public function setProductBatchNumberAttribute(?string $value): void
    {
        $this->attributes['target_finished_lot'] = $value;
    }

    public function getScheduledDateAttribute(): mixed
    {
        return $this->scheduled_start;
    }

    public function setScheduledDateAttribute(mixed $value): void
    {
        $this->attributes['scheduled_start'] = $value;
    }

    public function getActualQuantityAttribute(): ?int
    {
        return $this->produced_qty;
    }

    public function setActualQuantityAttribute(?int $value): void
    {
        $this->attributes['produced_qty'] = $value;
    }

    public function getScrapQuantityAttribute(): ?int
    {
        return $this->rejected_qty;
    }

    public function setScrapQuantityAttribute(?int $value): void
    {
        $this->attributes['rejected_qty'] = $value;
    }

    public function getWoNumberAttribute(): ?string
    {
        return $this->order_number;
    }

    public function setWoNumberAttribute(?string $value): void
    {
        $this->attributes['order_number'] = $value;
    }

    protected static function booted(): void
    {
        static::creating(function (WorkOrder $wo) {
            if (empty($wo->order_number)) {
                $wo->order_number = self::generateOrderNumber();
            }
            if (empty($wo->target_finished_lot)) {
                $wo->target_finished_lot = self::generateFinishedLot();
            }
            if ($wo->location_id === null) {
                $wo->location_id = Location::first()?->id ?? 'central_wh';
            }
        });
    }
}
