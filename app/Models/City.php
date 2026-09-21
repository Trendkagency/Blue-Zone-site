<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'country_id',
        'name_en',
        'name_ar',
        'state_or_province',
        'shipping_cost',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'shipping_cost' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class)->orderBy('sort_order')->orderBy('name_en');
    }

    public function activeAreas(): HasMany
    {
        return $this->hasMany(Area::class)->where('is_active', true)->orderBy('sort_order')->orderBy('name_en');
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
