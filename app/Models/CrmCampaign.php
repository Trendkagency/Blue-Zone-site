<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'start_at',
        'end_at',
        'budget',
        'currency',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'owner_id',
        'target_leads',
        'converted_leads',
        'revenue_generated',
        'metadata',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'budget' => 'decimal:2',
        'revenue_generated' => 'decimal:2',
        'target_leads' => 'integer',
        'converted_leads' => 'integer',
        'metadata' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(CrmLead::class, 'campaign_id');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(CrmOpportunity::class, 'campaign_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getRoiPercentageAttribute(): float
    {
        if ($this->budget > 0) {
            return round((($this->revenue_generated - $this->budget) / $this->budget) * 100, 2);
        }
        return 0.0;
    }
}
