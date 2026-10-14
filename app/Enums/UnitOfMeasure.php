<?php

namespace App\Enums;

enum UnitOfMeasure: string
{
    case Kilogram = 'kg';
    case Gram = 'g';
    case Milligram = 'mg';
    case Liter = 'l';
    case Milliliter = 'ml';
    case Pieces = 'pcs';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Kilogram => $locale === 'ar' ? 'كيلوغرام (كغ)' : 'Kilogram (kg)',
            self::Gram => $locale === 'ar' ? 'غرام (غ)' : 'Gram (g)',
            self::Milligram => $locale === 'ar' ? 'مليغرام (ملغ)' : 'Milligram (mg)',
            self::Liter => $locale === 'ar' ? 'لتر (ل)' : 'Liter (L)',
            self::Milliliter => $locale === 'ar' ? 'مليلتر (مل)' : 'Milliliter (ml)',
            self::Pieces => $locale === 'ar' ? 'قطعة / وحدة' : 'Pieces (pcs)',
        };
    }
}
