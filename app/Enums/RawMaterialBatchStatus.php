<?php

namespace App\Enums;

use DomainException;

enum RawMaterialBatchStatus: string
{
    case Quarantine = 'quarantine';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case RetestRequired = 'retest_required';
    case Expired = 'expired';
    case Exhausted = 'exhausted';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Quarantine => $locale === 'ar' ? 'قيد الحجر المخبري' : 'Quarantine Inspection',
            self::Approved => $locale === 'ar' ? 'معتمد للاستخدام' : 'Approved for Production',
            self::Rejected => $locale === 'ar' ? 'مرفوض / غير مطابق' : 'Rejected',
            self::RetestRequired => $locale === 'ar' ? 'مطلوب إعادة فحص' : 'Retest Required',
            self::Expired => $locale === 'ar' ? 'منتهي الصلاحية' : 'Expired',
            self::Exhausted => $locale === 'ar' ? 'مستهلك بالكامل' : 'Exhausted',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Quarantine => 'badge-warning',
            self::Approved => 'badge-success',
            self::Rejected => 'badge-danger',
            self::RetestRequired => 'badge-info',
            self::Expired => 'badge-secondary',
            self::Exhausted => 'badge-neutral',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Quarantine => 'fa-flask-vial',
            self::Approved => 'fa-circle-check',
            self::Rejected => 'fa-triangle-exclamation',
            self::RetestRequired => 'fa-rotate',
            self::Expired => 'fa-clock',
            self::Exhausted => 'fa-box-open',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        $allowed = match ($this) {
            self::Quarantine => [self::Approved, self::Rejected],
            self::Approved => [self::RetestRequired, self::Expired, self::Exhausted],
            self::RetestRequired => [self::Approved, self::Rejected, self::Expired],
            self::Rejected => [], // Terminal state
            self::Expired => [],  // Terminal state
            self::Exhausted => [],// Terminal state
        };

        return in_array($target, $allowed, true);
    }

    public function validateTransitionTo(self $target): void
    {
        if (! $this->canTransitionTo($target)) {
            throw new DomainException("Illegal Batch transition from [{$this->value}] to [{$target->value}].");
        }
    }
}
