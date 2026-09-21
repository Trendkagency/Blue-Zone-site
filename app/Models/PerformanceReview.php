<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'performance_cycle_id',
        'reviewer_employee_id',
        'reviewer_user_id',
        'overall_score',
        'strengths',
        'areas_for_improvement',
        'overall_notes',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'performance_cycle_id' => 'integer',
            'reviewer_employee_id' => 'integer',
            'reviewer_user_id' => 'integer',
            'overall_score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(PerformanceCycle::class, 'performance_cycle_id');
    }

    public function reviewerEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_employee_id');
    }

    public function reviewerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PerformanceReviewItem::class);
    }
}
