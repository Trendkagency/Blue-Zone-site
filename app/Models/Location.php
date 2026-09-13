<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name_en',
        'name_ar',
        'code',
        'type',
        'country_id',
        'city_id',
        'address',
        'city',
        'manager_name',
        'phone',
        'email',
        'notes',
        'capacity_units',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
            'city_id' => 'integer',
            'is_active' => 'boolean',
            'capacity_units' => 'integer',
        ];
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cityModel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'location_id', 'id');
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && !empty($this->name_ar) ? $this->name_ar : $this->name_en;
    }

    public function getCityNameAttribute(): string
    {
        if ($this->cityModel) {
            return $this->cityModel->name;
        }
        return $this->city ?: '';
    }

    public function getCountryNameAttribute(): string
    {
        if ($this->country) {
            return $this->country->name;
        }
        return '';
    }

    public function getDisplayLocationAttribute(): string
    {
        $parts = array_filter([$this->city_name, $this->country_name]);
        return !empty($parts) ? implode(', ', $parts) : ($this->address ?: '—');
    }

    public function getPhoneWithCodeAttribute(): string
    {
        if (empty($this->phone)) {
            return '';
        }
        if (str_starts_with($this->phone, '+') || str_starts_with($this->phone, '00')) {
            return $this->phone;
        }
        $code = $this->country?->phone_code ?? '+966';
        return $code . ' ' . ltrim($this->phone, '0');
    }

    /**
     * Check if this is one of the immutable system core hubs.
     */
    public function getIsSystemCoreAttribute(): bool
    {
        return in_array($this->id, ['online', 'offline', 'central_wh'], true);
    }

    /**
     * Get aggregate total stock units currently in this warehouse.
     */
    public function getTotalStockUnitsAttribute(): int
    {
        return (int) $this->inventoryItems()->sum('current_stock');
    }

    /**
     * Get aggregate available units in this warehouse.
     */
    public function getTotalAvailableUnitsAttribute(): int
    {
        return (int) $this->inventoryItems()->sum('available_stock');
    }

    /**
     * Get aggregate reserved units in this warehouse.
     */
    public function getTotalReservedUnitsAttribute(): int
    {
        return (int) $this->inventoryItems()->sum('reserved_stock');
    }

    /**
     * Count of distinct active product formulations stocked in this facility.
     */
    public function getActiveSkusCountAttribute(): int
    {
        return (int) $this->inventoryItems()->where('current_stock', '>', 0)->count();
    }

    /**
     * Calculate financial stock valuation (current_stock * unit_cost) in this warehouse.
     */
    public function getTotalValuationAttribute(): float
    {
        return (float) $this->inventoryItems()
            ->selectRaw('SUM(current_stock * unit_cost) as total_val')
            ->value('total_val') ?? 0.0;
    }

    /**
     * Localized type badge label.
     */
    public function getTypeLabelAttribute(): string
    {
        $types = [
            'online' => ['en' => 'E-Commerce Hub', 'ar' => 'متجر إلكتروني'],
            'offline' => ['en' => 'Warehouse / POS', 'ar' => 'مستودع / نقطة بيع'],
            'warehouse' => ['en' => 'Logistics Warehouse', 'ar' => 'مستودع لوجستي'],
            'branch' => ['en' => 'Regional Branch', 'ar' => 'فرع إقليمي'],
        ];

        $lang = app()->getLocale();
        return $types[$this->type][$lang] ?? ucfirst($this->type);
    }

    /**
     * Badge visual color classes for light & dark mode.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'online' => 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20',
            'offline' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20',
            'warehouse' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20',
            'branch' => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-500/20',
            default => 'bg-slate-100 dark:bg-slate-500/10 text-slate-700 dark:text-slate-400 border border-slate-200 dark:border-slate-500/20',
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
