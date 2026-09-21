<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'salary_component_id',
        'amount',
        'effective_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'salary_component_id' => 'integer',
            'amount' => 'decimal:2',
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(SalaryComponent::class, 'salary_component_id');
    }
}
