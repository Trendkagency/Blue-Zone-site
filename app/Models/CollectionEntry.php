<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionEntry extends Model
{
    use HasFactory;

    protected $table = 'collection_entries';

    protected $fillable = [
        'collection_number',
        'entry_date',
        'category',
        'description',
        'source_type',
        'customer_id',
        'supplier_id',
        'customer_code',
        'customer_source',
        'amount_excl_vat',
        'vat_rate',
        'vat_amount',
        'gross_amount',
        'financial_account_id',
        'payment_method',
        'cheque_number',
        'cheque_bank',
        'cheque_due_date',
        'order_id',
        'is_auto_synced',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'cheque_due_date' => 'date',
        'amount_excl_vat' => 'float',
        'vat_rate' => 'float',
        'vat_amount' => 'float',
        'gross_amount' => 'float',
        'is_auto_synced' => 'boolean',
    ];

    /**
     * Categories allowed in the system.
     */
    public const CATEGORY_NON_OPERATIONAL = 'non_operational_revenue';
    public const CATEGORY_SALES = 'sales';
    public const CATEGORY_ONLINE_SALES_AUTO = 'online_sales_auto';
    public const CATEGORY_EQUITIES = 'equities';
    public const CATEGORY_SUPPLIER_REFUND = 'supplier_refund';
    public const CATEGORY_ASSETS = 'assets_disposal';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_ONLINE_SALES_AUTO => [
                'name_en' => 'online sales (auto)',
                'name_ar' => 'مبيعات أونلاين (آلي)',
                'badge_color' => '#0284c7',
                'bg_color' => 'rgba(2, 132, 199, 0.1)',
                'default_vat' => 15.00,
            ],
            self::CATEGORY_SALES => [
                'name_en' => 'sales',
                'name_ar' => 'مبيعات تجارية',
                'badge_color' => '#10b981',
                'bg_color' => 'rgba(16, 185, 129, 0.1)',
                'default_vat' => 15.00,
            ],
            self::CATEGORY_SUPPLIER_REFUND => [
                'name_en' => 'supplier refund / credit',
                'name_ar' => 'استرداد / تسوية مورد',
                'badge_color' => '#d97706',
                'bg_color' => 'rgba(217, 119, 6, 0.1)',
                'default_vat' => 15.00,
            ],
            self::CATEGORY_ASSETS => [
                'name_en' => 'asset sale / disposal',
                'name_ar' => 'تحصيل بيع أصول',
                'badge_color' => '#059669',
                'bg_color' => 'rgba(5, 150, 105, 0.1)',
                'default_vat' => 15.00,
            ],
            self::CATEGORY_NON_OPERATIONAL => [
                'name_en' => 'non-operational revenue',
                'name_ar' => 'إيرادات غير تشغيلية',
                'badge_color' => '#f59e0b',
                'bg_color' => 'rgba(245, 158, 11, 0.1)',
                'default_vat' => 0.00,
            ],
            self::CATEGORY_EQUITIES => [
                'name_en' => 'equities',
                'name_ar' => 'حقوق ملكية ورأس مال',
                'badge_color' => '#8b5cf6',
                'bg_color' => 'rgba(139, 92, 246, 0.1)',
                'default_vat' => 0.00,
            ],
        ];
    }

    /**
     * Generate sequential collection entry number (e.g. COL-2026-0001).
     */
    public static function generateCollectionNumber(): string
    {
        $year = date('Y');
        $latest = static::whereYear('entry_date', $year)
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($latest && preg_match('/COL-\d{4}-(\d+)/', $latest->collection_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $count = static::count();
            $nextNumber = $count + 1;
        }

        return sprintf('COL-%s-%04d', $year, $nextNumber);
    }

    /**
     * Relationship with Financial Account (Bank or Cash).
     */
    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    /**
     * Relationship with Customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relationship with Supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Relationship with Sales Order (if auto-synced or linked).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
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
            'bg_color' => 'rgba(100, 116, 139, 0.1)',
            'default_vat' => 15.00,
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
