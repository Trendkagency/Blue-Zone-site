<?php

namespace App\Observers;

use App\Enums\RawMaterialBatchStatus;
use App\Models\RawMaterialBatch;
use Illuminate\Support\Facades\Cache;

class RawMaterialBatchObserver
{
    public function creating(RawMaterialBatch $batch): void
    {
        if (empty($batch->status)) {
            $batch->status = RawMaterialBatchStatus::Quarantine;
        }

        // Generate batch number if missing
        if (empty($batch->batch_number)) {
            $batch->batch_number = RawMaterialBatch::generateBatchNumber();
        }
    }

    public function saving(RawMaterialBatch $batch): void
    {
        $batch->assertInvariant();
    }

    public function saved(RawMaterialBatch $batch): void
    {
        Cache::forget('bz_report_rm_valuation');
        Cache::forget('bz_report_rm_expiry');
        Cache::forget('bz_report_mrp_forecast');
        Cache::forget('bz_widget_expiring_capital');
        Cache::forget('bz_widget_quarantine_queue');
        Cache::forget('bz_widget_low_stock_rm');
    }
}
