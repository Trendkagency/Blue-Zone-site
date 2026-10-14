<?php

namespace App\Observers;

use App\Models\QcInspection;
use App\Services\RawMaterial\VendorEvaluationEngine;
use Illuminate\Support\Facades\Cache;

class QcInspectionObserver
{
    public function creating(QcInspection $inspection): void
    {
        if (empty($inspection->inspection_code)) {
            $inspection->inspection_code = QcInspection::generateInspectionCode();
        }
    }

    public function saved(QcInspection $inspection): void
    {
        $batch = $inspection->batch;
        if ($batch && $batch->supplier) {
            VendorEvaluationEngine::getInstance()->recalculateSupplierRating($batch->supplier);
        }

        Cache::forget('bz_report_qc_log');
        Cache::forget('bz_report_vendor_scorecard');
        Cache::forget('bz_widget_quarantine_queue');
    }
}
