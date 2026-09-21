<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'provider',
        'trainer',
        'start_date',
        'end_date',
        'cost',
        'duration_hours',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'cost' => 'decimal:2',
            'duration_hours' => 'integer',
        ];
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EmployeeTraining::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(TrainingCertificate::class);
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && !empty($this->name_ar) ? $this->name_ar : $this->name_en;
    }
}
