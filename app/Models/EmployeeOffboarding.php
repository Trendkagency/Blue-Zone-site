<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOffboarding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'offboarding_type',
        'submission_date',
        'last_working_date',
        'reason',
        'notice_period_days',
        'status',
        'notes',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'approved_by' => 'integer',
            'submission_date' => 'date',
            'last_working_date' => 'date',
            'notice_period_days' => 'integer',
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

    public function exitInterview(): HasOne
    {
        return $this->hasOne(ExitInterview::class, 'offboarding_id');
    }

    public function finalSettlement(): HasOne
    {
        return $this->hasOne(FinalSettlement::class, 'offboarding_id');
    }
}
