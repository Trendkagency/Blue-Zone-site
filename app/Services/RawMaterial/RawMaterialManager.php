<?php

namespace App\Services\RawMaterial;

use App\Enums\QcVerdict;
use App\Enums\RawMaterialBatchStatus;
use App\Enums\RawMaterialMovementType;
use App\Models\GoodsReceiptItem;
use App\Models\ProductRecipeItem;
use App\Models\QcInspection;
use App\Models\RawMaterialBatch;
use App\Models\RawMaterialMovement;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderMaterialConsumption;
use DomainException;
use Illuminate\Support\Facades\DB;

class RawMaterialManager
{
    private static ?self $instance = null;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Inbound dock receiving: Creates batch with status strictly forced to QUARANTINE.
     */
    public function receiveBatch(
        GoodsReceiptItem $item,
        User $receiver,
        ?string $locationId = null,
        ?string $storageBin = null
    ): RawMaterialBatch {
        return DB::transaction(function () use ($item, $receiver, $locationId, $storageBin) {
            $grn = $item->goodsReceipt;
            $rawMaterial = $item->rawMaterial;
            $unitCost = $item->purchaseOrderItem?->unit_price ?? 0;
            $locId = $locationId ?? $grn?->location_id ?? Location::first()?->id ?? 'central_wh';

            $batch = new RawMaterialBatch([
                'raw_material_id' => $rawMaterial->id,
                'batch_number' => RawMaterialBatch::generateBatchNumber(),
                'supplier_lot' => $item->supplier_lot,
                'purchase_order_id' => $grn?->purchase_order_id,
                'goods_receipt_id' => $grn?->id,
                'supplier_id' => $grn?->supplier_id ?? $item->purchaseOrderItem?->purchaseOrder?->supplier_id,
                'location_id' => $locId,
                'storage_bin' => $storageBin,
                'initial_qty' => $item->received_qty,
                'current_qty' => $item->received_qty,
                'available_qty' => 0.0000,
                'reserved_qty' => 0.0000,
                'quarantine_qty' => $item->received_qty,
                'unit_cost' => $unitCost,
                'manufacture_date' => $item->manufacture_date,
                'expiry_date' => $item->expiry_date,
                'status' => RawMaterialBatchStatus::Quarantine,
            ]);

            $batch->save();
            $batch->assertInvariant();

            // Record immutable audit movement
            RawMaterialMovement::create([
                'raw_material_batch_id' => $batch->id,
                'movement_type' => RawMaterialMovementType::Receipt,
                'from_location' => 'Inbound Dock',
                'to_location' => $locationId.($storageBin ? " ({$storageBin})" : ''),
                'quantity' => $item->received_qty,
                'previous_qty' => 0.0000,
                'new_qty' => $item->received_qty,
                'user_id' => $receiver->id,
                'reference_type' => 'GRN',
                'reference_id' => $grn->grn_number,
                'note' => 'Initial dock receipt placed under mandatory laboratory quarantine.',
                'ip_address' => request()->ip(),
            ]);

            return $batch;
        });
    }

    /**
     * Release quarantine into approved inventory following verified dual-signoff inspection.
     */
    public function releaseQuarantine(
        RawMaterialBatch $batch,
        QcInspection $inspection,
        User $verifier
    ): void {
        DB::transaction(function () use ($batch, $inspection, $verifier) {
            $lockedBatch = RawMaterialBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();

            if ($lockedBatch->status !== RawMaterialBatchStatus::Quarantine && $lockedBatch->status !== RawMaterialBatchStatus::RetestRequired) {
                throw new DomainException("Cannot release batch [{$lockedBatch->batch_number}] from status [{$lockedBatch->status->value}].");
            }

            if (! in_array($inspection->verdict, [QcVerdict::Passed, QcVerdict::ConditionalPass], true)) {
                throw new DomainException('Cannot release batch without a Passed or ConditionalPass verdict.');
            }

            // Verify dual sign-off
            $inspection->verifySignOff($verifier);

            $quarantineQty = (float) $lockedBatch->quarantine_qty;
            $lockedBatch->status->validateTransitionTo(RawMaterialBatchStatus::Approved);
            $lockedBatch->status = RawMaterialBatchStatus::Approved;
            $lockedBatch->quarantine_qty = 0.0000;
            $lockedBatch->available_qty = round((float) $lockedBatch->available_qty + $quarantineQty, 4);
            $lockedBatch->save();
            $lockedBatch->assertInvariant();

            RawMaterialMovement::create([
                'raw_material_batch_id' => $lockedBatch->id,
                'movement_type' => RawMaterialMovementType::Transfer,
                'from_location' => 'QUARANTINE-WH',
                'to_location' => $lockedBatch->location_id.($lockedBatch->storage_bin ? " ({$lockedBatch->storage_bin})" : ''),
                'quantity' => $quarantineQty,
                'previous_qty' => $lockedBatch->current_qty,
                'new_qty' => $lockedBatch->current_qty,
                'user_id' => $verifier->id,
                'reference_type' => 'QC',
                'reference_id' => $inspection->inspection_code,
                'note' => "Released from quarantine following dual-signed QC clearance ({$inspection->verdict->value}).",
                'ip_address' => request()->ip(),
            ]);
        });
    }

    /**
     * Reject batch permanently following failed QC testing.
     */
    public function rejectBatch(
        RawMaterialBatch $batch,
        QcInspection $inspection,
        User $verifier,
        string $reason
    ): void {
        DB::transaction(function () use ($batch, $inspection, $verifier, $reason) {
            $lockedBatch = RawMaterialBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();

            $inspection->verifySignOff($verifier);
            $lockedBatch->status->validateTransitionTo(RawMaterialBatchStatus::Rejected);
            $lockedBatch->status = RawMaterialBatchStatus::Rejected;
            $lockedBatch->available_qty = 0.0000;
            $lockedBatch->reserved_qty = 0.0000;
            // Quarantine qty remains to reflect physical holding until RTV/destruction
            $lockedBatch->save();
            $lockedBatch->assertInvariant();

            RawMaterialMovement::create([
                'raw_material_batch_id' => $lockedBatch->id,
                'movement_type' => RawMaterialMovementType::Transfer,
                'from_location' => $lockedBatch->location_id,
                'to_location' => 'REJECTED-HOLDING-AREA',
                'quantity' => $lockedBatch->current_qty,
                'previous_qty' => $lockedBatch->current_qty,
                'new_qty' => $lockedBatch->current_qty,
                'user_id' => $verifier->id,
                'reference_type' => 'QC',
                'reference_id' => $inspection->inspection_code,
                'note' => "Rejected: {$reason}. Transferred to bio-hazard / return quarantine.",
                'ip_address' => request()->ip(),
            ]);
        });
    }

    /**
     * FEFO auto-pick and reserve ingredients for a scheduled Work Order.
     */
    public function reserveForWorkOrder(WorkOrder $workOrder): array
    {
        return DB::transaction(function () use ($workOrder) {
            $recipeItems = ProductRecipeItem::where('product_id', $workOrder->product_id)->get();
            $reservations = [];

            foreach ($recipeItems as $recipeItem) {
                $needed = $recipeItem->calculateRequiredQuantity($workOrder->planned_qty);
                $remainingToReserve = $needed;

                // FEFO Order: expiry_date ASC, id ASC with row locking
                $eligibleBatches = RawMaterialBatch::where('raw_material_id', $recipeItem->raw_material_id)
                    ->where('status', RawMaterialBatchStatus::Approved)
                    ->where('available_qty', '>', 0)
                    ->where('expiry_date', '>', now()->toDateString())
                    ->orderBy('expiry_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->get();

                $totalAvailable = (float) $eligibleBatches->sum('available_qty');
                if ($totalAvailable < $needed && $recipeItem->is_critical) {
                    $mat = $recipeItem->rawMaterial;
                    throw new DomainException(__('production.errors.insufficient_raw_material', [
                        'default' => "Insufficient approved FEFO stock for [{$mat->code} - {$mat->name_en}]. Required: {$needed} {$mat->unit_of_measure->value}, Available: {$totalAvailable} {$mat->unit_of_measure->value}.",
                    ]));
                }

                foreach ($eligibleBatches as $batch) {
                    if ($remainingToReserve <= 0) {
                        break;
                    }

                    $canReserve = min((float) $batch->available_qty, $remainingToReserve);
                    $batch->available_qty = round((float) $batch->available_qty - $canReserve, 4);
                    $batch->reserved_qty = round((float) $batch->reserved_qty + $canReserve, 4);
                    $batch->save();
                    $batch->assertInvariant();

                    $remainingToReserve = round($remainingToReserve - $canReserve, 4);
                    $reservations[] = [
                        'batch' => $batch,
                        'raw_material_batch_id' => $batch->id,
                        'batch_number' => $batch->batch_number,
                        'quantity' => $canReserve,
                    ];
                }
            }

            return $reservations;
        });
    }

    /**
     * Dispense exact gravimetric quantity during clean-room compounding.
     */
    public function dispenseMaterial(
        WorkOrder $workOrder,
        RawMaterialBatch $batch,
        float $quantity,
        User $operator,
        string $scannedBarcode
    ): WorkOrderMaterialConsumption {
        if ($quantity <= 0) {
            throw new DomainException('Dispense quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($workOrder, $batch, $quantity, $operator, $scannedBarcode) {
            $lockedBatch = RawMaterialBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();

            // Strict Validation Rules
            if ($lockedBatch->status !== RawMaterialBatchStatus::Approved) {
                throw new DomainException("Cannot dispense material: Batch [{$lockedBatch->batch_number}] is [{$lockedBatch->status->value}], not approved.");
            }

            if ($lockedBatch->expiry_date < now()->toDateString()) {
                throw new DomainException("Cannot dispense material: Batch [{$lockedBatch->batch_number}] expired on [{$lockedBatch->expiry_date->toDateString()}].");
            }

            $current = (float) $lockedBatch->current_qty;
            if ($current < $quantity) {
                throw new DomainException("Insufficient batch stock for [{$lockedBatch->batch_number}]. On-hand: {$current}, Requested: {$quantity}.");
            }

            // Deduct from reserved first if allocated, otherwise available
            $prevQty = $lockedBatch->current_qty;
            $newQty = round($current - $quantity, 4);

            if ((float) $lockedBatch->reserved_qty >= $quantity) {
                $lockedBatch->reserved_qty = round((float) $lockedBatch->reserved_qty - $quantity, 4);
            } else {
                $deficit = round($quantity - (float) $lockedBatch->reserved_qty, 4);
                $lockedBatch->reserved_qty = 0.0000;
                $lockedBatch->available_qty = max(0.0000, round((float) $lockedBatch->available_qty - $deficit, 4));
            }

            $lockedBatch->current_qty = $newQty;
            if ($lockedBatch->current_qty <= 0.0001) {
                $lockedBatch->status = RawMaterialBatchStatus::Exhausted;
                $lockedBatch->current_qty = 0.0000;
                $lockedBatch->available_qty = 0.0000;
                $lockedBatch->reserved_qty = 0.0000;
            }

            $lockedBatch->save();
            $lockedBatch->assertInvariant();

            // Record immutable consumption
            $consumption = WorkOrderMaterialConsumption::create([
                'work_order_id' => $workOrder->id,
                'raw_material_batch_id' => $lockedBatch->id,
                'quantity' => $quantity,
                'operator_id' => $operator->id,
                'scanned_barcode' => $scannedBarcode,
                'created_at' => now(),
            ]);

            // Record immutable audit movement
            RawMaterialMovement::create([
                'raw_material_batch_id' => $lockedBatch->id,
                'movement_type' => RawMaterialMovementType::ProductionDispense,
                'from_location' => $lockedBatch->location_id,
                'to_location' => "Clean Room (WO #{$workOrder->order_number})",
                'quantity' => $quantity,
                'previous_qty' => $prevQty,
                'new_qty' => $lockedBatch->current_qty,
                'user_id' => $operator->id,
                'reference_type' => 'WO',
                'reference_id' => $workOrder->order_number,
                'note' => "Dispensed for finished good batch [{$workOrder->target_finished_lot}].",
                'ip_address' => request()->ip(),
            ]);

            return $consumption;
        });
    }

    /**
     * Inter-warehouse batch transfer.
     */
    public function transferBatch(
        RawMaterialBatch $batch,
        string $toLocationId,
        ?string $toBin,
        User $user,
        ?string $note = null
    ): void {
        DB::transaction(function () use ($batch, $toLocationId, $toBin, $user, $note) {
            $lockedBatch = RawMaterialBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();
            $fromLoc = $lockedBatch->location_id.($lockedBatch->storage_bin ? " ({$lockedBatch->storage_bin})" : '');

            $lockedBatch->location_id = $toLocationId;
            $lockedBatch->storage_bin = $toBin;
            $lockedBatch->save();

            RawMaterialMovement::create([
                'raw_material_batch_id' => $lockedBatch->id,
                'movement_type' => RawMaterialMovementType::Transfer,
                'from_location' => $fromLoc,
                'to_location' => $toLocationId.($toBin ? " ({$toBin})" : ''),
                'quantity' => $lockedBatch->current_qty,
                'previous_qty' => $lockedBatch->current_qty,
                'new_qty' => $lockedBatch->current_qty,
                'user_id' => $user->id,
                'reference_type' => 'TRANSFER',
                'reference_id' => (string) $lockedBatch->id,
                'note' => $note ?? 'Internal location bin transfer.',
                'ip_address' => request()->ip(),
            ]);
        });
    }

    /**
     * Stock adjustment with dual sign-off requirement for significant variances.
     */
    public function adjustStock(
        RawMaterialBatch $batch,
        float $newQuantity,
        string $reason,
        User $user,
        ?User $secondApprover = null,
        float $highVarianceThresholdQty = 5.0000
    ): void {
        if ($newQuantity < 0) {
            throw new DomainException('Adjusted quantity cannot be negative.');
        }

        DB::transaction(function () use ($batch, $newQuantity, $reason, $user, $secondApprover, $highVarianceThresholdQty) {
            $lockedBatch = RawMaterialBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();
            $diff = abs((float) $lockedBatch->current_qty - $newQuantity);

            if ($diff >= $highVarianceThresholdQty) {
                if (! $secondApprover || $secondApprover->id === $user->id) {
                    throw new DomainException("Adjustments greater than {$highVarianceThresholdQty} units require a second distinct managerial approver.");
                }
            }

            $prevQty = $lockedBatch->current_qty;
            $delta = $newQuantity - (float) $lockedBatch->current_qty;

            $lockedBatch->current_qty = $newQuantity;
            if ($lockedBatch->status === RawMaterialBatchStatus::Approved) {
                $lockedBatch->available_qty = max(0.0000, round((float) $lockedBatch->available_qty + $delta, 4));
            } elseif ($lockedBatch->status === RawMaterialBatchStatus::Quarantine) {
                $lockedBatch->quarantine_qty = max(0.0000, round((float) $lockedBatch->quarantine_qty + $delta, 4));
            }

            if ($lockedBatch->current_qty <= 0) {
                $lockedBatch->status = RawMaterialBatchStatus::Exhausted;
                $lockedBatch->available_qty = 0.0000;
                $lockedBatch->reserved_qty = 0.0000;
                $lockedBatch->quarantine_qty = 0.0000;
            }

            $lockedBatch->save();
            $lockedBatch->assertInvariant();

            RawMaterialMovement::create([
                'raw_material_batch_id' => $lockedBatch->id,
                'movement_type' => RawMaterialMovementType::Adjustment,
                'from_location' => $lockedBatch->location_id,
                'to_location' => $lockedBatch->location_id,
                'quantity' => abs($delta),
                'previous_qty' => $prevQty,
                'new_qty' => $newQuantity,
                'user_id' => $user->id,
                'reference_type' => 'ADJUSTMENT',
                'reference_id' => (string) $lockedBatch->id,
                'note' => "Stock adjustment: {$reason}".($secondApprover ? " (Dual-verified by {$secondApprover->name})" : ''),
                'ip_address' => request()->ip(),
            ]);
        });
    }
}
