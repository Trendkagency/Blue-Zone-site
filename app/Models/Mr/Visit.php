<?php

namespace App\Models\Mr;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    protected $table = 'mr_visits';

    protected $fillable = [
        'scheduled_visit_id',
        'assignment_id',
        'mr_id',
        'contact_id',
        'cycle_id',
        'product_id',
        'checkin_at',
        'checkin_lat',
        'checkin_lng',
        'checkin_accuracy_m',
        'checkout_at',
        'checkout_lat',
        'checkout_lng',
        'duration_minutes',
        'distance_from_contact_m',
        'gps_verified',
        'gps_flag',
        'outcome',
        'notes',
        'device_meta',
    ];

    protected function casts(): array
    {
        return [
            'checkin_at' => 'datetime',
            'checkout_at' => 'datetime',
            'checkin_lat' => 'decimal:8',
            'checkin_lng' => 'decimal:8',
            'checkin_accuracy_m' => 'decimal:2',
            'checkout_lat' => 'decimal:8',
            'checkout_lng' => 'decimal:8',
            'duration_minutes' => 'integer',
            'distance_from_contact_m' => 'decimal:2',
            'gps_verified' => 'boolean',
            'device_meta' => 'array',
        ];
    }

    public function scheduledVisit(): BelongsTo
    {
        return $this->belongsTo(ScheduledVisit::class, 'scheduled_visit_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ContactAssignment::class, 'assignment_id');
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mr_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(VisitCycle::class, 'cycle_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Product::class, 'mr_visit_products', 'visit_id', 'product_id')->withTimestamps();
    }
}
