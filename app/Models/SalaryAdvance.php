<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryAdvance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'amount',
        'request_date',
        'approved_date',
        'repayment_start_date',
        'installments',
        'installment_amount',
        'remaining_amount',
        'status',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'approved_by' => 'integer',
            'amount' => 'decimal:2',
            'installment_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'installments' => 'integer',
            'request_date' => 'date',
            'approved_date' => 'date',
            'repayment_start_date' => 'date',
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
