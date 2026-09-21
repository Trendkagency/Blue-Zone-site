<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_record_id',
        'employee_id',
        'payslip_number',
        'gross_salary',
        'net_salary',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'payroll_record_id' => 'integer',
            'employee_id' => 'integer',
            'gross_salary' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'generated_at' => 'datetime',
        ];
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(PayrollRecord::class, 'payroll_record_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
