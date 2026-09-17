<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmOpportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opportunity_number',
        'lead_id',
        'customer_id',
        'company_id',
        'name',
        'description',
        'pipeline_id',
        'stage_id',
        'owner_id',
        'source_id',
        'campaign_id',
        'value',
        'currency',
        'probability',
        'expected_close_date',
        'status',
        'won_at',
        'lost_at',
        'lost_reason',
        'last_activity_at',
        'next_follow_up_at',
        'metadata',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'integer',
        'expected_close_date' => 'date',
        'won_at' => 'datetime',
        'lost_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(CrmPipeline::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(CrmPipelineStage::class, 'stage_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(CrmLeadSource::class, 'source_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(CrmCampaign::class, 'campaign_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'opportunity_id')->orderBy('due_at', 'desc');
    }

    public function notesList(): HasMany
    {
        return $this->hasMany(CrmNote::class, 'opportunity_id')->latest();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(CrmTag::class, 'taggable', 'crm_taggables', 'taggable_id', 'tag_id');
    }

    public function getWeightedValueAttribute(): float
    {
        return round(($this->value * $this->probability) / 100, 2);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeWon($query)
    {
        return $query->where('status', 'won');
    }

    public function scopeLost($query)
    {
        return $query->where('status', 'lost');
    }
}
