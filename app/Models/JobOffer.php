<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidate_id',
        'position_id',
        'department_id',
        'salary',
        'housing_allowance',
        'transportation_allowance',
        'employment_type',
        'start_date',
        'probation_period_months',
        'offer_expiry_date',
        'status',
        'notes',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'candidate_id' => 'integer',
            'position_id' => 'integer',
            'department_id' => 'integer',
            'approved_by' => 'integer',
            'salary' => 'decimal:2',
            'housing_allowance' => 'decimal:2',
            'transportation_allowance' => 'decimal:2',
            'start_date' => 'date',
            'offer_expiry_date' => 'date',
            'probation_period_months' => 'integer',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
