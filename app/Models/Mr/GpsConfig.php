<?php

namespace App\Models\Mr;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GpsConfig extends Model
{
    use HasFactory;

    protected $table = 'mr_gps_configs';

    protected $fillable = [
        'user_id',
        'allowed_radius_m',
        'is_gps_required',
        'mock_detection_enabled',
    ];

    protected function casts(): array
    {
        return [
            'allowed_radius_m' => 'integer',
            'is_gps_required' => 'boolean',
            'mock_detection_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
