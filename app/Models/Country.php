<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'iso2',
        'phone_code',
        'currency_code',
        'currency_symbol_en',
        'currency_symbol_ar',
        'flag_emoji',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class)->orderBy('sort_order')->orderBy('name_en');
    }

    public function activeCities(): HasMany
    {
        return $this->hasMany(City::class)->where('is_active', true)->orderBy('sort_order')->orderBy('name_en');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar);
    }

    public function getCurrencySymbolAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' 
            ? ($this->currency_symbol_ar ?: $this->currency_symbol_en ?: $this->currency_code ?: '') 
            : ($this->currency_symbol_en ?: $this->currency_symbol_ar ?: $this->currency_code ?: '');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
