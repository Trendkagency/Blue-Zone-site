<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReviewItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'performance_review_id',
        'category',
        'title',
        'rating',
        'comments',
    ];

    protected function casts(): array
    {
        return [
            'performance_review_id' => 'integer',
            'rating' => 'decimal:1',
        ];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }
}
