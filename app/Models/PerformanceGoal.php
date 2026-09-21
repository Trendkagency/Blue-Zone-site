<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceGoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'performance_cycle_id',
        'title',
        'description',
        'target',
        'actual',
        'achievement_percentage',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'performance_cycle_id' => 'integer',
            'achievement_percentage' => 'decimal:2',
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
}
