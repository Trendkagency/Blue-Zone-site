<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseOrderRequest;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Services\LandedCostService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $query = PurchaseOrder::with(['supplier', 'destinationLocation', 'creator'])
            ->withCount('items');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                    ->orWhere('supplier_reference', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name_en', 'like', "%{$search}%")->orWhere('name_ar', 'like', "%{$search}%");
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
        $orders = $query->orderBy('order_date', 'desc')->paginate($perPage)->withQueryString();

        $statuses = PurchaseOrderStatus::cases();
        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();

        $purchaseOrders = $orders;

        $kpis = [
            'total_orders' => $orders->total(),
            'pending_approval' => PurchaseOrder::where('status', PurchaseOrderStatus::Submitted)->count(),
            'awaiting_receipt' => PurchaseOrder::whereIn('status', [PurchaseOrderStatus::Confirmed, PurchaseOrderStatus::PartiallyReceived])->count(),
            'overdue' => PurchaseOrder::where('status', PurchaseOrderStatus::Confirmed)->where('expected_date', '<', now())->count(),
        ];

        return view('admin.supply_chain.purchase_orders.index', compact('orders', 'purchaseOrders', 'statuses', 'suppliers', 'kpis'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', PurchaseOrder::class);
        $suppliers = Supplier::where('is_active', true)->with('country')->orderBy('name_en')->get();
        $materials = RawMaterial::where('is_active', true)->orderBy('name_en')->get();
        $products = Product::where('status', 'active')->orWhereNull('status')->orderBy('name_en')->get(['id', 'sku', 'name_en', 'name_ar', 'price', 'cost_price']);
        $locations = Location::where('is_active', true)->get();
        $baseCurrency = \App\Services\CurrencyService::code();
        $supportedCurrencies = \App\Services\CurrencyService::supportedCurrencies();
        $currencies = array_keys($supportedCurrencies);

        $selectedQuotation = null;
        if ($request->filled('quotation_id')) {
            $selectedQuotation = \App\Models\SupplierQuotation::with(['supplier', 'items.rawMaterial', 'items.product'])->find($request->input('quotation_id'));
        }

        return view('admin.supply_chain.purchase_orders.create', compact(
            'suppliers', 'materials', 'products', 'locations', 'selectedQuotation', 
            'baseCurrency', 'currencies'
        ));
    }

    /**
     * AJAX endpoint: Retrieve the latest stock adjustment information for a specific item (product or raw material).
     */
    public function getItemAdjustment(Request $request): JsonResponse
    {
        $itemType = $request->input('item_type', 'product');
        $itemId = (int) $request->input('item_id');

        if (!$itemId) {
            return response()->json(['has_adjustment' => false]);
        }

        if ($itemType === 'product') {
            $product = Product::find($itemId);
            if (!$product) {
                return response()->json(['has_adjustment' => false]);
            }

            $movement = \App\Models\InventoryMovement::where(function ($q) use ($itemId, $product) {
                    $q->where('product_id', $itemId);
                    if (!empty($product->sku)) {
                        $q->orWhere('sku', $product->sku);
                    }
                })
                ->whereIn('movement_type', ['Manual Adjustment', 'Damaged', 'Expired', 'Return', 'Stock Out', 'Adjustment'])
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($movement) {
                $qty = abs((float) $movement->quantity);
                $unitCost = (float) ($product->cost_price ?? $product->price * 0.5 ?? 0);
                $val = round($qty * $unitCost, 2);
                $date = $movement->date ? (is_string($movement->date) ? $movement->date : $movement->date->format('Y-m-d')) : '';
                $num = 'MOV-' . str_pad((string)$movement->id, 5, '0', STR_PAD_LEFT);

                return response()->json([
                    'has_adjustment' => true,
                    'movement_id' => $movement->id,
                    'movement_number' => $num,
                    'movement_type' => $movement->movement_type,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'total_value' => $val,
                    'date' => $date,
                    'note' => $movement->note,
                    'current_stock' => (int) ($product->stock_online ?? 0),
                ]);
            }
        } else {
            $material = RawMaterial::find($itemId);
            if (!$material) {
                return response()->json(['has_adjustment' => false]);
            }

            $rawMovement = \App\Models\RawMaterialMovement::whereHas('batch', function ($q) use ($itemId) {
                    $q->where('raw_material_id', $itemId);
                })
                ->whereIn('movement_type', [\App\Enums\RawMaterialMovementType::Adjustment->value, \App\Enums\RawMaterialMovementType::Scrap->value])
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($rawMovement) {
                $qty = abs((float) $rawMovement->quantity);
                $unitCost = (float) ($material->standard_cost ?? 0);
                $val = round($qty * $unitCost, 2);
                $date = $rawMovement->created_at ? $rawMovement->created_at->format('Y-m-d') : '';
                $num = 'RMOV-' . str_pad((string)$rawMovement->id, 5, '0', STR_PAD_LEFT);
                $typeLabel = $rawMovement->movement_type instanceof \App\Enums\RawMaterialMovementType ? $rawMovement->movement_type->label(app()->getLocale()) : (string) $rawMovement->movement_type;

                return response()->json([
                    'has_adjustment' => true,
                    'movement_id' => $rawMovement->id,
                    'movement_number' => $num,
                    'movement_type' => $typeLabel,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'total_value' => $val,
                    'date' => $date,
                    'note' => $rawMovement->note ?? $rawMovement->notes,
                    'current_stock' => 0,
                ]);
            }
        }

        return response()->json(['has_adjustment' => false]);
    }

    /**
     * AJAX endpoint: Calculate automated landed costs based on selected or latest transaction
     * minus selected or latest stock adjustments, and apportion using IAS 2 value-based allocation.
     */
    public function calculateLandedCosts(Request $request): JsonResponse
    {
        $supplierId = $request->input('supplier_id') ? (int) $request->input('supplier_id') : null;
        $items = $request->input('items', []);
        $transactionId = $request->input('transaction_id') ? (int) $request->input('transaction_id') : null;
        $adjustmentId = $request->input('adjustment_id') ? (string) $request->input('adjustment_id') : null;

        $result = LandedCostService::getInstance()->calculateLandedCost($supplierId, $items, $transactionId, $adjustmentId);

        return response()->json($result);
    }

    public function store(PurchaseOrderRequest $request): RedirectResponse
    {
        $this->authorize('create', PurchaseOrder::class);

        $order = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $itemsData = $data['items'];
            unset($data['items']);

            $data['po_number'] = PurchaseOrder::generatePoNumber();
            $data['status'] = PurchaseOrderStatus::Draft;
            $data['created_by'] = auth()->id();

            // Calculate item subtotals
            $subtotal = 0.0;
            $itemsToCreate = [];
            foreach ($itemsData as $item) {
                $lineTotal = round((float) $item['ordered_qty'] * (float) $item['unit_price'], 2);
                $subtotal += $lineTotal;

                $isProduct = ($item['item_type'] ?? '') === 'product' || !empty($item['product_id']);

                $adjCost = (float) ($item['adjustment_cost'] ?? 0.0);

                $itemsToCreate[] = [
                    'item_type' => $isProduct ? 'product' : 'raw_material',
                    'product_id' => $isProduct ? $item['product_id'] : null,
                    'raw_material_id' => !$isProduct ? ($item['raw_material_id'] ?? null) : null,
                    'ordered_qty' => $item['ordered_qty'],
                    'received_qty' => 0.0000,
                    'unit_price' => $item['unit_price'],
                    'total_price' => $lineTotal,
                    'allocated_landed_cost' => (float) ($item['allocated_landed_cost'] ?? 0.0),
                    'adjustment_cost' => $adjCost,
                    'landed_unit_cost' => (float) ($item['landed_unit_cost'] ?? $item['unit_price']),
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $data['subtotal'] = $subtotal;
            $freight = (float) ($data['freight_cost'] ?? 0);
            $customs = (float) ($data['customs_duties'] ?? 0);
            $tax = (float) ($data['tax_amount'] ?? 0);
            $data['grand_total'] = round($subtotal + $freight + $customs + $tax, 2);

            // Ensure IAS 2 Value-Based Landed Cost Allocation across items
            $totalLandedCost = $freight + $customs;
            if ($totalLandedCost > 0 && $subtotal > 0) {
                $allocatedSum = 0.0;
                $largestIdx = 0;
                $maxLine = -1.0;
                foreach ($itemsToCreate as $idx => &$itemRef) {
                    $weight = $itemRef['total_price'] / $subtotal;
                    $itemAlloc = round($totalLandedCost * $weight, 2);
                    $itemRef['allocated_landed_cost'] = $itemAlloc;
                    $allocatedSum += $itemAlloc;
                    if ($itemRef['total_price'] > $maxLine) {
                        $maxLine = $itemRef['total_price'];
                        $largestIdx = $idx;
                    }
                }
                unset($itemRef);

                // Rounding penny gap reconciliation
                $roundingGap = round($totalLandedCost - $allocatedSum, 2);
                if ($roundingGap != 0.0) {
                    $itemsToCreate[$largestIdx]['allocated_landed_cost'] = round($itemsToCreate[$largestIdx]['allocated_landed_cost'] + $roundingGap, 2);
                }
            }

            // Always calculate final landed unit cost (base price + allocated freight/customs + absorbed adjustment)
            foreach ($itemsToCreate as &$itemRef) {
                $qty = (float) $itemRef['ordered_qty'];
                $allocLanded = (float) ($itemRef['allocated_landed_cost'] ?? 0.0);
                $adj = (float) ($itemRef['adjustment_cost'] ?? 0.0);
                $itemRef['landed_unit_cost'] = $qty > 0
                    ? round((float) $itemRef['unit_price'] + (($allocLanded + $adj) / $qty), 4)
                    : (float) $itemRef['unit_price'];
            }
            unset($itemRef);

            $order = PurchaseOrder::create($data);
            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            return $order;
        });

        return redirect()->route('admin.purchase-orders.show', $order)
            ->with('success', __('purchase_orders.messages.created', ['default' => 'Purchase order drafted successfully.']));
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('view', $purchaseOrder);
        $purchaseOrder->load([
            'supplier',
            'supplierQuotation',
            'destinationLocation',
            'creator',
            'approver',
            'items.rawMaterial',
            'items.product',
            'goodsReceipts.receiver',
            'goodsReceipts.items.rawMaterial',
            'goodsReceipts.items.product',
            'purchaseInvoices',
        ]);

        return view('admin.supply_chain.purchase_orders.show', compact('purchaseOrder'));
    }

    public function submit(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);
        $purchaseOrder->status->validateTransitionTo(PurchaseOrderStatus::Submitted);
        $purchaseOrder->status = PurchaseOrderStatus::Submitted;
        $purchaseOrder->save();

        return back()->with('success', __('purchase_orders.messages.submitted', ['default' => 'Purchase order submitted for approval.']));
    }

    public function approve(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('approve', $purchaseOrder);
        $user = auth()->user();

        if (! $purchaseOrder->canBeApprovedBy($user)) {
            throw new DomainException(__('purchase_orders.errors.creator_approval_forbidden', [
                'default' => 'Separation of duties: The creator of a purchase order cannot approve orders valued above AED 25,000.',
            ]));
        }

        $purchaseOrder->status->validateTransitionTo(PurchaseOrderStatus::Confirmed);
        $purchaseOrder->status = PurchaseOrderStatus::Confirmed;
        $purchaseOrder->approved_by = $user->id;
        $purchaseOrder->approved_at = now();
        $purchaseOrder->save();

        return back()->with('success', __('purchase_orders.messages.approved', ['default' => 'Purchase order approved and confirmed with supplier.']));
    }

    public function cancel(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);
        $purchaseOrder->status->validateTransitionTo(PurchaseOrderStatus::Cancelled);
        $purchaseOrder->status = PurchaseOrderStatus::Cancelled;
        $purchaseOrder->save();

        return back()->with('success', __('purchase_orders.messages.cancelled', ['default' => 'Purchase order cancelled.']));
    }

    public function print(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('view', $purchaseOrder);
        $purchaseOrder->load(['supplier.country', 'destinationLocation', 'creator', 'approver', 'items.rawMaterial', 'items.product']);

        return view('admin.supply_chain.purchase_orders.print', compact('purchaseOrder'));
    }
}
