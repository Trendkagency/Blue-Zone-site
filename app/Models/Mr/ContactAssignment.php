<?php

namespace App\Models\Mr;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactAssignment extends Model
{
    use HasFactory;

    protected $table = 'mr_contact_assignments';

    protected $fillable = [
        'cycle_id',
        'mr_id',
        'contact_id',
        'target_visits',
        'visits_done',
        'target_points',
        'achieved_points',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_visits' => 'integer',
            'visits_done' => 'integer',
            'target_points' => 'integer',
            'achieved_points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(VisitCycle::class, 'cycle_id');
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mr_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function scheduledVisits(): HasMany
    {
        return $this->hasMany(ScheduledVisit::class, 'assignment_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'assignment_id');
    }

    /**
     * Calculate compliance percentage for this specific doctor assignment
     */
    public function getCompliancePercentageAttribute(): float
    {
        if ($this->target_visits <= 0) {
            return 0.0;
        }

        return round(($this->visits_done / $this->target_visits) * 100, 2);
    }

    /**
     * Check if this contact assignment is behind target (at risk)
     */
    public function isAtRisk(): bool
    {
        if ($this->visits_done >= $this->target_visits) {
            return false;
        }

        $cycle = $this->cycle;
        if (!$cycle || $cycle->status !== 'active') {
            return false;
        }

        $daysRemaining = $cycle->getDaysRemaining();
        $visitsRemaining = $this->target_visits - $this->visits_done;

        // If days remaining are tight relative to visits needed
        return $daysRemaining <= ($visitsRemaining * 3);
    }
}
