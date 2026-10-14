<?php

namespace App\Enums;

enum RawMaterialCategory: string
{
    case ActiveIngredient = 'active_ingredient';
    case Excipient = 'excipient';
    case PrimaryPackaging = 'packaging';
    case Solvent = 'solvent';
    case Flavoring = 'flavoring';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::ActiveIngredient => $locale === 'ar' ? 'مادة فعّالة حيوية (Active API)' : 'Active Bio-Ingredient (API)',
            self::Excipient => $locale === 'ar' ? 'مادة سواغ وإضافات تركيب' : 'Formulation Excipient',
            self::PrimaryPackaging => $locale === 'ar' ? 'عبوات وتغليف أولي' : 'Primary Packaging',
            self::Solvent => $locale === 'ar' ? 'مذيبات حيوية ومحاليل' : 'Bio-Solvent / Carrier',
            self::Flavoring => $locale === 'ar' ? 'منكهات طبيعية ومعدلات طعم' : 'Natural Flavoring',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ActiveIngredient => 'badge-primary',
            self::Excipient => 'badge-secondary',
            self::PrimaryPackaging => 'badge-info',
            self::Solvent => 'badge-accent',
            self::Flavoring => 'badge-warning',
        };
    }
}
