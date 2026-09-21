<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_assets';

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'serial_number',
        'model',
        'purchase_date',
        'cost',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EmployeeAssetAssignment::class, 'hr_asset_id');
    }

    public function currentAssignment()
    {
        return $this->hasOne(EmployeeAssetAssignment::class, 'hr_asset_id')->whereNull('returned_at')->latest('assigned_at');
    }
}
