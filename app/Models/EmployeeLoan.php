<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLoan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'amount',
        'start_date',
        'installments',
        'installment_amount',
        'remaining_balance',
        'status',
        'notes',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'approved_by' => 'integer',
            'amount' => 'decimal:2',
            'installment_amount' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'installments' => 'integer',
            'start_date' => 'date',
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
