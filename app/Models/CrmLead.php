<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_number',
        'customer_id',
        'company_id',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'phone_normalized',
        'secondary_phone',
        'country_id',
        'city_id',
        'job_title',
        'company_name',
        'source_id',
        'campaign_id',
        'status',
        'stage',
        'priority',
        'estimated_value',
        'currency',
        'owner_id',
        'assigned_at',
        'last_contacted_at',
        'next_follow_up_at',
        'converted_at',
        'converted_customer_id',
        'converted_opportunity_id',
        'lost_reason',
        'notes',
        'score',
        'metadata',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'assigned_at' => 'datetime',
        'last_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'converted_at' => 'datetime',
        'score' => 'integer',
        'metadata' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'company_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(CrmLeadSource::class, 'source_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(CrmCampaign::class, 'campaign_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'lead_id')->orderBy('due_at', 'desc');
    }

    public function notesList(): HasMany
    {
        return $this->hasMany(CrmNote::class, 'lead_id')->latest();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(CrmTag::class, 'taggable', 'crm_taggables', 'taggable_id', 'tag_id');
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }

    public function convertedOpportunity(): BelongsTo
    {
        return $this->belongsTo(CrmOpportunity::class, 'converted_opportunity_id');
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeOverdueFollowUp($query)
    {
        return $query->where('status', '!=', 'converted')
            ->where('status', '!=', 'lost')
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now());
    }
}
