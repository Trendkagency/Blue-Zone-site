<?php

namespace App\Models;

use App\Enums\SupplierType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Supplier extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'name_en',
        'name_ar',
        'contact_name',
        'email',
        'phone',
        'country_id',
        'city',
        'address',
        'rating',
        'gmp_certified',
        'iso_certified',
        'audit_expiry',
        'payment_terms',
        'currency',
        'lead_time_days',
        'is_active',
        'qa_approved_by',
        'qa_approved_at',
        'procurement_approved_by',
        'procurement_approved_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => SupplierType::class,
            'rating' => 'decimal:2',
            'gmp_certified' => 'boolean',
            'iso_certified' => 'boolean',
            'audit_expiry' => 'date',
            'lead_time_days' => 'integer',
            'is_active' => 'boolean',
            'qa_approved_at' => 'datetime',
            'procurement_approved_at' => 'datetime',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function qaApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qa_approved_by');
    }

    public function procurementApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procurement_approved_by');
    }

    public function materialPrices(): HasMany
    {
        return $this->hasMany(SupplierMaterialPrice::class);
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class, 'default_supplier_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(RawMaterialBatch::class);
    }

    /**
     * Dual Sign-Off Check: QA Director and Procurement Head must be different users.
     */
    public function isDualApproved(): bool
    {
        return ! empty($this->qa_approved_by)
            && ! empty($this->procurement_approved_by)
            && $this->qa_approved_by !== $this->procurement_approved_by;
    }

    public function approveByQa(User $user): void
    {
        if ($this->procurement_approved_by === $user->id) {
            throw new \DomainException('Separation of duties violation: You cannot provide both QA and Procurement approvals.');
        }

        $this->qa_approved_by = $user->id;
        $this->qa_approved_at = now();

        if ($this->isDualApproved()) {
            $this->is_active = true;
        }

        $this->save();
    }

    public function approveByProcurement(User $user): void
    {
        if ($this->qa_approved_by === $user->id) {
            throw new \DomainException('Separation of duties violation: You cannot provide both QA and Procurement approvals.');
        }

        $this->procurement_approved_by = $user->id;
        $this->procurement_approved_at = now();

        if ($this->isDualApproved()) {
            $this->is_active = true;
        }

        $this->save();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cgmp_certificates')->singleFile();
        $this->addMediaCollection('iso_certificates')->singleFile();
        $this->addMediaCollection('sample_coas');
        $this->addMediaCollection('audit_reports');
    }

    public static function generateNextCode(): string
    {
        $year = date('Y');
        $prefix = "SUP-{$year}-";
        $latest = static::withTrashed()
            ->where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('code');

        if ($latest && preg_match('/(\d+)$/', $latest, $m)) {
            $seq = (int) $m[1] + 1;
        } else {
            $count = static::withTrashed()->count();
            $seq = $count + 1;
        }

        return sprintf('%s%04d', $prefix, $seq);
    }

    public function getContactPersonAttribute(): ?string
    {
        return $this->contact_name;
    }

    public function setContactPersonAttribute(?string $value): void
    {
        $this->attributes['contact_name'] = $value;
    }

    public function getAuditValidUntilAttribute(): mixed
    {
        return $this->audit_expiry;
    }

    public function setAuditValidUntilAttribute($value): void
    {
        $this->attributes['audit_expiry'] = $value;
    }
}
