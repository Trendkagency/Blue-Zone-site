<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollPeriod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'total_gross',
        'total_deductions',
        'total_net',
        'status',
        'finalized_by',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'finalized_by' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'finalized_at' => 'datetime',
            'total_gross' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'total_net' => 'decimal:2',
        ];
    }

    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function records(): HasMany
    {
        return $this->hasMany(PayrollRecord::class);
    }
}
