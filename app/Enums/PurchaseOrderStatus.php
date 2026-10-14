<?php

namespace App\Enums;

use DomainException;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Confirmed = 'confirmed';
    case PartiallyReceived = 'partially_received';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Draft => $locale === 'ar' ? 'مسودة' : 'Draft',
            self::Submitted => $locale === 'ar' ? 'مرفوع للاعتماد' : 'Submitted for Approval',
            self::Confirmed => $locale === 'ar' ? 'معتمد ومؤكد لدى المورد' : 'Confirmed with Supplier',
            self::PartiallyReceived => $locale === 'ar' ? 'مستلم جزئياً' : 'Partially Received',
            self::Received => $locale === 'ar' ? 'مستلم بالكامل' : 'Fully Received',
            self::Cancelled => $locale === 'ar' ? 'ملغي' : 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'badge-neutral',
            self::Submitted => 'badge-warning',
            self::Confirmed => 'badge-info',
            self::PartiallyReceived => 'badge-primary',
            self::Received => 'badge-success',
            self::Cancelled => 'badge-danger',
        };
    }

    /**
     * Allowed state transitions map
     */
    public function canTransitionTo(self $target): bool
    {
        $allowed = match ($this) {
            self::Draft => [self::Submitted, self::Cancelled],
            self::Submitted => [self::Confirmed, self::Draft, self::Cancelled],
            self::Confirmed => [self::PartiallyReceived, self::Received, self::Cancelled],
            self::PartiallyReceived => [self::Received, self::Cancelled],
            self::Received => [],
            self::Cancelled => [],
        };

        return in_array($target, $allowed, true);
    }

    public function validateTransitionTo(self $target): void
    {
        if (! $this->canTransitionTo($target)) {
            throw new DomainException("Illegal Purchase Order transition from [{$this->value}] to [{$target->value}].");
        }
    }
}
