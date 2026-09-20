<?php

namespace App\Models\Mr;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepPerformanceSnapshot extends Model
{
    use HasFactory;

    protected $table = 'mr_rep_performance_snapshots';

    protected $fillable = [
        'mr_id',
        'cycle_id',
        'total_assigned_contacts',
        'unique_contacts_visited',
        'coverage_rate_pct',
        'planned_visits',
        'visits_done',
        'visit_compliance_pct',
        'verified_visits',
        'gps_accuracy_pct',
        'target_points',
        'achieved_points',
        'points_achieved_pct',
        'unreported_days_count',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'total_assigned_contacts' => 'integer',
            'unique_contacts_visited' => 'integer',
            'coverage_rate_pct' => 'decimal:2',
            'planned_visits' => 'integer',
            'visits_done' => 'integer',
            'visit_compliance_pct' => 'decimal:2',
            'verified_visits' => 'integer',
            'gps_accuracy_pct' => 'decimal:2',
            'target_points' => 'integer',
            'achieved_points' => 'integer',
            'points_achieved_pct' => 'decimal:2',
            'unreported_days_count' => 'integer',
            'calculated_at' => 'datetime',
        ];
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mr_id');
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(VisitCycle::class, 'cycle_id');
    }
}
