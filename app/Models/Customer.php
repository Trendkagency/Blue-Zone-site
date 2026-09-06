<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'city', 'country',
        'total_orders', 'total_spent', 'status', 'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'total_spent' => 'decimal:2',
            'registered_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
