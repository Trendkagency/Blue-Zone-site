<?php

namespace App\Services\RawMaterial;

use App\Enums\RawMaterialBatchStatus;
use App\Models\GoodsReceipt;
use App\Models\RawMaterialBatch;
use App\Models\Supplier;

class VendorEvaluationEngine
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
     * Recompute composite vendor rating (1.00 to 5.00) based on QC pass rate and on-time delivery.
     */
    public function recalculateSupplierRating(Supplier $supplier): float
    {
        // 1. Quality Score (40% weight): Percentage of batches approved vs rejected
        $totalBatches = RawMaterialBatch::where('supplier_id', $supplier->id)->count();
        $approvedBatches = RawMaterialBatch::where('supplier_id', $supplier->id)
            ->whereIn('status', [RawMaterialBatchStatus::Approved, RawMaterialBatchStatus::Exhausted])
            ->count();
        $qcScore = $totalBatches > 0 ? ($approvedBatches / $totalBatches) * 5.0 : 5.0;

        // 2. On-Time Delivery Score (40% weight): Compare PO expected_date with GRN received_date
        $grns = GoodsReceipt::with('purchaseOrder')
            ->where('supplier_id', $supplier->id)
            ->get();
        $onTimeCount = 0;
        foreach ($grns as $grn) {
            if ($grn->purchaseOrder && $grn->purchaseOrder->expected_date) {
                if ($grn->received_date <= $grn->purchaseOrder->expected_date) {
                    $onTimeCount++;
                }
            } else {
                $onTimeCount++;
            }
        }
        $deliveryScore = $grns->count() > 0 ? ($onTimeCount / $grns->count()) * 5.0 : 5.0;

        // 3. Condition & Seal Score (20% weight): Temperature compliance and seal verification
        $compliantGrns = GoodsReceipt::where('supplier_id', $supplier->id)
            ->where('temperature_within_range', true)
            ->where('seal_verified', true)
            ->count();
        $conditionScore = $grns->count() > 0 ? ($compliantGrns / $grns->count()) * 5.0 : 5.0;

        $compositeRating = round(($qcScore * 0.40) + ($deliveryScore * 0.40) + ($conditionScore * 0.20), 2);
        $compositeRating = max(1.00, min(5.00, $compositeRating));

        $supplier->rating = $compositeRating;
        $supplier->saveQuietly();

        return $compositeRating;
    }
}
