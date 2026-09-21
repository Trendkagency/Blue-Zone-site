<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidate_number',
        'job_vacancy_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'whatsapp',
        'cv_path',
        'source',
        'applied_position',
        'expected_salary',
        'experience_years',
        'education',
        'status',
        'rejection_reason',
        'notes',
        'converted_employee_id',
    ];

    protected function casts(): array
    {
        return [
            'job_vacancy_id' => 'integer',
            'expected_salary' => 'decimal:2',
            'experience_years' => 'integer',
            'converted_employee_id' => 'integer',
        ];
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    public function convertedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'converted_employee_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(CandidateInterview::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(JobOffer::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
