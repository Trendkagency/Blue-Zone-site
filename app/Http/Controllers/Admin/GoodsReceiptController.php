<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GoodsReceiptStatus;
use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
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

        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $receipts = $query->orderBy('received_date', 'desc')->paginate($perPage)->withQueryString();
        $goodsReceipts = $receipts;

        $kpis = [
            'total_grns' => $receipts->total(),
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
        if ($poId = $request->query('po_id')) {
            $selectedPo = PurchaseOrder::with(['items.rawMaterial', 'items.product', 'supplier', 'destinationLocation'])
                ->whereIn('status', [PurchaseOrderStatus::Confirmed, PurchaseOrderStatus::PartiallyReceived])
                ->find($poId);
        }

        $openOrders = PurchaseOrder::with('supplier')
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
                        InventoryService::adjustStock(
                            $product,
                            $data['location_id'],
                            $qty,
                            'Purchase Receipt',
                            "GRN: {$grn->grn_number} (PO: {$po->po_number}) | Lot: {$itemInput['supplier_lot']}",
                            auth()->user()?->name,
                            $po->supplier?->name_en ?? 'Supplier'
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
                $poItem = $po->items->firstWhere('id', $itemInput['purchase_order_item_id']);
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

        return view('admin.supply_chain.goods_receipts.show', compact('goodsReceipt'));
    }

    public function generateBill(GoodsReceipt $goodsReceipt, FinanceService $financeService): RedirectResponse
    {
        $this->authorize('view', $goodsReceipt);
        $goodsReceipt->load(['purchaseOrder.items', 'supplier', 'items.product', 'items.rawMaterial']);

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

        // Build invoice data from received items
        $items = [];
        $subtotal = 0.0;
        foreach ($goodsReceipt->items as $item) {
            $poItem = $item->purchaseOrderItem;
            $unitPrice = $poItem ? (float) $poItem->unit_price : 0.0;
            $qty = (float) $item->received_qty;
            $lineSub = round($qty * $unitPrice, 2);
            $subtotal += $lineSub;

            $items[] = [
                'raw_material_id' => $item->raw_material_id,
                'item_name' => $item->item_name . ($item->supplier_lot ? " (Lot: {$item->supplier_lot})" : ''),
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'tax_rate' => 5.0,
            ];
        }

        $taxAmount = round($subtotal * 0.05, 2);
        $grandTotal = $subtotal + $taxAmount;

        $invoice = $financeService->createPurchaseInvoice([
            'supplier_id' => $goodsReceipt->supplier_id,
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
