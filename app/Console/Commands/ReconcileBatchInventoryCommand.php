<?php

namespace App\Console\Commands;

use App\Models\RawMaterialBatch;
use DomainException;
use Illuminate\Console\Command;

class ReconcileBatchInventoryCommand extends Command
{
    protected $signature = 'bz:reconcile-batches';
    protected $description = 'Reconcile all raw material batch quantities and assert the mathematical invariant';

    public function handle(): int
    {
        $this->info('Starting BZ-OS Raw Material Batch Inventory Reconciliation...');

        $batches = RawMaterialBatch::all();
        $violations = 0;
        $checked = 0;

        foreach ($batches as $batch) {
            $checked++;
            try {
                $batch->assertInvariant();
            } catch (DomainException $e) {
                $violations++;
                $this->error("VIOLATION: {$e->getMessage()}");
            }
        }

        if ($violations === 0) {
            $this->info("✓ Successfully verified {$checked} batches. All mathematical invariants intact.");
            return self::SUCCESS;
        }

        $this->error("✗ Reconciliation completed with {$violations} invariant violations.");
        return self::FAILURE;
    }
}
