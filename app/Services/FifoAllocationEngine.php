<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\RawMaterialMovement;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Singleton Engine for First Expired / First Out (FEFO / FIFO) Batch Allocation and Stock Fulfillment.
 */
class FifoAllocationEngine
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Allocate Finished Product quantity using strict FEFO (earliest expiry first).
     *
     * @return array<int, array{batch: ProductBatch, allocated_qty: int, unit_cost: float}>
     */
    public function allocateProduct(
        Product $product,
        int $requiredQty,
        string $locationId = 'central_wh',
        ?int $specificBatchId = null
    ): array {
        if ($requiredQty <= 0) {
            return [];
        }

        return DB::transaction(function () use ($product, $requiredQty, $locationId, $specificBatchId) {
            $query = ProductBatch::query()
                ->where('product_id', $product->id)
                ->where('location_id', $locationId)
                ->where('status', 'approved')
                ->where('available_qty', '>', 0)
                ->where('expiry_date', '>', now()->toDateString())
                ->lockForUpdate();

            if ($specificBatchId) {
                $query->where('id', $specificBatchId);
            } else {
                $query->orderBy('expiry_date', 'asc')->orderBy('id', 'asc');
            }

            $batches = $query->get();

            // Auto-provision initial batch for legacy catalog stock ONLY if no batches exist at all
            if ($batches->isEmpty() && !$specificBatchId && $product->batches()->doesntExist() && (int) $product->stock_online >= (int) $requiredQty) {
                $initialQty = max((int) $product->stock_online, (int) $requiredQty);
                $initBatch = ProductBatch::create([
                    'batch_number' => ProductBatch::generateBatchNumber($product),
                    'product_id' => $product->id,
                    'location_id' => $locationId ?: 'online',
                    'initial_qty' => $initialQty,
                    'current_qty' => $initialQty,
                    'available_qty' => $initialQty,
                    'reserved_qty' => 0,
                    'quarantine_qty' => 0,
                    'unit_cost' => (float) ($product->cost_price ?? 0),
                    'manufacture_date' => now()->toDateString(),
                    'expiry_date' => $product->calculateDefaultExpiryDate(now()),
                    'status' => 'approved',
                ]);
                $batches = collect([$initBatch]);
            }

            $allocations = [];
            $remaining = $requiredQty;

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $take = min($remaining, $batch->available_qty);
                $allocations[] = [
                    'batch' => $batch,
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'expiry_date' => $batch->expiry_date->toDateString(),
                    'allocated_qty' => $take,
                    'unit_cost' => (float) $batch->unit_cost,
                ];

                $remaining -= $take;
            }

            if ($remaining > 0) {
                $productName = $product->name_en ?? "Product #{$product->id}";
                $available = (int) $batches->sum('available_qty');
                throw new DomainException(
                    app()->getLocale() === 'ar'
                        ? "الرصيد الصالح غير منتهي الصلاحية للمنتج [{$productName}] غير كافٍ. المطلوب: {$requiredQty}، المتوفر: {$available}."
                        : "Insufficient approved, unexpired stock for [{$productName}]. Required: {$requiredQty}, Available: {$available}."
                );
            }

            return $allocations;
        });
    }

    /**
     * Allocate Raw Material quantity using strict FEFO.
     *
     * @return array<int, array{batch: RawMaterialBatch, allocated_qty: float, unit_cost: float}>
     */
    public function allocateRawMaterial(
        RawMaterial $material,
        float $requiredQty,
        string $locationId = 'central_wh',
        ?int $specificBatchId = null
    ): array {
        if ($requiredQty <= 0) {
            return [];
        }

        return DB::transaction(function () use ($material, $requiredQty, $locationId, $specificBatchId) {
            $query = RawMaterialBatch::query()
                ->where('raw_material_id', $material->id)
                ->where('location_id', $locationId)
                ->where('status', 'approved')
                ->where('available_qty', '>', 0)
                ->where('expiry_date', '>', now()->toDateString())
                ->lockForUpdate();

            if ($specificBatchId) {
                $query->where('id', $specificBatchId);
            } else {
                $query->orderBy('expiry_date', 'asc')->orderBy('id', 'asc');
            }

            $batches = $query->get();
            $allocations = [];
            $remaining = round($requiredQty, 4);

            foreach ($batches as $batch) {
                if ($remaining <= 0.0001) {
                    break;
                }

                $take = round(min($remaining, (float) $batch->available_qty), 4);
                $allocations[] = [
                    'batch' => $batch,
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'expiry_date' => $batch->expiry_date->toDateString(),
                    'allocated_qty' => $take,
                    'unit_cost' => (float) $batch->unit_cost,
                ];

                $remaining = round($remaining - $take, 4);
            }

            if ($remaining > 0.0001) {
                $matName = $material->name_en ?? "Material #{$material->id}";
                $available = round((float) $batches->sum('available_qty'), 4);
                throw new DomainException(
                    app()->getLocale() === 'ar'
                        ? "رصيد المادة الخام الصالحة [{$matName}] غير كافٍ. المطلوب: {$requiredQty}، المتوفر: {$available}."
                        : "Insufficient approved raw material stock for [{$matName}]. Required: {$requiredQty}, Available: {$available}."
                );
            }

            return $allocations;
        });
    }

    /**
     * Commit and deduct allocated batches in database with movement audits.
     */
    public function commitProductDeduction(
        array $allocations,
        ?string $reason = null,
        ?string $userName = null,
        ?Model $reference = null
    ): void {
        DB::transaction(function () use ($allocations, $reason, $userName, $reference) {
            foreach ($allocations as $alloc) {
                /** @var ProductBatch $batch */
                $batch = $alloc['batch'] ?? ProductBatch::findOrFail($alloc['batch_id']);
                $qty = (int) $alloc['allocated_qty'];

                $prevStock = $batch->current_stock;
                $batch->deductQuantity($qty, $reason);

                // Update location inventory items as well
                $product = $batch->product;
                if ($product) {
                    InventoryService::adjustStock(
                        $product,
                        $batch->location_id,
                        -$qty,
                        'Sales Issue',
                        $reason ?? "Deducted via FEFO Batch {$batch->batch_number}",
                        $userName,
                        $reference ? get_class($reference) . " #{$reference->id}" : 'Invoice / Order'
                    );
                }
            }
        });
    }

    /**
     * Commit and deduct raw material batches.
     */
    public function commitRawMaterialDeduction(
        array $allocations,
        ?string $reason = null,
        ?string $userName = null,
        ?Model $reference = null
    ): void {
        DB::transaction(function () use ($allocations, $reason, $userName, $reference) {
            foreach ($allocations as $alloc) {
                /** @var RawMaterialBatch $batch */
                $batch = $alloc['batch'] ?? RawMaterialBatch::findOrFail($alloc['batch_id']);
                $qty = round((float) $alloc['allocated_qty'], 4);

                $prevCurrent = $batch->current_qty;
                $batch->available_qty = max(0, round((float) $batch->available_qty - $qty, 4));
                $batch->current_qty = max(0, round((float) $batch->current_qty - $qty, 4));
                $batch->assertInvariant();
                $batch->save();

                // Create audit movement
                RawMaterialMovement::create([
                    'raw_material_batch_id' => $batch->id,
                    'movement_type' => 'Consumption / Issue',
                    'quantity' => $qty,
                    'previous_qty' => $prevCurrent,
                    'new_qty' => $batch->current_qty,
                    'reference_type' => $reference ? get_class($reference) : 'Direct Issue',
                    'reference_id' => $reference?->id,
                    'notes' => $reason ?? "FEFO deduction for batch {$batch->batch_number}",
                    'created_by' => auth()->id() ?? 1,
                ]);
            }
        });
    }
}
