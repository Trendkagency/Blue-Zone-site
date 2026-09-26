<?php

namespace App\Models\Mr;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mr_contacts';

    protected $fillable = [
        'code',
        'name',
        'specialty_id',
        'classification_id',
        'country_id',
        'city_id',
        'area_id',
        'region',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'hospital_clinic_name',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(ContactSpecialty::class, 'specialty_id');
    }

    public function classification(): BelongsTo
    {
        return $this->belongsTo(ContactClassification::class, 'classification_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Area::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ContactAssignment::class, 'contact_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'contact_id');
    }

    public function scheduledVisits(): HasMany
    {
        return $this->hasMany(ScheduledVisit::class, 'contact_id');
    }

    /**
     * Get coordinates as array [lat, lng]
     */
    public function getCoordinates(): ?array
    {
        if ($this->latitude !== null && $this->longitude !== null) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }
        return null;
    }

    /**
     * Get the doctor's visit quota status for a cycle based on their classification.
     */
    public function getVisitQuotaStatus(?int $cycleId = null): array
    {
        if (!$cycleId) {
            $cycleId = VisitCycle::where('status', 'active')->value('id') 
                ?: VisitCycle::latest('id')->value('id');
        }

        $class = $this->classification;
        $classCode = $class?->code ?? 'A';
        $maxVisits = ($class && (int) $class->required_visits > 0) ? (int) $class->required_visits : 1;

        // Count planned, completed, and in_progress visits scheduled for this cycle
        $scheduledCount = ScheduledVisit::where('contact_id', $this->id)
            ->when($cycleId, fn($q) => $q->where('cycle_id', $cycleId))
            ->whereIn('status', ['planned', 'completed', 'in_progress'])
            ->count();

        // Also count any direct completed visits without scheduled_visit_id
        $directCount = Visit::where('contact_id', $this->id)
            ->when($cycleId, fn($q) => $q->where('cycle_id', $cycleId))
            ->whereNull('scheduled_visit_id')
            ->count();

        $totalActive = $scheduledCount + $directCount;
        $canSchedule = $totalActive < $maxVisits;
        $remaining = max(0, $maxVisits - $totalActive);

        return [
            'can_schedule' => $canSchedule,
            'class_code' => $classCode,
            'class_label' => $class?->label ?? "Class {$classCode}",
            'max_visits' => $maxVisits,
            'current_count' => $totalActive,
            'remaining_visits' => $remaining,
            'cycle_id' => $cycleId,
            'message_ar' => $canSchedule 
                ? "متبقي {$remaining} من أصل {$maxVisits} زيارات لتصنيف Class {$classCode}"
                : "تم استنفاد الحد الأقصى ({$totalActive}/{$maxVisits} زيارات لتصنيف Class {$classCode})",
            'message_en' => $canSchedule
                ? "{$remaining} of {$maxVisits} visits remaining for Class {$classCode}"
                : "Quota limit reached ({$totalActive}/{$maxVisits} visits for Class {$classCode})",
        ];
    }
}
