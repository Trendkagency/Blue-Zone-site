<?php

namespace App\Enums;

enum SupplierType: string
{
    case IngredientSupplier = 'ingredient_supplier';
    case PackagingVendor = 'packaging_vendor';
    case LabService = 'lab_service';
    case LogisticsPartner = 'logistics';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::IngredientSupplier => $locale === 'ar' ? 'مورّد مواد فعّالة ومكونات' : 'Active Ingredient Supplier',
            self::PackagingVendor => $locale === 'ar' ? 'مورّد عبوات وتغليف' : 'Primary Packaging Vendor',
            self::LabService => $locale === 'ar' ? 'مختبر فحص تحليلي خارجي' : 'Analytical Lab Service',
            self::LogisticsPartner => $locale === 'ar' ? 'شريك لوجستي وسلسلة تبريد' : 'Cold-Chain Logistics Partner',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::IngredientSupplier => 'badge-primary',
            self::PackagingVendor => 'badge-info',
            self::LabService => 'badge-warning',
            self::LogisticsPartner => 'badge-secondary',
        };
    }
}
