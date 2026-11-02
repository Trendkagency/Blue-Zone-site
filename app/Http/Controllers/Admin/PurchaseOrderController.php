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
use DomainException;
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

    public function create(): View
    {
        $this->authorize('create', PurchaseOrder::class);
        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();
        $materials = RawMaterial::where('is_active', true)->orderBy('name_en')->get();
        $products = Product::where('status', 'active')->orWhereNull('status')->orderBy('name_en')->get(['id', 'sku', 'name_en', 'name_ar', 'price', 'cost_price']);
        $locations = Location::where('is_active', true)->get();

        return view('admin.supply_chain.purchase_orders.create', compact('suppliers', 'materials', 'products', 'locations'));
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

                $itemsToCreate[] = [
                    'item_type' => $isProduct ? 'product' : 'raw_material',
                    'product_id' => $isProduct ? $item['product_id'] : null,
                    'raw_material_id' => !$isProduct ? ($item['raw_material_id'] ?? null) : null,
                    'ordered_qty' => $item['ordered_qty'],
                    'received_qty' => 0.0000,
                    'unit_price' => $item['unit_price'],
                    'total_price' => $lineTotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $data['subtotal'] = $subtotal;
            $freight = (float) ($data['freight_cost'] ?? 0);
            $customs = (float) ($data['customs_duties'] ?? 0);
            $tax = (float) ($data['tax_amount'] ?? 0);
            $data['grand_total'] = round($subtotal + $freight + $customs + $tax, 2);

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
            'destinationLocation',
            'creator',
            'approver',
            'items.rawMaterial',
            'items.product',
            'goodsReceipts.receiver',
            'goodsReceipts.items.rawMaterial',
            'goodsReceipts.items.product',
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
