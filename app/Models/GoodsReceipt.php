<?php

namespace App\Models;

use App\Enums\GoodsReceiptStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_number',
        'purchase_order_id',
        'supplier_id',
        'location_id',
        'received_date',
        'receiver_id',
        'received_by',
        'carrier_name',
        'tracking_number',
        'temperature_logger_reading',
        'temperature_reading',
        'temperature_within_range',
        'seal_verified',
        'seal_intact',
        'container_count',
        'discrepancy_notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => GoodsReceiptStatus::class,
            'received_date' => 'date',
            'temperature_logger_reading' => 'decimal:2',
            'temperature_within_range' => 'boolean',
            'seal_verified' => 'boolean',
            'container_count' => 'integer',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(RawMaterialBatch::class);
    }

    public static function generateGrnNumber(): string
    {
        $year = date('Y');
        $prefix = "GRN-{$year}-";

        return DB::transaction(function () use ($prefix) {
            $latest = self::where('grn_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $seq = 1;
            if ($latest && preg_match('/GRN-\d{4}-(\d+)/', $latest->grn_number, $matches)) {
                $seq = ((int) $matches[1]) + 1;
            }

            return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        });
    }

    protected static function booted(): void
    {
        static::creating(function (GoodsReceipt $grn) {
            if (empty($grn->grn_number)) {
                $grn->grn_number = self::generateGrnNumber();
            }
            if (empty($grn->location_id)) {
                $grn->location_id = 'central_wh';
            }
            if (empty($grn->status)) {
                $grn->status = GoodsReceiptStatus::Received;
            }
        });
    }

    public function getTemperatureReadingAttribute(): ?float
    {
        return $this->temperature_logger_reading !== null ? (float) $this->temperature_logger_reading : null;
    }

    public function setTemperatureReadingAttribute(float|int|null $value): void
    {
        $this->attributes['temperature_logger_reading'] = $value;
    }

    public function getSealIntactAttribute(): bool
    {
        return (bool) ($this->seal_verified ?? true);
    }

    public function setSealIntactAttribute(bool|int|null $value): void
    {
        $this->attributes['seal_verified'] = (bool) $value;
    }

    public function getReceivedByAttribute(): ?int
    {
        return $this->receiver_id;
    }

    public function setReceivedByAttribute(?int $value): void
    {
        $this->attributes['receiver_id'] = $value;
    }
}
