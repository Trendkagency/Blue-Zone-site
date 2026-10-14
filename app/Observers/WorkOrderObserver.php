<?php

namespace App\Observers;

use App\Enums\WorkOrderStatus;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\WorkOrder;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WorkOrderObserver
{
    public function creating(WorkOrder $order): void
    {
        if (empty($order->order_number)) {
            $order->order_number = WorkOrder::generateOrderNumber();
        }

        if (empty($order->target_finished_lot)) {
            $order->target_finished_lot = WorkOrder::generateFinishedLot();
        }
    }

    public function updating(WorkOrder $order): void
    {
        // When transitioning to Completed, credit finished goods into inventory_items
        if ($order->isDirty('status') && $order->status === WorkOrderStatus::Completed) {
            DB::transaction(function () use ($order) {
                // 1. Calculate actual raw material cost
                $totalCost = 0.0;
                foreach ($order->consumptions as $consumption) {
                    $unitCost = (float) ($consumption->batch->unit_cost ?? 0);
                    $totalCost += ($unitCost * (float) $consumption->quantity);
                }

                $order->total_material_cost = $totalCost;
                $order->completed_at = now();

                $producedUnits = max(1, (int) $order->produced_qty);
                $unitCost = round($totalCost / $producedUnits, 4);
                $order->unit_cost = $unitCost;

                // 2. Provision / increment finished product in inventory_items
                $product = $order->product;
                if ($product) {
                    $item = InventoryService::getItem($product, $order->location_id);
                    $prevStock = $item->current_stock;

                    $item->current_stock += $order->produced_qty;
                    $item->available_stock += $order->produced_qty;
                    $item->unit_cost = $unitCost;
                    $item->save();
                    $item->refreshStatus();

                    // Update product cost_price
                    $product->cost_price = $unitCost;
                    $product->save();
                    InventoryService::syncProductModelStock($product);

                    // 3. Log InventoryMovement for finished good
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'product_name_en' => $product->name_en,
                        'product_name_ar' => $product->name_ar,
                        'sku' => $product->sku,
                        'variant' => "Lot: {$order->target_finished_lot}",
                        'movement_type' => 'production_receipt',
                        'from_location' => "Clean Room (WO #{$order->order_number})",
                        'to_location' => $item->location_name_en ?? $order->location_id,
                        'quantity' => $order->produced_qty,
                        'previous_qty' => $prevStock,
                        'new_qty' => $item->current_stock,
                        'date' => now()->toDateString(),
                        'time' => now()->toTimeString(),
                        'user' => auth()->user()?->name ?? 'Manufacturing System',
                        'note' => "Manufactured from Work Order #{$order->order_number} (Lot: {$order->target_finished_lot})",
                    ]);
                }
            });
        }
    }

    public function saved(WorkOrder $order): void
    {
        Cache::forget('bz_report_scrap_variance');
        Cache::forget('bz_report_mrp_forecast');
    }
}
