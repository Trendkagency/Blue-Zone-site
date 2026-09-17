<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmSegment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'description',
        'rules',
        'customer_count',
        'is_active',
        'last_evaluated_at',
    ];

    protected $casts = [
        'rules' => 'array',
        'customer_count' => 'integer',
        'is_active' => 'boolean',
        'last_evaluated_at' => 'datetime',
    ];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
