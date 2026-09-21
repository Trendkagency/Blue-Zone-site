<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_salary_history';

    protected $fillable = [
        'employee_id',
        'old_salary',
        'new_salary',
        'effective_date',
        'reason',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'approved_by' => 'integer',
            'old_salary' => 'decimal:2',
            'new_salary' => 'decimal:2',
            'effective_date' => 'date',
        ];
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
