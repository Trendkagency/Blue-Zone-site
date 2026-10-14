<?php

namespace App\Enums;

enum QcVerdict: string
{
    case Pending = 'pending';
    case Passed = 'passed';
    case Failed = 'failed';
    case ConditionalPass = 'conditional_pass';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Pending => $locale === 'ar' ? 'قيد الفحص والتحليل' : 'Under Testing',
            self::Passed => $locale === 'ar' ? 'مطابق ومجاز مخبرياً' : 'Passed / Certified',
            self::Failed => $locale === 'ar' ? 'راسب / غير مطابق' : 'Failed / Rejected',
            self::ConditionalPass => $locale === 'ar' ? 'إجازة استثنائية مشروطة' : 'Conditional Release (Waiver)',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'badge-warning',
            self::Passed => 'badge-success',
            self::Failed => 'badge-danger',
            self::ConditionalPass => 'badge-info',
        };
    }
}
