<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'icon',
        'description_en',
        'description_ar',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Register Spatie media collections for Category.
     */
    /**
     * Get category icon URL (media or legacy string).
     */
    public function getIconUrlAttribute(): ?string
    {
        if (method_exists($this, 'hasMedia') && $this->hasMedia('icon')) {
            return $this->getFirstMediaUrl('icon');
        }

        return $this->icon;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
