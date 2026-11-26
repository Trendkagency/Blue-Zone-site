<?php

namespace App\Console\Commands;

use App\Enums\RawMaterialBatchStatus;
use App\Models\ProductBatch;
use App\Models\RawMaterialBatch;
use Illuminate\Console\Command;

class CheckBatchExpiriesCommand extends Command
{
    protected $signature = 'bz:check-expiries';
    protected $description = 'Scan all raw material and finished product batches for expiration and alert upcoming thresholds (30/60/90 days)';

    public function handle(): int
    {
        $this->info('Scanning raw material and finished product batches for expiration thresholds...');
        $today = now()->toDateString();

        // 1. Mark expired RawMaterialBatches while preserving inventory invariant (available -> quarantine)
        $expiredRmCount = 0;
        $expiredRmBatches = RawMaterialBatch::with('rawMaterial')
            ->where('status', RawMaterialBatchStatus::Approved)
            ->whereDate('expiry_date', '<=', $today)
            ->get();

        foreach ($expiredRmBatches as $batch) {
            $batch->status = RawMaterialBatchStatus::Expired;
            $batch->quarantine_qty = round((float) $batch->quarantine_qty + (float) $batch->available_qty, 4);
            $batch->available_qty = 0.0000;
            $batch->saveQuietly();
            $expiredRmCount++;
            $this->warn("Expired Raw Material Batch #{$batch->batch_number} (" . ($batch->rawMaterial?->name_en ?? 'Unknown') . ") marked EXPIRED.");
        }

        // 2. Mark expired ProductBatches while preserving inventory invariant (available -> quarantine)
        $expiredFgCount = 0;
        $expiredFgBatches = ProductBatch::with('product')
            ->where('status', 'approved')
            ->whereDate('expiry_date', '<=', $today)
            ->get();

        foreach ($expiredFgBatches as $fgBatch) {
            $fgBatch->status = 'expired';
            $fgBatch->quarantine_qty = (int) $fgBatch->quarantine_qty + (int) $fgBatch->available_qty;
            $fgBatch->available_qty = 0;
            $fgBatch->saveQuietly();
            $expiredFgCount++;
            $this->warn("Expired Product Batch #{$fgBatch->batch_number} (" . ($fgBatch->product?->name_en ?? 'Unknown') . ") marked EXPIRED.");
        }

        // 3. Count upcoming risk batches (30d & 60d across both catalogs)
        $rmIn30 = RawMaterialBatch::where('status', RawMaterialBatchStatus::Approved)
            ->where('current_qty', '>', 0)
            ->whereBetween('expiry_date', [now()->addDay()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();
        $fgIn30 = ProductBatch::where('status', 'approved')
            ->where('current_qty', '>', 0)
            ->whereBetween('expiry_date', [now()->addDay()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();

        $rmIn60 = RawMaterialBatch::where('status', RawMaterialBatchStatus::Approved)
            ->where('current_qty', '>', 0)
            ->whereBetween('expiry_date', [now()->addDays(31)->toDateString(), now()->addDays(60)->toDateString()])
            ->count();
        $fgIn60 = ProductBatch::where('status', 'approved')
            ->where('current_qty', '>', 0)
            ->whereBetween('expiry_date', [now()->addDays(31)->toDateString(), now()->addDays(60)->toDateString()])
            ->count();

        $totalExpired = $expiredRmCount + $expiredFgCount;
        $in30Days = $rmIn30 + $fgIn30;
        $in60Days = $rmIn60 + $fgIn60;

        $this->info("✓ Expiry scan completed. {$totalExpired} marked expired (RM: {$expiredRmCount}, FG: {$expiredFgCount}). Upcoming risk: {$in30Days} within 30d, {$in60Days} within 60d.");

        return self::SUCCESS;
    }
}
