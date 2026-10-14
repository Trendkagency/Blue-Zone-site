<?php

namespace App\Enums;

use DomainException;

enum GoodsReceiptStatus: string
{
    case Draft = 'draft';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Draft => $locale === 'ar' ? 'مسودة استلام' : 'Draft Receipt',
            self::Received => $locale === 'ar' ? 'تم الاستلام وإيداع الحجر' : 'Received & Quarantined',
            self::Cancelled => $locale === 'ar' ? 'ملغي' : 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'badge-neutral',
            self::Received => 'badge-success',
            self::Cancelled => 'badge-danger',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        $allowed = match ($this) {
            self::Draft => [self::Received, self::Cancelled],
            self::Received => [],
            self::Cancelled => [],
        };

        return in_array($target, $allowed, true);
    }

    public function validateTransitionTo(self $target): void
    {
        if (! $this->canTransitionTo($target)) {
            throw new DomainException("Illegal Goods Receipt transition from [{$this->value}] to [{$target->value}].");
        }
    }
}
