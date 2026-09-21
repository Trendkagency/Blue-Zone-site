<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'onboarding_template_id',
        'title_en',
        'title_ar',
        'description',
        'assigned_role',
        'due_days',
        'sort_order',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'onboarding_template_id' => 'integer',
            'due_days' => 'integer',
            'sort_order' => 'integer',
            'is_required' => 'boolean',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'onboarding_template_id');
    }

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' && !empty($this->title_ar) ? $this->title_ar : $this->title_en;
    }
}
