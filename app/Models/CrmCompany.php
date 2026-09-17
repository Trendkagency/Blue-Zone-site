<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmCompany extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'legal_name',
        'email',
        'phone',
        'website',
        'country_id',
        'city_id',
        'address',
        'industry',
        'company_size',
        'tax_number',
        'registration_number',
        'owner_id',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(CrmLead::class, 'company_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'company_id');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(CrmOpportunity::class, 'company_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'company_id');
    }

    public function notesList(): HasMany
    {
        return $this->hasMany(CrmNote::class, 'company_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(CrmTag::class, 'taggable', 'crm_taggables', 'taggable_id', 'tag_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
