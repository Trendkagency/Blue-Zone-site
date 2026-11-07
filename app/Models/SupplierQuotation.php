<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SupplierQuotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quote_number',
        'supplier_id',
        'rfq_reference',
        'quote_date',
        'valid_until',
        'status',
        'currency',
        'exchange_rate',
        'subtotal',
        'tax_amount',
        'estimated_freight',
        'estimated_customs',
        'grand_total',
        'payment_terms',
        'lead_time_days',
        'notes',
        'converted_po_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quote_date' => 'date',
            'valid_until' => 'date',
            'exchange_rate' => 'decimal:4',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'estimated_freight' => 'decimal:2',
            'estimated_customs' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'lead_time_days' => 'integer',
        ];
    }

    public static function generateQuoteNumber(): string
    {
        $year = date('Y');
        $prefix = "RFQ-{$year}-";
        $latest = static::withTrashed()
            ->where('quote_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('quote_number');

        if ($latest && preg_match('/(\d+)$/', $latest, $m)) {
            $seq = (int) $m[1] + 1;
        } else {
            $count = static::withTrashed()->count();
            $seq = $count + 1;
        }

        return sprintf('%s%04d', $prefix, $seq);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierQuotationItem::class);
    }

    public function convertedPurchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'converted_po_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 1-Click Convert this approved supplier quotation into an official Purchase Order.
     */
    public function convertToPurchaseOrder(User|int|null $user = null): PurchaseOrder
    {
        return DB::transaction(function () use ($user) {
            $userId = $user instanceof User ? $user->id : ($user ?: auth()->id());

            $poNumber = PurchaseOrder::generatePoNumber();
            $defaultLocation = Location::where('is_active', true)->first();

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $this->supplier_id,
                'supplier_quotation_id' => $this->id,
                'order_date' => now()->toDateString(),
                'expected_date' => now()->addDays($this->lead_time_days ?: 14)->toDateString(),
                'destination_location_id' => $defaultLocation?->id ?? 1,
                'status' => \App\Enums\PurchaseOrderStatus::Draft,
                'subtotal' => $this->subtotal ?: 0.00,
                'freight_cost' => $this->estimated_freight ?: 0.00,
                'customs_duties' => $this->estimated_customs ?: 0.00,
                'tax_amount' => $this->tax_amount ?: 0.00,
                'grand_total' => $this->grand_total ?: 0.00,
                'total_amount' => $this->grand_total ?: 0.00,
                'currency' => $this->currency,
                'exchange_rate' => $this->exchange_rate ?: 1.0000,
                'supplier_reference' => $this->quote_number . ($this->rfq_reference ? " ({$this->rfq_reference})" : ''),
                'notes' => "Converted from Supplier Quotation {$this->quote_number} on " . now()->toFormattedDateString() . ". " . ($this->notes ?? ''),
                'created_by' => $userId,
            ]);

            foreach ($this->items as $qItem) {
                $po->items()->create([
                    'item_type' => $qItem->item_type,
                    'raw_material_id' => $qItem->raw_material_id,
                    'product_id' => $qItem->product_id,
                    'ordered_qty' => $qItem->quantity,
                    'received_qty' => 0.0000,
                    'unit_price' => $qItem->unit_price,
                    'total_price' => round($qItem->quantity * $qItem->unit_price, 2),
                    'notes' => $qItem->notes,
                ]);
            }

            $this->update([
                'status' => 'converted',
                'converted_po_id' => $po->id,
            ]);

            return $po;
        });
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'accepted' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border-emerald-300',
            'converted' => 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 border-purple-300',
            'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border-amber-300',
            'rejected' => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border-rose-300',
            default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-300',
        };
    }
}
