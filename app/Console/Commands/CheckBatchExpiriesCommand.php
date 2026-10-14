<?php

namespace App\Console\Commands;

use App\Enums\RawMaterialBatchStatus;
use App\Models\RawMaterialBatch;
use Illuminate\Console\Command;

class CheckBatchExpiriesCommand extends Command
{
    protected $signature = 'bz:check-expiries';
    protected $description = 'Scan all raw material batches for expiration and alert upcoming thresholds (30/60/90 days)';

    public function handle(): int
    {
        $this->info('Scanning raw material batches for expiration thresholds...');

        // 1. Mark expired batches
        $expiredCount = 0;
        $expiredBatches = RawMaterialBatch::where('status', RawMaterialBatchStatus::Approved)
            ->where('expiry_date', '<', now()->toDateString())
            ->get();

        foreach ($expiredBatches as $batch) {
            $batch->status = RawMaterialBatchStatus::Expired;
            $batch->available_qty = 0.0000;
            $batch->saveQuietly();
            $expiredCount++;
            $this->warn("Expired Batch #{$batch->batch_number} ({$batch->rawMaterial->name_en}) marked EXPIRED.");
        }

        // 2. Count upcoming risk batches
        $in30Days = RawMaterialBatch::where('status', RawMaterialBatchStatus::Approved)
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();

        $in60Days = RawMaterialBatch::where('status', RawMaterialBatchStatus::Approved)
            ->whereBetween('expiry_date', [now()->addDays(31)->toDateString(), now()->addDays(60)->toDateString()])
            ->count();

        $this->info("✓ Expiry scan completed. {$expiredCount} marked expired. Upcoming risk: {$in30Days} within 30d, {$in60Days} within 60d.");

        return self::SUCCESS;
    }
}
