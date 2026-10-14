<?php

namespace App\Models;

use App\Enums\QcVerdict;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class QcInspection extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'inspection_code',
        'raw_material_batch_id',
        'sample_size',
        'sample_uom',
        'inspector_id',
        'verifier_id',
        'verdict',
        'waiver_reason',
        'waiver_expires_at',
        'tested_at',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'verdict' => QcVerdict::class,
            'sample_size' => 'decimal:4',
            'waiver_expires_at' => 'date',
            'tested_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(RawMaterialBatch::class, 'raw_material_batch_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(QcInspectionParameter::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('coa_documents')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }

    /**
     * Enforce strict dual sign-off rule: verifier must differ from inspector
     */
    public function verifySignOff(User $verifier): void
    {
        if ($this->inspector_id === $verifier->id) {
            throw new DomainException(__('qc.errors.self_verification_prohibited', [
                'default' => 'Separation of duties violation: An inspector cannot verify their own inspection.',
            ]));
        }

        $this->verifier_id = $verifier->id;
        $this->verified_at = now();
        $this->save();
    }

    public static function generateInspectionCode(): string
    {
        $year = date('Y');
        $prefix = "QC-{$year}-";

        return DB::transaction(function () use ($prefix) {
            $latest = self::where('inspection_code', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $seq = 1;
            if ($latest && preg_match('/QC-\d{4}-(\d+)/', $latest->inspection_code, $matches)) {
                $seq = ((int) $matches[1]) + 1;
            }

            return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        });
    }
}
