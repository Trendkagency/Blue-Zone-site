<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GoodsReceiptStatus;
use App\Enums\PurchaseOrderStatus;
use App\Enums\RawMaterialBatchStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\RawMaterialBatch;
use App\Models\Supplier;
use App\Services\FinanceService;
use App\Services\InventoryService;
use App\Services\RawMaterial\RawMaterialManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GoodsReceiptController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GoodsReceipt::class);

        $query = GoodsReceipt::with(['purchaseOrder', 'supplier', 'location', 'receiver'])
            ->withCount('items');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('grn_number', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('purchaseOrder', function ($pq) use ($search) {
                        $pq->where('po_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($supplierId = $request->query('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $receipts = $query->orderBy('received_date', 'desc')->paginate($perPage)->withQueryString();
        $goodsReceipts = $receipts;

        $kpis = [
            'total_grns' => GoodsReceipt::count(),
            'received' => GoodsReceipt::where('status', GoodsReceiptStatus::Received)->count(),
            'drafts' => GoodsReceipt::where('status', GoodsReceiptStatus::Draft)->count(),
            'broken_seals' => GoodsReceipt::where('seal_verified', false)->count(),
        ];

        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();
        $statuses = GoodsReceiptStatus::cases();

        return view('admin.supply_chain.goods_receipts.index', compact('receipts', 'goodsReceipts', 'kpis', 'suppliers', 'statuses'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', GoodsReceipt::class);

        $selectedPo = null;
        $poId = $request->query('po_id') ?? old('purchase_order_id');
        if ($poId) {
            $selectedPo = PurchaseOrder::with(['items.rawMaterial', 'items.product', 'supplier', 'destinationLocation'])
                ->whereIn('status', [PurchaseOrderStatus::Confirmed, PurchaseOrderStatus::PartiallyReceived])
                ->find($poId);
        }

        $openOrders = PurchaseOrder::with(['supplier', 'items'])
            ->whereIn('status', [PurchaseOrderStatus::Confirmed, PurchaseOrderStatus::PartiallyReceived])
            ->orderBy('po_number', 'desc')
            ->get();
        $purchaseOrders = $openOrders;

        $locations = Location::where('is_active', true)->get();

        return view('admin.supply_chain.goods_receipts.create', compact('selectedPo', 'openOrders', 'purchaseOrders', 'locations'));
    }

    public function store(GoodsReceiptRequest $request): RedirectResponse
    {
        $this->authorize('create', GoodsReceipt::class);

        $grn = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $itemsData = $data['items'];
            unset($data['items']);

            $po = PurchaseOrder::with('items')->findOrFail($data['purchase_order_id']);
            $data['grn_number'] = GoodsReceipt::generateGrnNumber();
            $data['supplier_id'] = $po->supplier_id;
            $data['receiver_id'] = auth()->id();
            $data['status'] = GoodsReceiptStatus::Received;

            $grn = GoodsReceipt::create($data);
            $rawManager = RawMaterialManager::getInstance();

            foreach ($itemsData as $itemInput) {
                $isProduct = ($itemInput['item_type'] ?? '') === 'product' || !empty($itemInput['product_id']);
                $poItem = $po->items->firstWhere('id', $itemInput['purchase_order_item_id']);

                $grnItem = $grn->items()->create([
                    'purchase_order_item_id' => $itemInput['purchase_order_item_id'],
                    'item_type' => $isProduct ? 'product' : 'raw_material',
                    'product_id' => $isProduct ? $itemInput['product_id'] : null,
                    'raw_material_id' => !$isProduct ? ($itemInput['raw_material_id'] ?? null) : null,
                    'storage_bin' => $itemInput['storage_bin'] ?? null,
                    'ordered_qty' => $itemInput['received_qty'],
                    'received_qty' => $itemInput['received_qty'],
                    'rejected_qty' => $itemInput['rejected_qty'] ?? 0.0000,
                    'supplier_lot' => $itemInput['supplier_lot'],
                    'manufacture_date' => $itemInput['manufacture_date'],
                    'expiry_date' => $itemInput['expiry_date'],
                    'storage_condition' => $itemInput['storage_condition'] ?? null,
                    'notes' => $itemInput['notes'] ?? null,
                ]);

                if ($isProduct) {
                    $product = Product::find($itemInput['product_id']);
                    if ($product) {
                        $qty = (int) round((float) $itemInput['received_qty']);
                        
                        // Create physical ProductBatch for received finished goods
                        $mfgDate = $itemInput['manufacture_date'] ?: now()->subDays(14)->toDateString();
                        $expDate = $itemInput['expiry_date'] ?: $product->calculateDefaultExpiryDate($mfgDate);
                        $batchNumber = !empty($itemInput['supplier_lot']) ? $itemInput['supplier_lot'] : ProductBatch::generateBatchNumber();

                        $effectiveBatchUnitCost = (float) (($poItem?->landed_unit_cost && $poItem->landed_unit_cost > 0) ? $poItem->landed_unit_cost : ($poItem?->unit_price ?? $product->cost_price ?? 0));

                        ProductBatch::create([
                            'product_id' => $product->id,
                            'batch_number' => $batchNumber,
                            'supplier_lot' => $itemInput['supplier_lot'] ?? null,
                            'goods_receipt_id' => $grn->id,
                            'location_id' => $data['location_id'],
                            'storage_bin' => $itemInput['storage_bin'] ?? null,
                            'initial_qty' => $qty,
                            'current_qty' => $qty,
                            'available_qty' => $qty,
                            'reserved_qty' => 0,
                            'quarantine_qty' => 0,
                            'unit_cost' => $effectiveBatchUnitCost,
                            'manufacture_date' => $mfgDate,
                            'expiry_date' => $expDate,
                            'status' => 'approved',
                        ]);

                        // Update Product Cost Price using Weighted Average Cost (WAC - IAS 2) in inventory
                        $currentStock = (int) ($product->stock_online ?? 0);
                        $currentCost = (float) ($product->cost_price ?? 0);
                        $newStockTotal = $currentStock + $qty;
                        if ($newStockTotal > 0) {
                            $product->cost_price = round((($currentStock * $currentCost) + ($qty * $effectiveBatchUnitCost)) / $newStockTotal, 4);
                            $product->save();
                        }

                        InventoryService::adjustStock(
                            $product,
                            $data['location_id'],
                            $qty,
                            'Purchase Receipt',
                            "GRN: {$grn->grn_number} (PO: {$po->po_number}) | Lot: {$batchNumber}",
                            auth()->user()?->name,
                            $po->supplier?->name_en ?? 'Supplier',
                            false
                        );
                    }
                } else {
                    // Create quarantine batch via RawMaterialManager
                    $rawManager->receiveBatch(
                        $grnItem,
                        auth()->user(),
                        $data['location_id'],
                        $itemInput['storage_bin'] ?? null
                    );
                }

                // Update PO Item received quantity
                if ($poItem) {
                    $poItem->received_qty = round((float) $poItem->received_qty + (float) $itemInput['received_qty'], 4);
                    $poItem->save();
                }
            }

            // Update PO Status
            $allReceived = true;
            foreach ($po->items as $item) {
                if ((float) $item->received_qty < (float) $item->ordered_qty) {
                    $allReceived = false;
                    break;
                }
            }

            $po->status = $allReceived ? PurchaseOrderStatus::Received : PurchaseOrderStatus::PartiallyReceived;
            $po->save();

            return $grn;
        });

        return redirect()->route('admin.goods-receipts.show', $grn)
            ->with('success', __('goods_receipts.messages.created', [
                'default' => 'Goods receipt created successfully. Received items have been accurately credited to warehouse stock and laboratory quarantine.',
            ]));
    }

    public function show(GoodsReceipt $goodsReceipt): View
    {
        $this->authorize('view', $goodsReceipt);
        $goodsReceipt->load([
            'purchaseOrder.items.product',
            'purchaseOrder.items.rawMaterial',
            'supplier',
            'location',
            'receiver',
            'items.rawMaterial',
            'items.product',
            'batches.inspections',
        ]);

        $existingInvoice = PurchaseInvoice::where('purchase_order_id', $goodsReceipt->purchase_order_id)
            ->where('reference_number', $goodsReceipt->grn_number)
            ->first();

        return view('admin.supply_chain.goods_receipts.show', compact('goodsReceipt', 'existingInvoice'));
    }

    public function edit(GoodsReceipt $goodsReceipt): View
    {
        $this->authorize('update', $goodsReceipt);

        $goodsReceipt->load([
            'purchaseOrder.items.product',
            'purchaseOrder.items.rawMaterial',
            'supplier',
            'location',
            'receiver',
            'items.rawMaterial',
            'items.product',
            'items.purchaseOrderItem',
            'batches',
        ]);

        $existingInvoice = PurchaseInvoice::where('purchase_order_id', $goodsReceipt->purchase_order_id)
            ->where('reference_number', $goodsReceipt->grn_number)
            ->first();

        $locations = Location::where('is_active', true)->get();

        return view('admin.supply_chain.goods_receipts.edit', compact('goodsReceipt', 'existingInvoice', 'locations'));
    }

    public function update(GoodsReceiptRequest $request, GoodsReceipt $goodsReceipt): RedirectResponse
    {
        $this->authorize('update', $goodsReceipt);

        $goodsReceipt = DB::transaction(function () use ($request, $goodsReceipt) {
            $data = $request->validated();
            $itemsData = $data['items'] ?? [];
            unset($data['items']);

            // Update Header & Dock Telemetry
            $goodsReceipt->update([
                'location_id' => $data['location_id'] ?? $goodsReceipt->location_id,
                'received_date' => $data['received_date'] ?? $goodsReceipt->received_date,
                'carrier_name' => $data['carrier_name'] ?? $goodsReceipt->carrier_name,
                'tracking_number' => $data['tracking_number'] ?? $goodsReceipt->tracking_number,
                'container_count' => $data['container_count'] ?? $goodsReceipt->container_count,
                'temperature_logger_reading' => array_key_exists('temperature_logger_reading', $data) ? $data['temperature_logger_reading'] : $goodsReceipt->temperature_logger_reading,
                'temperature_within_range' => !empty($data['temperature_within_range']),
                'seal_verified' => !empty($data['seal_verified']),
                'discrepancy_notes' => $data['discrepancy_notes'] ?? $goodsReceipt->discrepancy_notes,
            ]);

            $po = $goodsReceipt->purchaseOrder()->with('items')->first();

            foreach ($itemsData as $itemInput) {
                $grnItem = null;
                if (!empty($itemInput['id'])) {
                    $grnItem = $goodsReceipt->items()->find($itemInput['id']);
                } elseif (!empty($itemInput['purchase_order_item_id'])) {
                    $grnItem = $goodsReceipt->items()->where('purchase_order_item_id', $itemInput['purchase_order_item_id'])->first();
                }

                if (!$grnItem) {
                    continue;
                }

                $oldQty = (float) $grnItem->received_qty;
                $newQty = (float) $itemInput['received_qty'];
                $diff = round($newQty - $oldQty, 4);

                $grnItem->update([
                    'received_qty' => $newQty,
                    'rejected_qty' => $itemInput['rejected_qty'] ?? 0.0000,
                    'supplier_lot' => $itemInput['supplier_lot'],
                    'manufacture_date' => $itemInput['manufacture_date'],
                    'expiry_date' => $itemInput['expiry_date'],
                    'storage_condition' => $itemInput['storage_condition'] ?? $grnItem->storage_condition,
                    'storage_bin' => $itemInput['storage_bin'] ?? $grnItem->storage_bin,
                    'notes' => $itemInput['notes'] ?? $grnItem->notes,
                ]);

                // Synchronize Finished Good ProductBatch & Stock
                if ($grnItem->item_type === 'product' || !empty($grnItem->product_id)) {
                    $product = $grnItem->product ?? Product::find($grnItem->product_id);
                    $productBatch = ProductBatch::where('goods_receipt_id', $goodsReceipt->id)
                        ->where('product_id', $grnItem->product_id)
                        ->first();

                    if ($productBatch) {
                        $productBatch->supplier_lot = $itemInput['supplier_lot'];
                        $productBatch->manufacture_date = $itemInput['manufacture_date'];
                        $productBatch->expiry_date = $itemInput['expiry_date'];
                        $productBatch->storage_bin = $itemInput['storage_bin'] ?? $productBatch->storage_bin;
                        $productBatch->location_id = $goodsReceipt->location_id;

                        if (abs($diff) > 0.0001) {
                            $intDiff = (int) round($diff);
                            $productBatch->initial_qty = max(0, $productBatch->initial_qty + $intDiff);
                            $productBatch->current_qty = max(0, $productBatch->current_qty + $intDiff);
                            $productBatch->available_qty = max(0, $productBatch->available_qty + $intDiff);
                            $productBatch->assertInvariant();
                        }
                        $productBatch->save();
                    }

                    if ($product && abs($diff) > 0.0001) {
                        InventoryService::adjustStock(
                            $product,
                            $goodsReceipt->location_id,
                            (int) round($diff),
                            'GRN Quantity Adjustment',
                            "Adjustment on GRN: {$goodsReceipt->grn_number} (PO: " . ($po?->po_number ?? 'N/A') . ")",
                            auth()->user()?->name,
                            $goodsReceipt->supplier?->name_en ?? 'Supplier',
                            false
                        );
                    }
                } else {
                    // Synchronize RawMaterialBatch (Quarantine or Approved)
                    $rmBatch = RawMaterialBatch::where('goods_receipt_id', $goodsReceipt->id)
                        ->where('raw_material_id', $grnItem->raw_material_id)
                        ->first();

                    if ($rmBatch) {
                        $rmBatch->supplier_lot = $itemInput['supplier_lot'];
                        $rmBatch->manufacture_date = $itemInput['manufacture_date'];
                        $rmBatch->expiry_date = $itemInput['expiry_date'];
                        $rmBatch->storage_bin = $itemInput['storage_bin'] ?? $rmBatch->storage_bin;
                        $rmBatch->location_id = $goodsReceipt->location_id;

                        if (abs($diff) > 0.0001) {
                            $rmBatch->initial_qty = max(0.0000, round((float) $rmBatch->initial_qty + $diff, 4));
                            $rmBatch->current_qty = max(0.0000, round((float) $rmBatch->current_qty + $diff, 4));

                            if ($rmBatch->status === RawMaterialBatchStatus::Quarantine) {
                                $rmBatch->quarantine_qty = max(0.0000, round((float) $rmBatch->quarantine_qty + $diff, 4));
                            } else {
                                $rmBatch->available_qty = max(0.0000, round((float) $rmBatch->available_qty + $diff, 4));
                            }
                            $rmBatch->assertInvariant();
                        }
                        $rmBatch->save();
                    }
                }

                // Update PO Item received quantity
                $poItem = $grnItem->purchaseOrderItem;
                if ($poItem && abs($diff) > 0.0001) {
                    $poItem->received_qty = max(0.0000, round((float) $poItem->received_qty + $diff, 4));
                    $poItem->save();
                }
            }

            // Re-evaluate PO Status
            if ($po) {
                $po->load('items');
                $allReceived = true;
                $anyReceived = false;
                foreach ($po->items as $item) {
                    if ((float) $item->received_qty >= (float) $item->ordered_qty) {
                        $anyReceived = true;
                    } else {
                        $allReceived = false;
                        if ((float) $item->received_qty > 0) {
                            $anyReceived = true;
                        }
                    }
                }

                $po->status = $allReceived
                    ? PurchaseOrderStatus::Received
                    : ($anyReceived ? PurchaseOrderStatus::PartiallyReceived : PurchaseOrderStatus::Confirmed);
                $po->save();
            }

            return $goodsReceipt;
        });

        return redirect()->route('admin.goods-receipts.show', $goodsReceipt)
            ->with('success', __('goods_receipts.messages.updated', [
                'default' => 'Goods receipt updated successfully. Line items, lots, and warehouse stock synchronized.',
            ]));
    }

    public function destroy(GoodsReceipt $goodsReceipt): RedirectResponse
    {
        $this->authorize('delete', $goodsReceipt);

        $existingInvoice = PurchaseInvoice::where('purchase_order_id', $goodsReceipt->purchase_order_id)
            ->where('reference_number', $goodsReceipt->grn_number)
            ->first();

        if ($existingInvoice) {
            return redirect()->route('admin.goods-receipts.show', $goodsReceipt)
                ->with('error', __('goods_receipts.messages.cannot_delete_billed', [
                    'default' => 'Cannot delete this receipt: Matched supplier bill ' . $existingInvoice->invoice_number . ' already exists in Finance.',
                ]));
        }

        DB::transaction(function () use ($goodsReceipt) {
            $po = $goodsReceipt->purchaseOrder()->with('items')->first();

            foreach ($goodsReceipt->items as $item) {
                $qty = (float) $item->received_qty;

                if ($item->item_type === 'product' || !empty($item->product_id)) {
                    $product = $item->product ?? Product::find($item->product_id);
                    $productBatch = ProductBatch::where('goods_receipt_id', $goodsReceipt->id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($productBatch) {
                        $productBatch->delete();
                    }

                    if ($product && $qty > 0) {
                        InventoryService::adjustStock(
                            $product,
                            $goodsReceipt->location_id,
                            -(int) round($qty),
                            'GRN Reversal',
                            "Deleted GRN {$goodsReceipt->grn_number}",
                            auth()->user()?->name,
                            $goodsReceipt->supplier?->name_en ?? 'Supplier',
                            false
                        );
                    }
                } else {
                    $rmBatch = RawMaterialBatch::where('goods_receipt_id', $goodsReceipt->id)
                        ->where('raw_material_id', $item->raw_material_id)
                        ->first();

                    if ($rmBatch) {
                        $rmBatch->delete();
                    }
                }

                if ($item->purchaseOrderItem && $qty > 0) {
                    $item->purchaseOrderItem->received_qty = max(0.0000, round((float) $item->purchaseOrderItem->received_qty - $qty, 4));
                    $item->purchaseOrderItem->save();
                }

                $item->delete();
            }

            if ($po) {
                $po->load('items');
                $allReceived = true;
                $anyReceived = false;
                foreach ($po->items as $pItem) {
                    if ((float) $pItem->received_qty >= (float) $pItem->ordered_qty) {
                        $anyReceived = true;
                    } else {
                        $allReceived = false;
                        if ((float) $pItem->received_qty > 0) {
                            $anyReceived = true;
                        }
                    }
                }

                $po->status = $allReceived
                    ? PurchaseOrderStatus::Received
                    : ($anyReceived ? PurchaseOrderStatus::PartiallyReceived : PurchaseOrderStatus::Confirmed);
                $po->save();
            }

            $goodsReceipt->delete();
        });

        return redirect()->route('admin.goods-receipts.index')
            ->with('success', __('goods_receipts.messages.deleted', [
                'default' => 'Goods receipt deleted successfully and inventory adjustments reversed.',
            ]));
    }

    public function generateBill(GoodsReceipt $goodsReceipt, FinanceService $financeService): RedirectResponse
    {
        $this->authorize('view', $goodsReceipt);
        $goodsReceipt->load([
            'purchaseOrder.items',
            'supplier',
            'items.product',
            'items.rawMaterial',
            'items.purchaseOrderItem',
        ]);

        // Check if an invoice already exists for this GRN reference
        $existingInvoice = PurchaseInvoice::where('purchase_order_id', $goodsReceipt->purchase_order_id)
            ->where('reference_number', $goodsReceipt->grn_number)
            ->first();

        if ($existingInvoice) {
            return redirect()->route('admin.finance.purchase-invoices.show', $existingInvoice)
                ->with('info', __('goods_receipts.messages.invoice_already_exists', [
                    'default' => 'Supplier bill already exists for this Goods Receipt (' . $goodsReceipt->grn_number . ').',
                ]));
        }

        if ($goodsReceipt->items->isEmpty()) {
            return redirect()->route('admin.goods-receipts.show', $goodsReceipt)
                ->with('error', __('goods_receipts.messages.no_items_to_bill', [
                    'default' => 'Cannot generate bill: This Goods Receipt has no received line items.',
                ]));
        }

        // Build invoice data from received items
        $items = [];
        $subtotal = 0.0;
        foreach ($goodsReceipt->items as $item) {
            $poItem = $item->purchaseOrderItem;
            $unitPrice = $poItem ? (float) $poItem->unit_price : 0.0;
            if ($unitPrice <= 0) {
                if ($item->item_type === 'product' && $item->product) {
                    $unitPrice = (float) ($item->product->cost_price ?? 0.0);
                } elseif ($item->rawMaterial) {
                    $unitPrice = (float) ($item->rawMaterial->standard_cost ?? $item->rawMaterial->unit_cost ?? 0.0);
                }
            }

            $qty = (float) $item->received_qty;
            $lineSub = round($qty * $unitPrice, 2);
            $subtotal += $lineSub;

            $itemType = $item->item_type ?? ($item->product_id ? 'product' : 'raw_material');
            $existingProductBatch = $itemType === 'product' && $item->product_id
                ? ProductBatch::where('goods_receipt_id', $goodsReceipt->id)
                    ->where('product_id', $item->product_id)
                    ->when($item->supplier_lot, fn ($q) => $q->where('supplier_lot', $item->supplier_lot))
                    ->first()
                : null;
            $existingRawBatch = $itemType === 'raw_material' && $item->raw_material_id
                ? \App\Models\RawMaterialBatch::where('goods_receipt_id', $goodsReceipt->id)
                    ->where('raw_material_id', $item->raw_material_id)
                    ->when($item->supplier_lot, fn ($q) => $q->where('supplier_lot', $item->supplier_lot))
                    ->first()
                : null;

            $resolvedExpiry = $item->expiry_date?->toDateString()
                ?? $existingProductBatch?->expiry_date?->toDateString()
                ?? $existingRawBatch?->expiry_date?->toDateString();

            $items[] = [
                'item_type' => $itemType,
                'product_id' => $item->product_id,
                'raw_material_id' => $item->raw_material_id,
                'product_batch_id' => $existingProductBatch?->id,
                'raw_material_batch_id' => $existingRawBatch?->id,
                'batch_number' => $item->supplier_lot ?: ($existingProductBatch?->batch_number ?? $existingRawBatch?->batch_number),
                'expiry_date' => $resolvedExpiry,
                'item_name' => $item->item_name . ($item->supplier_lot ? " (Lot: {$item->supplier_lot})" : ''),
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'tax_rate' => 5.0,
            ];
        }

        $taxAmount = round($subtotal * 0.05, 2);
        $grandTotal = $subtotal + $taxAmount;

        $supplierId = $goodsReceipt->supplier_id ?? $goodsReceipt->purchaseOrder?->supplier_id;

        $invoice = $financeService->createPurchaseInvoice([
            'supplier_id' => $supplierId,
            'purchase_order_id' => $goodsReceipt->purchase_order_id,
            'reference_number' => $goodsReceipt->grn_number,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'notes' => "Auto-generated 3-way match bill from GRN {$goodsReceipt->grn_number} (PO #{$goodsReceipt->purchaseOrder?->po_number})",
            'items' => $items,
        ], $goodsReceipt->purchaseOrder);

        return redirect()->route('admin.finance.purchase-invoices.show', $invoice)
            ->with('success', __('goods_receipts.messages.bill_generated', [
                'default' => 'Supplier Bill successfully created and matched against GRN ' . $goodsReceipt->grn_number,
            ]));
    }
}
