<?php

namespace App\Enums;

use DomainException;

enum WorkOrderStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Dispensing = 'dispensing';
    case InProduction = 'in_production';
    case QcReview = 'qc_review';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Draft => $locale === 'ar' ? 'مسودة أمر إنتاج' : 'Draft',
            self::Scheduled => $locale === 'ar' ? 'مجدول ومعتمد للتنفيذ' : 'Scheduled',
            self::Dispensing => $locale === 'ar' ? 'قيد وزن وصرف المواد' : 'Dispensing & Staging',
            self::InProduction => $locale === 'ar' ? 'قيد التصنيع والمزج' : 'In Production / Compounding',
            self::QcReview => $locale === 'ar' ? 'مراجعة وفحص المنتج التام' : 'Finished Good QC Review',
            self::Completed => $locale === 'ar' ? 'مكتمل ومودع في المستودع' : 'Completed & Stocked',
            self::Cancelled => $locale === 'ar' ? 'ملغي' : 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'badge-neutral',
            self::Scheduled => 'badge-info',
            self::Dispensing => 'badge-warning',
            self::InProduction => 'badge-primary',
            self::QcReview => 'badge-warning',
            self::Completed => 'badge-success',
            self::Cancelled => 'badge-danger',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        $allowed = match ($this) {
            self::Draft => [self::Scheduled, self::Cancelled],
            self::Scheduled => [self::Dispensing, self::Cancelled],
            self::Dispensing => [self::InProduction, self::Cancelled],
            self::InProduction => [self::QcReview, self::Cancelled],
            self::QcReview => [self::Completed, self::Cancelled],
            self::Completed => [],
            self::Cancelled => [],
        };

        return in_array($target, $allowed, true);
    }

    public function validateTransitionTo(self $target): void
    {
        if (! $this->canTransitionTo($target)) {
            throw new DomainException("Illegal Work Order transition from [{$this->value}] to [{$target->value}].");
        }
    }
}
