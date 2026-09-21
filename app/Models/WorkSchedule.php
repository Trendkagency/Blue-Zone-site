<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'start_time',
        'end_time',
        'break_minutes',
        'grace_minutes',
        'working_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'break_minutes' => 'integer',
            'grace_minutes' => 'integer',
            'working_hours' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function days(): HasMany
    {
        return $this->hasMany(WorkScheduleDay::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && !empty($this->name_ar) ? $this->name_ar : $this->name_en;
    }
}
