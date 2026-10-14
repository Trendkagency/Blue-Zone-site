<?php

namespace App\Enums;

enum RawMaterialMovementType: string
{
    case Receipt = 'receipt';
    case QcSampling = 'qc_sampling';
    case ProductionDispense = 'production_dispense';
    case Transfer = 'transfer';
    case Adjustment = 'adjustment';
    case Scrap = 'scrap';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Receipt => $locale === 'ar' ? 'استلام توريد (GRN)' : 'Inbound Receipt (GRN)',
            self::QcSampling => $locale === 'ar' ? 'سحب عينة مخبرية (QC Sample)' : 'QC Lab Sampling',
            self::ProductionDispense => $locale === 'ar' ? 'صرف لأمر إنتاج (WO Dispense)' : 'Production Work Order Dispense',
            self::Transfer => $locale === 'ar' ? 'نقل بين المستودعات' : 'Inter-Warehouse Transfer',
            self::Adjustment => $locale === 'ar' ? 'تسوية جردية معتمدة' : 'Approved Stock Adjustment',
            self::Scrap => $locale === 'ar' ? 'إتلاف وتكهين مواد' : 'Scrap / Disposal',
        };
    }
}
