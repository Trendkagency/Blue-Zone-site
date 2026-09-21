<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobVacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'position_id',
        'department_id',
        'location_id',
        'title_en',
        'title_ar',
        'description',
        'requirements',
        'openings',
        'employment_type',
        'salary_min',
        'salary_max',
        'status',
        'opened_at',
        'closing_at',
    ];

    protected function casts(): array
    {
        return [
            'position_id' => 'integer',
            'department_id' => 'integer',
            'openings' => 'integer',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'opened_at' => 'date',
            'closing_at' => 'date',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' && !empty($this->title_ar) ? $this->title_ar : $this->title_en;
    }
}
