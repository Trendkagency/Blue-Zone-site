<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'basic_salary',
        'total_allowances',
        'overtime_amount',
        'bonus_amount',
        'gross_salary',
        'total_deductions',
        'advance_deduction',
        'loan_deduction',
        'net_salary',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payroll_period_id' => 'integer',
            'employee_id' => 'integer',
            'basic_salary' => 'decimal:2',
            'total_allowances' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'bonus_amount' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'advance_deduction' => 'decimal:2',
            'loan_deduction' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function payslip(): HasOne
    {
        return $this->hasOne(Payslip::class);
    }
}
