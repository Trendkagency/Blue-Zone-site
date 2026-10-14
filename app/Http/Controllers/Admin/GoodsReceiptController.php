<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GoodsReceiptStatus;
use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
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
            $selectedPo = PurchaseOrder::with(['items.rawMaterial', 'supplier', 'destinationLocation'])
                ->whereIn('status', [PurchaseOrderStatus::Confirmed, PurchaseOrderStatus::PartiallyReceived])
                ->find($poId);
        }

        $openOrders = PurchaseOrder::with('supplier')
            ->whereIn('status', [PurchaseOrderStatus::Confirmed, PurchaseOrderStatus::PartiallyReceived])
            ->orderBy('po_number', 'desc')
            ->get();

        $locations = Location::where('is_active', true)->get();

        return view('admin.supply_chain.goods_receipts.create', compact('selectedPo', 'openOrders', 'locations'));
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
                $grnItem = $grn->items()->create([
                    'purchase_order_item_id' => $itemInput['purchase_order_item_id'],
                    'raw_material_id' => $itemInput['raw_material_id'],
                    'ordered_qty' => $itemInput['received_qty'],
                    'received_qty' => $itemInput['received_qty'],
                    'rejected_qty' => $itemInput['rejected_qty'] ?? 0.0000,
                    'supplier_lot' => $itemInput['supplier_lot'],
                    'manufacture_date' => $itemInput['manufacture_date'],
                    'expiry_date' => $itemInput['expiry_date'],
                    'storage_condition' => $itemInput['storage_condition'],
                    'notes' => $itemInput['notes'] ?? null,
                ]);

                // Create quarantine batch via RawMaterialManager
                $rawManager->receiveBatch(
                    $grnItem,
                    auth()->user(),
                    $data['location_id'],
                    $itemInput['storage_bin'] ?? null
                );

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
                'default' => 'Goods receipt created successfully. Received items have been automatically tagged and placed into laboratory quarantine.',
            ]));
    }

    public function show(GoodsReceipt $goodsReceipt): View
    {
        $this->authorize('view', $goodsReceipt);
        $goodsReceipt->load(['purchaseOrder', 'supplier', 'location', 'receiver', 'items.rawMaterial', 'batches.inspections']);

        return view('admin.supply_chain.goods_receipts.show', compact('goodsReceipt'));
    }
}
