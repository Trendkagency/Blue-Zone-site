<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDisciplinaryAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'incident_date',
        'incident_type',
        'description',
        'investigation_notes',
        'employee_response',
        'action_type',
        'action_date',
        'deduction_amount',
        'suspension_days',
        'attachment_path',
        'status',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'approved_by' => 'integer',
            'incident_date' => 'date',
            'action_date' => 'date',
            'deduction_amount' => 'decimal:2',
            'suspension_days' => 'integer',
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
