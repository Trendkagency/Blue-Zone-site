<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'order_date',
        'expected_date',
        'destination_location_id',
        'status',
        'subtotal',
        'freight_cost',
        'customs_duties',
        'tax_amount',
        'grand_total',
        'total_amount',
        'currency',
        'exchange_rate',
        'created_by',
        'approved_by',
        'approved_at',
        'supplier_reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PurchaseOrderStatus::class,
            'order_date' => 'date',
            'expected_date' => 'date',
            'subtotal' => 'decimal:2',
            'freight_cost' => 'decimal:2',
            'customs_duties' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'approved_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(RawMaterialBatch::class);
    }

    /**
     * Generate next sequential atomic PO number in PO-YYYY-XXXX format
     */
    public static function generatePoNumber(): string
    {
        $year = date('Y');
        $prefix = "PO-{$year}-";

        return DB::transaction(function () use ($prefix) {
            $latest = self::where('po_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $seq = 1;
            if ($latest && preg_match('/PO-\d{4}-(\d+)/', $latest->po_number, $matches)) {
                $seq = ((int) $matches[1]) + 1;
            }

            return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Separation of duties rule: Creator cannot approve above threshold (AED 25,000)
     */
    /**
     * Separation of duties rule: Creator cannot approve above threshold (AED 25,000)
     */
    public function canBeApprovedBy(User $user, float $threshold = 25000.00): bool
    {
        if ($this->grand_total >= $threshold && $this->created_by === $user->id) {
            return false;
        }

        return $user->hasPermission('purchase_orders.approve') || $user->hasPermission('*');
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) ($this->grand_total ?? 0);
    }

    public function setTotalAmountAttribute(float|int|null $value): void
    {
        $this->attributes['grand_total'] = $value;
    }

    public function getExpectedDeliveryDateAttribute(): mixed
    {
        return $this->expected_date;
    }

    public function setExpectedDeliveryDateAttribute($value): void
    {
        $this->attributes['expected_date'] = $value;
    }

    public function transitionTo(PurchaseOrderStatus $target, array $attributes = []): void
    {
        $this->status->validateTransitionTo($target);
        $this->status = $target;

        foreach ($attributes as $key => $val) {
            $this->{$key} = $val;
        }

        if ($target === PurchaseOrderStatus::Confirmed && empty($this->approved_at)) {
            $this->approved_at = now();
        }

        $this->save();
    }
}
