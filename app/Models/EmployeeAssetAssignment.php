<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAssetAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'hr_asset_id',
        'assigned_at',
        'expected_return_date',
        'returned_at',
        'condition_on_assignment',
        'condition_on_return',
        'notes',
        'assigned_by',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'hr_asset_id' => 'integer',
            'assigned_by' => 'integer',
            'assigned_at' => 'datetime',
            'expected_return_date' => 'date',
            'returned_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(HrAsset::class, 'hr_asset_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
