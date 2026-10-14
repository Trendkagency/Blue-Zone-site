<?php

namespace App\Enums;

enum StorageCondition: string
{
    case Ambient = 'ambient_15_25c';
    case ColdRefrigerated = 'cold_2_8c';
    case Frozen = 'frozen_minus_20c';
    case ControlledHumidity = 'controlled_humidity';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Ambient => $locale === 'ar' ? 'حرارة الغرفة القياسية (15 - 25° م)' : 'Ambient Room Temp (15 - 25°C)',
            self::ColdRefrigerated => $locale === 'ar' ? 'مبرد وسلسلة تبريد (2 - 8° م)' : 'Cold Refrigerated (2 - 8°C)',
            self::Frozen => $locale === 'ar' ? 'تجميد وحفظ عميق (-20° م)' : 'Deep Frozen (-20°C)',
            self::ControlledHumidity => $locale === 'ar' ? 'رطوبة متحكم بها (<45% RH)' : 'Controlled Humidity (<45% RH)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Ambient => 'fa-temperature-half',
            self::ColdRefrigerated => 'fa-snowflake',
            self::Frozen => 'fa-icicles',
            self::ControlledHumidity => 'fa-droplet-slash',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Ambient => 'badge-neutral',
            self::ColdRefrigerated => 'badge-info',
            self::Frozen => 'badge-primary',
            self::ControlledHumidity => 'badge-warning',
        };
    }
}
