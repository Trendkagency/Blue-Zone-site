<?php

namespace App\Models\Mr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactClassification extends Model
{
    use HasFactory;

    protected $table = 'contact_classifications';

    protected $fillable = [
        'code',
        'label',
        'points',
        'required_visits',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'required_visits' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'classification_id');
    }
}
