<?php

namespace App\Models;

use App\Enums\RawMaterialMovementType;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'raw_material_batch_id',
        'movement_type',
        'from_location',
        'to_location',
        'quantity',
        'previous_qty',
        'new_qty',
        'user_id',
        'reference_type',
        'reference_id',
        'note',
        'notes',
        'ip_address',
        'created_at',
    ];

    public function getNotesAttribute(): ?string
    {
        return $this->note;
    }

    public function setNotesAttribute(?string $value): void
    {
        $this->attributes['note'] = $value;
    }

    protected function casts(): array
    {
        return [
            'movement_type' => RawMaterialMovementType::class,
            'quantity' => 'decimal:4',
            'previous_qty' => 'decimal:4',
            'new_qty' => 'decimal:4',
            'created_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(RawMaterialBatch::class, 'raw_material_batch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Immutable append-only ledger enforcement
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new DomainException('Raw material movements form an immutable ledger and cannot be edited.');
        });

        static::deleting(function () {
            throw new DomainException('Raw material movements form an immutable ledger and cannot be deleted.');
        });
    }
}
