<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceKpi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'position_id',
        'name',
        'description',
        'measurement_unit',
        'default_target',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'position_id' => 'integer',
            'default_target' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
