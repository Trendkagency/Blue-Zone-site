<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'salary_structure_id',
        'name_en',
        'name_ar',
        'code',
        'type',
        'calculation_type',
        'default_value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'salary_structure_id' => 'integer',
            'default_value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(SalaryStructure::class, 'salary_structure_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EmployeeSalaryAssignment::class);
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && !empty($this->name_ar) ? $this->name_ar : $this->name_en;
    }
}
