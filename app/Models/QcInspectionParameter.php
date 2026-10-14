<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QcInspectionParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_inspection_id',
        'parameter_name',
        'specification',
        'result_value',
        'status',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(QcInspection::class, 'qc_inspection_id');
    }

    public function isPassed(): bool
    {
        return strtolower($this->status) === 'pass';
    }
}
