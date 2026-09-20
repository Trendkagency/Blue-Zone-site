<?php

namespace App\Models\Mr;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepDailyLog extends Model
{
    use HasFactory;

    protected $table = 'mr_rep_daily_logs';

    protected $fillable = [
        'mr_id',
        'cycle_id',
        'log_date',
        'is_reported',
        'first_checkin_at',
        'last_checkout_at',
        'total_visits_count',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'is_reported' => 'boolean',
            'first_checkin_at' => 'datetime',
            'last_checkout_at' => 'datetime',
            'total_visits_count' => 'integer',
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
