<?php

namespace App\Models;

use App\Models\Mr\Contact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'country_id',
        'city_id',
        'name_en',
        'name_ar',
        'code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'city_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function medicalReps(): HasMany
    {
        return $this->hasMany(User::class)->where(function ($q) {
            $q->whereHas('role', function ($rq) {
                $rq->where('name', 'mr');
            })->orWhere('role_id', 2);
        });
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar);
    }

    public function getFullHierarchyLabelAttribute(): string
    {
        $countryName = $this->country?->name ?? '';
        $cityName = $this->city?->name ?? '';
        $areaName = $this->name;

        return trim("{$countryName} › {$cityName} › {$areaName}", ' ›');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
