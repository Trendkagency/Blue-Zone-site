<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinalSettlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'offboarding_id',
        'employee_id',
        'basic_salary_due',
        'leave_encashment_amount',
        'overtime_amount',
        'end_of_service_gratuity',
        'advance_deductions',
        'loan_deductions',
        'asset_damage_deductions',
        'net_settlement_amount',
        'status',
        'settlement_date',
        'notes',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'offboarding_id' => 'integer',
            'employee_id' => 'integer',
            'approved_by' => 'integer',
            'basic_salary_due' => 'decimal:2',
            'leave_encashment_amount' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'end_of_service_gratuity' => 'decimal:2',
            'advance_deductions' => 'decimal:2',
            'loan_deductions' => 'decimal:2',
            'asset_damage_deductions' => 'decimal:2',
            'net_settlement_amount' => 'decimal:2',
            'settlement_date' => 'date',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
