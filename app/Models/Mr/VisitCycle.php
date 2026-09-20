<?php

namespace App\Models\Mr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitCycle extends Model
{
    use HasFactory;

    protected $table = 'mr_visit_cycles';

    protected $fillable = [
        'name',
        'code',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ContactAssignment::class, 'cycle_id');
    }

    public function scheduledVisits(): HasMany
    {
        return $this->hasMany(ScheduledVisit::class, 'cycle_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'cycle_id');
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(RepDailyLog::class, 'cycle_id');
    }

    public function performanceSnapshots(): HasMany
    {
        return $this->hasMany(RepPerformanceSnapshot::class, 'cycle_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get remaining days in the cycle
     */
    public function getDaysRemaining(): int
    {
        $now = now()->startOfDay();
        $end = $this->end_date->startOfDay();

        return max(0, $now->diffInDays($end, false));
    }
}
