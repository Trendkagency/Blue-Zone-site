<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentEntry extends Model
{
    use HasFactory;

    protected $table = 'payment_entries';

    protected $fillable = [
        'payment_number',
        'entry_date',
        'category',
        'description',
        'payee_type',
        'supplier_id',
        'purchase_invoice_id',
        'supplier_code',
        'payee_name',
        'amount_excl_vat',
        'vat_rate',
        'vat_amount',
        'gross_amount',
        'financial_account_id',
        'payment_method',
        'cheque_number',
        'cheque_bank',
        'cheque_due_date',
        'status',
        'cleared_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'cheque_due_date' => 'date',
        'cleared_at' => 'datetime',
        'amount_excl_vat' => 'float',
        'vat_rate' => 'float',
        'vat_amount' => 'float',
        'gross_amount' => 'float',
    ];

    /**
     * Categories allowed in the system.
     */
    public const CATEGORY_PURCHASE = 'purchase';
    public const CATEGORY_TRANSPORTATION = 'transportation_logistics';
    public const CATEGORY_OPERATING_EXPENSES = 'operating_expenses';
    public const CATEGORY_SUPPLIER_ADVANCE = 'supplier_advance';
    public const CATEGORY_ASSETS = 'assets_equipment';
    public const CATEGORY_PAYROLL = 'payroll_staff';
    public const CATEGORY_GOVERNMENT = 'government_customs';
    public const CATEGORY_OTHER = 'other';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_PURCHASE => [
                'name_en' => 'Purchase & Raw Materials',
                'name_ar' => 'مشتريات بضائع ومواد خام',
                'badge_color' => '#d97706',
                'bg_color' => 'rgba(217, 119, 6, 0.12)',
                'default_vat' => 15.00,
                'icon' => 'fa-truck-ramp-box',
            ],
            self::CATEGORY_TRANSPORTATION => [
                'name_en' => 'Transportation & Freight',
                'name_ar' => 'نقل وشحن وتخليص',
                'badge_color' => '#0284c7',
                'bg_color' => 'rgba(2, 132, 199, 0.12)',
                'default_vat' => 15.00,
                'icon' => 'fa-truck-fast',
            ],
            self::CATEGORY_OPERATING_EXPENSES => [
                'name_en' => 'Operating Expenses & Utilities',
                'name_ar' => 'مصاريف تشغيلية ومرافق',
                'badge_color' => '#8b5cf6',
                'bg_color' => 'rgba(139, 92, 246, 0.12)',
                'default_vat' => 15.00,
                'icon' => 'fa-bolt',
            ],
            self::CATEGORY_SUPPLIER_ADVANCE => [
                'name_en' => 'Supplier Advance Payment',
                'name_ar' => 'دفعة مقدمة لمورد',
                'badge_color' => '#ea580c',
                'bg_color' => 'rgba(234, 88, 12, 0.12)',
                'default_vat' => 15.00,
                'icon' => 'fa-money-bill-transfer',
            ],
            self::CATEGORY_ASSETS => [
                'name_en' => 'Assets & Capital Equipment',
                'name_ar' => 'شراء أصول ومعدات',
                'badge_color' => '#059669',
                'bg_color' => 'rgba(5, 150, 105, 0.12)',
                'default_vat' => 15.00,
                'icon' => 'fa-landmark',
            ],
            self::CATEGORY_PAYROLL => [
                'name_en' => 'Payroll & Staff Expenses',
                'name_ar' => 'رواتب ومستحقات موظفين',
                'badge_color' => '#0d9488',
                'bg_color' => 'rgba(13, 148, 136, 0.12)',
                'default_vat' => 0.00,
                'icon' => 'fa-users',
            ],
            self::CATEGORY_GOVERNMENT => [
                'name_en' => 'Government & Customs Fees',
                'name_ar' => 'رسوم حكومية وجمركية',
                'badge_color' => '#475569',
                'bg_color' => 'rgba(71, 85, 105, 0.12)',
                'default_vat' => 0.00,
                'icon' => 'fa-scale-balanced',
            ],
            self::CATEGORY_OTHER => [
                'name_en' => 'Other Disbursements',
                'name_ar' => 'مدفوعات ومصروفات أخرى',
                'badge_color' => '#64748b',
                'bg_color' => 'rgba(100, 116, 139, 0.12)',
                'default_vat' => 15.00,
                'icon' => 'fa-receipt',
            ],
        ];
    }

    /**
     * Generate sequential payment entry number (e.g. PAY-2026-0001).
     */
    public static function generatePaymentNumber(): string
    {
        $year = date('Y');
        $latest = static::whereYear('entry_date', $year)
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($latest && preg_match('/PAY-\d{4}-(\d+)/', $latest->payment_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $count = static::count();
            $nextNumber = $count + 1;
        }

        return sprintf('PAY-%s-%04d', $year, $nextNumber);
    }

    /**
     * Relationship with Financial Account (Bank or Cash).
     */
    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    /**
     * Relationship with Supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Relationship with Purchase Invoice (if linked).
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    /**
     * Creator of the entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Category Badge Meta.
     */
    public function getCategoryMetaAttribute(): array
    {
        $cats = static::getCategories();
        return $cats[$this->category] ?? [
            'name_en' => $this->category,
            'name_ar' => $this->category,
            'badge_color' => '#64748b',
            'bg_color' => 'rgba(100, 116, 139, 0.12)',
            'default_vat' => 15.00,
            'icon' => 'fa-receipt',
        ];
    }

    /**
     * Scope filter by Category.
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (!empty($category)) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope filter by Financial Account.
     */
    public function scopeAccount(Builder $query, $accountId): Builder
    {
        if (!empty($accountId)) {
            return $query->where('financial_account_id', $accountId);
        }
        return $query;
    }

    /**
     * Scope filter by Status.
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (!empty($status)) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope filter by Date Range.
     */
    public function scopeDateBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('entry_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('entry_date', '<=', $to);
        }
        return $query;
    }
}
