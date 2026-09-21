<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExitInterview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'offboarding_id',
        'employee_id',
        'interviewer_user_id',
        'reason_for_leaving',
        'working_environment_rating',
        'management_rating',
        'compensation_rating',
        'suggestions',
        'confidential_notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'offboarding_id' => 'integer',
            'employee_id' => 'integer',
            'interviewer_user_id' => 'integer',
            'working_environment_rating' => 'integer',
            'management_rating' => 'integer',
            'compensation_rating' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function offboarding(): BelongsTo
    {
        return $this->belongsTo(EmployeeOffboarding::class, 'offboarding_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_user_id');
    }
}
