<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeOnboardingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_onboarding_id',
        'title',
        'description',
        'assigned_to_role',
        'due_date',
        'completed_at',
        'completed_by',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'employee_onboarding_id' => 'integer',
            'completed_by' => 'integer',
            'due_date' => 'date',
            'completed_at' => 'date',
        ];
    }

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'employee_onboarding_id');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
