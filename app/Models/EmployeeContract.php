<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'basic_salary',
        'housing_allowance',
        'transportation_allowance',
        'other_allowance',
        'probation_days',
        'notice_period_days',
        'working_hours_per_week',
        'status',
        'document_path',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'created_by' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'basic_salary' => 'decimal:2',
            'housing_allowance' => 'decimal:2',
            'transportation_allowance' => 'decimal:2',
            'other_allowance' => 'decimal:2',
            'probation_days' => 'integer',
            'notice_period_days' => 'integer',
            'working_hours_per_week' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
