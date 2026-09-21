<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateInterview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidate_id',
        'interviewer_employee_id',
        'interviewer_user_id',
        'interview_type',
        'scheduled_at',
        'location',
        'meeting_url',
        'score',
        'evaluation_notes',
        'result',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'candidate_id' => 'integer',
            'interviewer_employee_id' => 'integer',
            'interviewer_user_id' => 'integer',
            'scheduled_at' => 'datetime',
            'score' => 'decimal:1',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewerEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'interviewer_employee_id');
    }

    public function interviewerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_user_id');
    }
}
