<?php

namespace App\Models\Mr;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScheduledVisit extends Model
{
    use HasFactory;

    protected $table = 'mr_scheduled_visits';

    protected $fillable = [
        'assignment_id',
        'mr_id',
        'contact_id',
        'cycle_id',
        'scheduled_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ContactAssignment::class, 'assignment_id');
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mr_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(VisitCycle::class, 'cycle_id');
    }

    public function visit(): HasOne
    {
        return $this->hasOne(Visit::class, 'scheduled_visit_id');
    }
}
