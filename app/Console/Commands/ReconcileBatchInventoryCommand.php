<?php

namespace App\Console\Commands;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\RawMaterialBatch;
use App\Services\InventoryService;
use DomainException;
use Illuminate\Console\Command;

class ReconcileBatchInventoryCommand extends Command
{
    protected $signature = 'bz:reconcile-batches {--backfill : Provision missing ProductBatches for stocked products and backfill unlinked order items}';
    protected $description = 'Reconcile raw material and finished product batch quantities, assert mathematical invariants, and optionally backfill missing batches/expiries';

    public function handle(): int
    {
        $this->info('Starting BZ-OS Batch & Expiry Inventory Reconciliation...');

        if ($this->option('backfill')) {
            $provisionedProducts = 0;
            $backfilledItems = 0;

            InventoryService::syncAllProductsInventory();
            foreach (Product::all() as $product) {
                $before = $product->batches()->count();
                InventoryService::syncMissingProductBatches($product);
                if ($product->batches()->count() > $before) {
                    $provisionedProducts++;
                }
            }

            $unlinkedItems = OrderItem::whereNotNull('product_id')
                ->where(function ($q) {
                    $q->whereNull('product_batch_id')
                        ->orWhereNull('expiry_date')
                        ->orWhereNull('batch_number');
                })
                ->get();

            foreach ($unlinkedItems as $item) {
                $batch = ProductBatch::where('product_id', $item->product_id)
                    ->where('status', 'approved')
                    ->orderByRaw('CASE WHEN available_qty > 0 THEN 0 ELSE 1 END')
                    ->orderBy('expiry_date', 'asc')
                    ->first()
                    ?? ProductBatch::where('product_id', $item->product_id)->orderBy('expiry_date', 'asc')->first();

                if ($batch) {
                    $item->product_batch_id = $item->product_batch_id ?: $batch->id;
                    $item->batch_number = $item->batch_number ?: $batch->batch_number;
                    $item->expiry_date = $item->expiry_date ?: $batch->expiry_date;
                    $item->saveQuietly();
                    $backfilledItems++;
                }
            }

            $this->info("✓ Backfill complete: {$provisionedProducts} product(s) initialized with FEFO batches, {$backfilledItems} order item(s) linked.");
        }

        $rmBatches = RawMaterialBatch::all();
        $fgBatches = ProductBatch::all();
        $violations = 0;
        $checked = 0;

        foreach ($rmBatches as $batch) {
            $checked++;
            try {
                $batch->assertInvariant();
            } catch (DomainException $e) {
                $violations++;
                $this->error("RM VIOLATION: {$e->getMessage()}");
            }
        }

        foreach ($fgBatches as $fgBatch) {
            $checked++;
            try {
                $fgBatch->assertInvariant();
            } catch (DomainException $e) {
                $violations++;
                $this->error("FG VIOLATION: {$e->getMessage()}");
            }
        }

        if ($violations === 0) {
            $this->info("✓ Successfully verified {$checked} batches (RM: {$rmBatches->count()}, FG: {$fgBatches->count()}). All mathematical invariants intact.");
            return self::SUCCESS;
        }

        $this->error("✗ Reconciliation completed with {$violations} invariant violations.");
        return self::FAILURE;
    }
}
