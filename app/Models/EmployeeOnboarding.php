<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOnboarding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'onboarding_template_id',
        'start_date',
        'target_completion_date',
        'completed_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'onboarding_template_id' => 'integer',
            'start_date' => 'date',
            'target_completion_date' => 'date',
            'completed_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'onboarding_template_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EmployeeOnboardingTask::class);
    }
}
