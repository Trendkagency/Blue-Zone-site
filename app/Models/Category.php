<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
<<<<<<< HEAD

class Category extends Model
{
=======
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;

>>>>>>> origin/main
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

<<<<<<< HEAD
=======
    /**
     * Register Spatie media collections for Category.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->singleFile();

        $this->addMediaCollection('banner')
            ->singleFile();
    }

    /**
     * Get category icon URL (media or legacy string).
     */
    public function getIconUrlAttribute(): ?string
    {
        if ($this->hasMedia('icon')) {
            return $this->getFirstMediaUrl('icon');
        }

        return $this->icon;
    }

>>>>>>> origin/main
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
