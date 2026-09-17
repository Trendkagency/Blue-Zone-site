<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class CrmTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'color',
    ];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar);
    }

    public function leads(): MorphToMany
    {
        return $this->morphedByMany(CrmLead::class, 'taggable', 'crm_taggables', 'tag_id', 'taggable_id');
    }

    public function customers(): MorphToMany
    {
        return $this->morphedByMany(Customer::class, 'taggable', 'crm_taggables', 'tag_id', 'taggable_id');
    }

    public function opportunities(): MorphToMany
    {
        return $this->morphedByMany(CrmOpportunity::class, 'taggable', 'crm_taggables', 'tag_id', 'taggable_id');
    }

    public function companies(): MorphToMany
    {
        return $this->morphedByMany(CrmCompany::class, 'taggable', 'crm_taggables', 'tag_id', 'taggable_id');
    }
}
