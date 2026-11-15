<?php

namespace App\Services;

use App\Enums\RawMaterialMovementType;
use App\Models\FinancialTransaction;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\RawMaterialMovement;
use Illuminate\Support\Facades\DB;

class LandedCostService
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
     * Retrieve a list of recent candidate financial transactions for the Select dropdown.
     */
    public function getAvailableTransactions(?int $supplierId = null, int $limit = 25): array
    {
        $query = FinancialTransaction::with(['account', 'expense.category', 'supplier'])
            ->whereIn('type', ['expense', 'supplier_payment', 'adjustment']);

        if ($supplierId) {
            $query->where(function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId)
                    ->orWhereNull('supplier_id');
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $formatted = [];
        foreach ($transactions as $t) {
            $date = $t->transaction_date ? (is_string($t->transaction_date) ? $t->transaction_date : $t->transaction_date->format('Y-m-d')) : '';
            $typeLabel = match ($t->type) {
                'expense' => app()->getLocale() === 'ar' ? 'مصروف شحن/تخليص' : 'Freight/Customs Expense',
                'supplier_payment' => app()->getLocale() === 'ar' ? 'دفعة مورد' : 'Supplier Payment',
                'adjustment' => app()->getLocale() === 'ar' ? 'تسوية مالية' : 'Financial Adjustment',
                default => (string) $t->type,
            };
            $amountFormatted = number_format((float) $t->amount, 2);
            $note = $t->notes ? ' - ' . \Illuminate\Support\Str::limit($t->notes, 30) : '';

            $formatted[] = [
                'id' => $t->id,
                'transaction_number' => $t->transaction_number,
                'amount' => (float) $t->amount,
                'date' => $date,
                'type' => $t->type,
                'label' => "[{$t->transaction_number}] {$date} — {$amountFormatted} EGP ({$typeLabel}){$note}",
            ];
        }

        return $formatted;
    }

    /**
     * Retrieve a list of recent candidate stock adjustments for the Select dropdown.
     */
    public function getAvailableAdjustments(array $items = [], int $limit = 25): array
    {
        $productIds = [];
        $rawMaterialIds = [];

        foreach ($items as $item) {
            if (!empty($item['product_id'])) {
                $productIds[] = (int) $item['product_id'];
            }
            if (!empty($item['raw_material_id'])) {
                $rawMaterialIds[] = (int) $item['raw_material_id'];
            }
        }

        // Product inventory adjustments
        $invQuery = InventoryMovement::with('product')
            ->whereIn('movement_type', ['Manual Adjustment', 'Damaged', 'Expired', 'Return', 'Stock Out']);

        if (!empty($productIds)) {
            $invQuery->whereIn('product_id', $productIds);
        }

        $invMovements = $invQuery->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $formatted = [];
        foreach ($invMovements as $m) {
            $qty = abs((float) $m->quantity);
            $cost = (float) ($m->product?->cost_price ?? $m->product?->price * 0.5 ?? 0);
            $val = round($qty * $cost, 2);
            $date = $m->date ? (is_string($m->date) ? $m->date : $m->date->format('Y-m-d')) : '';
            $prodName = app()->getLocale() === 'ar' ? ($m->product_name_ar ?? $m->product_name_en) : ($m->product_name_en ?? $m->product_name_ar);

            $formatted[] = [
                'id' => 'inv_' . $m->id,
                'source' => 'inventory',
                'movement_id' => $m->id,
                'movement_number' => $m->movement_number,
                'movement_type' => $m->movement_type,
                'item_name' => $prodName,
                'quantity' => $qty,
                'unit_cost' => $cost,
                'total_value' => $val,
                'date' => $date,
                'label' => "[{$m->movement_number}] {$date} — {$m->movement_type} ({$prodName}) | {$qty} وحدة × " . number_format($cost, 2) . " = خصم " . number_format($val, 2) . " EGP",
            ];
        }

        // Raw material adjustments
        $rawQuery = RawMaterialMovement::with(['batch.rawMaterial'])
            ->whereIn('movement_type', [RawMaterialMovementType::Adjustment->value, RawMaterialMovementType::Scrap->value]);

        if (!empty($rawMaterialIds)) {
            $rawQuery->whereHas('batch', function ($q) use ($rawMaterialIds) {
                $q->whereIn('raw_material_id', $rawMaterialIds);
            });
        }

        $rawMovements = $rawQuery->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        foreach ($rawMovements as $rm) {
            $qty = abs((float) $rm->quantity);
            $cost = (float) ($rm->batch?->rawMaterial?->standard_cost ?? 0);
            $val = round($qty * $cost, 2);
            $date = $rm->created_at ? $rm->created_at->format('Y-m-d') : '';
            $matName = $rm->batch?->rawMaterial?->name_ar ?? $rm->batch?->rawMaterial?->name_en ?? 'Raw Material';
            $num = 'RMOV-' . str_pad((string)$rm->id, 5, '0', STR_PAD_LEFT);
            $typeLabel = $rm->movement_type instanceof RawMaterialMovementType ? $rm->movement_type->label(app()->getLocale()) : (string) $rm->movement_type;

            $formatted[] = [
                'id' => 'raw_' . $rm->id,
                'source' => 'raw_material',
                'movement_id' => $rm->id,
                'movement_number' => $num,
                'movement_type' => $typeLabel,
                'item_name' => $matName,
                'quantity' => $qty,
                'unit_cost' => $cost,
                'total_value' => $val,
                'date' => $date,
                'label' => "[{$num}] {$date} — {$typeLabel} ({$matName}) | {$qty} كجم × " . number_format($cost, 2) . " = خصم " . number_format($val, 2) . " EGP",
            ];
        }

        return $formatted;
    }

    /**
     * Retrieve the latest relevant financial transaction for the given supplier.
     */
    public function fetchLatestTransaction(?int $supplierId): ?FinancialTransaction
    {
        if (!$supplierId) {
            return null;
        }

        $transaction = FinancialTransaction::with(['account', 'expense.category'])
            ->where('supplier_id', $supplierId)
            ->whereIn('type', ['expense', 'supplier_payment', 'adjustment'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (!$transaction) {
            $transaction = FinancialTransaction::with(['account', 'expense.category'])
                ->where('type', 'expense')
                ->whereHas('expense.category', function ($q) {
                    $q->where('name_en', 'like', '%freight%')
                        ->orWhere('name_en', 'like', '%shipping%')
                        ->orWhere('name_en', 'like', '%custom%')
                        ->orWhere('name_ar', 'like', '%شحن%')
                        ->orWhere('name_ar', 'like', '%تخليص%')
                        ->orWhere('name_ar', 'like', '%جمارك%');
                })
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();
        }

        return $transaction;
    }

    /**
     * Retrieve the latest stock adjustment operations for the items listed in the PO.
     */
    public function fetchLatestStockAdjustments(array $items): array
    {
        $adjustments = [];

        foreach ($items as $item) {
            $isProduct = ($item['item_type'] ?? '') === 'product' || !empty($item['product_id']);
            $unitPrice = (float) ($item['unit_price'] ?? 0);

            if ($isProduct && !empty($item['product_id'])) {
                $productId = (int) $item['product_id'];
                $product = Product::find($productId);
                if (!$product) {
                    continue;
                }

                $movement = InventoryMovement::where('product_id', $productId)
                    ->whereIn('movement_type', ['Manual Adjustment', 'Damaged', 'Expired', 'Return', 'Stock Out'])
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($movement) {
                    $qty = abs((float) $movement->quantity);
                    $effectiveUnitCost = $unitPrice > 0 ? $unitPrice : (float) ($product->cost_price ?? 0);
                    $costDeduction = round($qty * $effectiveUnitCost, 2);

                    $adjustments[] = [
                        'id' => 'inv_' . $movement->id,
                        'item_type' => 'product',
                        'product_id' => $productId,
                        'name' => app()->getLocale() === 'ar' ? ($product->name_ar ?? $product->name_en) : ($product->name_en ?? $product->name_ar),
                        'sku' => $product->sku ?? ('PRD-' . $productId),
                        'movement_id' => $movement->id,
                        'movement_number' => $movement->movement_number,
                        'movement_type' => $movement->movement_type,
                        'quantity' => $qty,
                        'unit_cost' => $effectiveUnitCost,
                        'total_value' => $costDeduction,
                        'date' => $movement->date ? (is_string($movement->date) ? $movement->date : $movement->date->format('Y-m-d')) : null,
                        'note' => $movement->note,
                    ];
                }
            } elseif (!$isProduct && !empty($item['raw_material_id'])) {
                $rawMaterialId = (int) $item['raw_material_id'];
                $material = RawMaterial::find($rawMaterialId);
                if (!$material) {
                    continue;
                }

                $rawMovement = RawMaterialMovement::whereHas('batch', function ($q) use ($rawMaterialId) {
                        $q->where('raw_material_id', $rawMaterialId);
                    })
                    ->whereIn('movement_type', [RawMaterialMovementType::Adjustment->value, RawMaterialMovementType::Scrap->value])
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($rawMovement) {
                    $qty = abs((float) $rawMovement->quantity);
                    $effectiveUnitCost = $unitPrice > 0 ? $unitPrice : (float) ($material->standard_cost ?? 0);
                    $costDeduction = round($qty * $effectiveUnitCost, 2);

                    $adjustments[] = [
                        'id' => 'raw_' . $rawMovement->id,
                        'item_type' => 'raw_material',
                        'raw_material_id' => $rawMaterialId,
                        'name' => app()->getLocale() === 'ar' ? ($material->name_ar ?? $material->name_en) : ($material->name_en ?? $material->name_ar),
                        'sku' => $material->code ?? ('RM-' . $rawMaterialId),
                        'movement_id' => $rawMovement->id,
                        'movement_number' => 'RMOV-' . str_pad((string)$rawMovement->id, 5, '0', STR_PAD_LEFT),
                        'movement_type' => $rawMovement->movement_type instanceof RawMaterialMovementType ? $rawMovement->movement_type->label(app()->getLocale()) : (string) $rawMovement->movement_type,
                        'quantity' => $qty,
                        'unit_cost' => $effectiveUnitCost,
                        'total_value' => $costDeduction,
                        'date' => $rawMovement->created_at ? $rawMovement->created_at->format('Y-m-d') : null,
                        'note' => $rawMovement->note ?? $rawMovement->notes,
                    ];
                }
            }
        }

        return $adjustments;
    }

    /**
     * Compute automated landed cost deduction and IAS 2 value-based allocation.
     * Supports specific transaction selection & specific adjustment selection from dropdowns.
     *
     * @param int|null $supplierId
     * @param array $items
     * @param int|null $selectedTransactionId
     * @param string|null $selectedAdjustmentId
     * @return array
     */
    public function calculateLandedCost(?int $supplierId, array $items, ?int $selectedTransactionId = null, ?string $selectedAdjustmentId = null): array
    {
        // 1. Transaction Selection Logic
        $transaction = null;
        if ($selectedTransactionId) {
            $transaction = FinancialTransaction::with(['account', 'expense.category'])->find($selectedTransactionId);
        }
        if (!$transaction && $selectedTransactionId === null) {
            $transaction = $this->fetchLatestTransaction($supplierId);
        }
        $transactionAmount = (float) ($transaction?->amount ?? 0);

        // 2. Stock Adjustment Selection Logic
        $totalAdjustmentDeduction = 0.0;
        $adjustmentsList = $this->getAvailableAdjustments($items);

        if ($selectedAdjustmentId) {
            $chosen = collect($adjustmentsList)->firstWhere('id', $selectedAdjustmentId);
            if ($chosen) {
                $totalAdjustmentDeduction = (float) $chosen['total_value'];
            }
        } elseif ($selectedAdjustmentId === null) {
            // Default: pick the latest adjustment
            $latestAdjustments = $this->fetchLatestStockAdjustments($items);
            foreach ($latestAdjustments as $adj) {
                $totalAdjustmentDeduction += (float) ($adj['total_value'] ?? 0);
            }
            if (empty($selectedAdjustmentId) && !empty($adjustmentsList[0])) {
                $selectedAdjustmentId = $adjustmentsList[0]['id'];
                $totalAdjustmentDeduction = (float) $adjustmentsList[0]['total_value'];
            }
        }
        $totalAdjustmentDeduction = round($totalAdjustmentDeduction, 2);

        // 3. Core Accounting Formula: Net Landed Cost = max(0, Transaction Amount - Stock Adjustment)
        $rawDifference = $transactionAmount - $totalAdjustmentDeduction;
        $netLandedCost = max(0.0, round($rawDifference, 2));

        $creditNoteRequired = $rawDifference < 0;
        $creditNoteAmount = $creditNoteRequired ? round(abs($rawDifference), 2) : 0.0;

        // 4. Subtotal calculation of PO items
        $poSubtotal = 0.0;
        $itemCalculations = [];

        foreach ($items as $index => $item) {
            $qty = max(0.0, (float) ($item['ordered_qty'] ?? 0));
            $price = max(0.0, (float) ($item['unit_price'] ?? 0));
            $lineTotal = round($qty * $price, 2);
            $poSubtotal += $lineTotal;

            $itemCalculations[$index] = [
                'index' => $index,
                'item_type' => ($item['item_type'] ?? '') === 'product' || !empty($item['product_id']) ? 'product' : 'raw_material',
                'product_id' => $item['product_id'] ?? null,
                'raw_material_id' => $item['raw_material_id'] ?? null,
                'ordered_qty' => $qty,
                'unit_price' => $price,
                'line_total' => $lineTotal,
                'allocated_landed_cost' => 0.0,
                'landed_unit_cost' => $price,
            ];
        }

        // 5. Apportion Net Landed Cost using Value-Based allocation (IAS 2)
        if ($netLandedCost > 0 && count($itemCalculations) > 0) {
            $allocatedSum = 0.0;
            $largestIndex = null;
            $maxLineTotal = -1.0;

            foreach ($itemCalculations as $idx => $calc) {
                if ($poSubtotal > 0) {
                    $weight = $calc['line_total'] / $poSubtotal;
                    $allocated = round($netLandedCost * $weight, 2);
                } else {
                    $allocated = round($netLandedCost / count($itemCalculations), 2);
                }

                $itemCalculations[$idx]['allocated_landed_cost'] = $allocated;
                $allocatedSum += $allocated;

                if ($calc['line_total'] > $maxLineTotal) {
                    $maxLineTotal = $calc['line_total'];
                    $largestIndex = $idx;
                }
            }

            // Adjust rounding penny gap on largest line
            $roundingGap = round($netLandedCost - $allocatedSum, 2);
            if ($roundingGap != 0.0 && $largestIndex !== null) {
                $itemCalculations[$largestIndex]['allocated_landed_cost'] = round($itemCalculations[$largestIndex]['allocated_landed_cost'] + $roundingGap, 2);
            }

            // Compute effective landed unit cost
            foreach ($itemCalculations as $idx => $calc) {
                $qty = $calc['ordered_qty'];
                $allocated = $itemCalculations[$idx]['allocated_landed_cost'];
                $unitCost = $qty > 0 ? round($calc['unit_price'] + ($allocated / $qty), 4) : $calc['unit_price'];
                $itemCalculations[$idx]['landed_unit_cost'] = $unitCost;
            }
        }

        $availableTransactions = $this->getAvailableTransactions($supplierId);

        return [
            'success' => true,
            'selected_transaction_id' => $transaction?->id,
            'selected_adjustment_id' => $selectedAdjustmentId,
            'transaction' => $transaction ? [
                'id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number,
                'type' => $transaction->type,
                'amount' => $transactionAmount,
                'date' => $transaction->transaction_date ? (is_string($transaction->transaction_date) ? $transaction->transaction_date : $transaction->transaction_date->format('Y-m-d')) : null,
                'reference_number' => $transaction->reference_number ?? $transaction->transaction_number,
            ] : null,
            'available_transactions' => $availableTransactions,
            'available_adjustments' => $adjustmentsList,
            'transaction_amount' => $transactionAmount,
            'adjustment_total' => $totalAdjustmentDeduction,
            'net_landed_cost' => $netLandedCost,
            'credit_note_required' => $creditNoteRequired,
            'credit_note_amount' => $creditNoteAmount,
            'subtotal' => round($poSubtotal, 2),
            'grand_total' => round($poSubtotal + $netLandedCost, 2),
            'allocated_items' => array_values($itemCalculations),
        ];
    }
}
