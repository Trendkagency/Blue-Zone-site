<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdjustmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'code',
        'direction',
        'description',
        'is_active',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get localized display name.
     */
    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }

        return $this->name_en;
    }

    /**
     * Associated movements that used this adjustment type code.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'movement_type', 'code');
    }

    /**
     * Direction badge styles.
     */
    public function getDirectionBadgeAttribute(): array
    {
        return match ($this->direction) {
            'increase' => [
                'bg' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                'icon' => 'fa-arrow-trend-up',
                'label_en' => 'Stock Inward (+)',
                'label_ar' => 'إضافة للمخزون (+)',
            ],
            'decrease' => [
                'bg' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                'icon' => 'fa-arrow-trend-down',
                'label_en' => 'Stock Outward (-)',
                'label_ar' => 'خصم من المخزون (-)',
            ],
            default => [
                'bg' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                'icon' => 'fa-scale-balanced',
                'label_en' => 'Count Reconciliation (±)',
                'label_ar' => 'فارق جرد / تسوية (±)',
            ],
        };
    }
}
