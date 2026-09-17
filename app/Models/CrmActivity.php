<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_type',
        'subject',
        'description',
        'lead_id',
        'customer_id',
        'company_id',
        'opportunity_id',
        'assigned_to',
        'created_by',
        'scheduled_at',
        'started_at',
        'completed_at',
        'due_at',
        'status',
        'priority',
        'outcome',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'due_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(CrmOpportunity::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'completed' || $this->status === 'cancelled') {
            return false;
        }
        return $this->due_at && $this->due_at->isPast();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'scheduled']);
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pending', 'scheduled', 'overdue'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }

    public function scopeDueToday($query)
    {
        return $query->whereIn('status', ['pending', 'scheduled'])
            ->whereDate('due_at', today());
    }

    public function scopeTasks($query)
    {
        return $query->where('activity_type', 'task');
    }
}
