<?php

namespace App\Models;

use App\Enums\RawMaterialCategory;
use App\Enums\StorageCondition;
use App\Enums\UnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $code
 * @property string|null $sku
 * @property string $name_en
 * @property string|null $name_ar
 * @property string|null $inci_name
 * @property string|null $cas_number
 * @property float|string|null $standard_cost
 * @property int|null $shelf_life_days
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class RawMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['material_code'];

    protected $fillable = [
        'code',
        'sku',
        'name_en',
        'name_ar',
        'inci_name',
        'cas_number',
        'category',
        'unit_of_measure',
        'uom',
        'storage_condition',
        'standard_cost',
        'min_reorder_qty',
        'reorder_level',
        'safety_stock',
        'shelf_life_days',
        'requires_coa',
        'default_supplier_id',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => RawMaterialCategory::class,
            'unit_of_measure' => UnitOfMeasure::class,
            'storage_condition' => StorageCondition::class,
            'standard_cost' => 'decimal:4',
            'min_reorder_qty' => 'decimal:4',
            'safety_stock' => 'decimal:4',
            'shelf_life_days' => 'integer',
            'requires_coa' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function defaultSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'default_supplier_id');
    }

    public function supplierPrices(): HasMany
    {
        return $this->hasMany(SupplierMaterialPrice::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(RawMaterialBatch::class);
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(ProductRecipeItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_recipe_items')
            ->withPivot(['quantity_per_unit', 'loss_percentage', 'is_critical'])
            ->withTimestamps();
    }

    /**
     * Get aggregate available approved stock across all warehouses
     */
    public function getTotalAvailableStockAttribute(): float
    {
        return (float) $this->batches()
            ->where('status', 'approved')
            ->sum('available_qty');
    }

    public function getTotalQuarantineStockAttribute(): float
    {
        return (float) $this->batches()
            ->where('status', 'quarantine')
            ->sum('quarantine_qty');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->total_available_stock <= (float) $this->min_reorder_qty;
    }

    public function getNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }

        return $this->name_en ?? '';
    }

    public function getShelfLifeMonthsAttribute(): int
    {
        return (int) round(($this->shelf_life_days ?? 730) / 30);
    }

    public function setShelfLifeMonthsAttribute(int|float|null $value): void
    {
        if ($value !== null) {
            $this->attributes['shelf_life_days'] = max(1, (int) $value) * 30;
        }
    }

    public static function generateNextCode(): string
    {
        $year = date('Y');
        $prefix = "RM-{$year}-";
        $latest = static::withTrashed()
            ->where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('code');

        if ($latest && preg_match('/(\d+)$/', $latest, $m)) {
            $seq = (int) $m[1] + 1;
        } else {
            $count = static::withTrashed()->count();
            $seq = $count + 1;
        }

        return sprintf('%s%04d', $prefix, $seq);
    }

    public function getSkuAttribute(): ?string
    {
        return $this->code;
    }

    public function setSkuAttribute(?string $value): void
    {
        $this->attributes['code'] = $value;
    }

    public function getMaterialCodeAttribute(): ?string
    {
        return $this->code;
    }

    public function setMaterialCodeAttribute(?string $value): void
    {
        $this->attributes['code'] = $value;
    }

    public function getUomAttribute(): ?UnitOfMeasure
    {
        return $this->unit_of_measure;
    }

    public function setUomAttribute(UnitOfMeasure|string|null $value): void
    {
        $this->attributes['unit_of_measure'] = $value instanceof UnitOfMeasure ? $value->value : $value;
    }

    public function getReorderLevelAttribute(): float
    {
        return (float) ($this->min_reorder_qty ?? 0);
    }

    public function setReorderLevelAttribute(float|int|null $value): void
    {
        $this->attributes['min_reorder_qty'] = $value;
    }
}
