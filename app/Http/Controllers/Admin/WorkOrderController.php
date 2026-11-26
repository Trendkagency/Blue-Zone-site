<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RawMaterialBatchStatus;
use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DispenseMaterialRequest;
use App\Http\Requests\Admin\WorkOrderRequest;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\RawMaterialBatch;
use App\Models\WorkOrder;
use App\Services\RawMaterial\RawMaterialManager;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', WorkOrder::class);

        $query = WorkOrder::with(['product', 'location', 'creator'])
            ->withCount('consumptions');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('target_finished_lot', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $orders = $query->orderBy('scheduled_start', 'desc')->paginate($perPage)->withQueryString();
        $workOrders = $orders;

        $kpis = [
            'total' => WorkOrder::count(),
            'in_production' => WorkOrder::whereIn('status', [
                WorkOrderStatus::Dispensing,
                WorkOrderStatus::InProduction,
                WorkOrderStatus::QcReview,
            ])->count(),
            'completed' => WorkOrder::where('status', WorkOrderStatus::Completed)->count(),
            'scheduled' => WorkOrder::whereIn('status', [
                WorkOrderStatus::Scheduled,
                WorkOrderStatus::Draft,
            ])->count(),
        ];

        $statuses = WorkOrderStatus::cases();

        return view('admin.supply_chain.work_orders.index', compact('orders', 'workOrders', 'statuses', 'kpis'));
    }

    public function create(): View
    {
        $this->authorize('create', WorkOrder::class);

        // Eager load recipe items to prevent lazy loading violations
        $products = Product::where('status', 'active')
            ->with(['recipeItems.rawMaterial'])
            ->withCount('recipeItems')
            ->orderBy('name_en')
            ->get();

        if ($products->isEmpty()) {
            $products = Product::with(['recipeItems.rawMaterial'])
                ->withCount('recipeItems')
                ->orderBy('name_en')
                ->get();
        }

        $locations = Location::where('is_active', true)->get();
        if ($locations->isEmpty()) {
            $locations = collect([
                (object) [
                    'id' => 'central_wh',
                    'name' => 'Central Quarantine Warehouse',
                    'name_en' => 'Central Quarantine Warehouse',
                    'name_ar' => 'المستودع المركزي الرئيسي',
                ],
            ]);
        }

        $suggestedLot = WorkOrder::generateFinishedLot();
        $recentExpenses = \App\Models\Expense::with('category')->latest('id')->limit(20)->get();

        return view('admin.supply_chain.work_orders.create', compact('products', 'locations', 'suggestedLot', 'recentExpenses'));
    }

    public function store(WorkOrderRequest $request): RedirectResponse
    {
        $this->authorize('create', WorkOrder::class);

        $data = $request->validated();
        $data['order_number'] = WorkOrder::generateOrderNumber();

        if (empty($data['target_finished_lot'])) {
            $data['target_finished_lot'] = WorkOrder::generateFinishedLot();
        }

        $data['status'] = WorkOrderStatus::Draft;
        $data['created_by'] = auth()->id();

        // Process Landed Costs
        if (!empty($data['landed_costs']) && is_array($data['landed_costs'])) {
            $cleanLanded = [];
            $totalLanded = 0.0;
            foreach ($data['landed_costs'] as $lc) {
                $amount = (float) ($lc['amount'] ?? 0);
                if ($amount > 0) {
                    $cleanLanded[] = [
                        'category' => $lc['category'] ?? 'General Overhead',
                        'amount' => $amount,
                        'note' => $lc['note'] ?? null,
                        'expense_id' => $lc['expense_id'] ?? null,
                    ];
                    $totalLanded += $amount;
                }
            }
            $data['landed_costs'] = $cleanLanded;
            $data['total_landed_cost'] = $totalLanded;
        }

        $autoReserve = $request->boolean('auto_reserve', true);

        /** @var WorkOrder $order */
        $order = WorkOrder::create($data);

        // If auto-reserve is requested and product has formulation BOM
        if ($autoReserve && $order->product && $order->product->recipeItems()->exists()) {
            try {
                $order->status->validateTransitionTo(WorkOrderStatus::Scheduled);
                RawMaterialManager::getInstance()->reserveForWorkOrder($order);
                $order->status = WorkOrderStatus::Scheduled;
                $order->save();

                return redirect()->route('admin.work-orders.show', $order)
                    ->with('success', __('production.messages.scheduled'));
            } catch (DomainException $e) {
                return redirect()->route('admin.work-orders.show', $order)
                    ->with('warning', $e->getMessage() . ' (' . __('production.messages.saved_as_draft') . ')');
            }
        }

        return redirect()->route('admin.work-orders.show', $order)
            ->with('success', __('production.messages.created'));
    }

    public function show(WorkOrder $workOrder): View
    {
        $this->authorize('view', $workOrder);

        $workOrder->load([
            'product.recipeItems.rawMaterial',
            'location',
            'creator',
            'consumptions.batch.rawMaterial',
            'consumptions.operator',
        ]);

        $rawMaterialIds = $workOrder->product?->recipeItems->pluck('raw_material_id')->filter()->all() ?? [];

        // Batches available or currently held for dispensing
        $batchesQuery = RawMaterialBatch::with(['rawMaterial'])
            ->where('status', RawMaterialBatchStatus::Approved)
            ->where('current_qty', '>', 0)
            ->where('expiry_date', '>', now()->toDateString())
            ->orderBy('expiry_date', 'asc');

        if (! empty($rawMaterialIds)) {
            $batches = (clone $batchesQuery)->whereIn('raw_material_id', $rawMaterialIds)->get();
            if ($batches->isEmpty()) {
                $batches = $batchesQuery->get();
            }
        } else {
            $batches = $batchesQuery->get();
        }

        $finishedBatches = ProductBatch::where('work_order_id', $workOrder->id)->get();

        return view('admin.supply_chain.work_orders.show', compact('workOrder', 'batches', 'finishedBatches'));
    }

    public function schedule(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('update', $workOrder);

        try {
            $workOrder->status->validateTransitionTo(WorkOrderStatus::Scheduled);
            RawMaterialManager::getInstance()->reserveForWorkOrder($workOrder);

            $workOrder->status = WorkOrderStatus::Scheduled;
            $workOrder->save();

            return back()->with('success', __('production.messages.scheduled'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function startDispensing(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('dispense', $workOrder);

        try {
            // If still Draft, try scheduling first
            if ($workOrder->status === WorkOrderStatus::Draft) {
                RawMaterialManager::getInstance()->reserveForWorkOrder($workOrder);
                $workOrder->status = WorkOrderStatus::Scheduled;
                $workOrder->save();
            }

            $workOrder->status->validateTransitionTo(WorkOrderStatus::Dispensing);
            $workOrder->status = WorkOrderStatus::Dispensing;
            if (empty($workOrder->started_at)) {
                $workOrder->started_at = now();
            }
            $workOrder->save();

            return back()->with('success', __('production.messages.dispensing_started'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function startProduction(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('update', $workOrder);

        try {
            $workOrder->status->validateTransitionTo(WorkOrderStatus::InProduction);
            $workOrder->status = WorkOrderStatus::InProduction;
            if (empty($workOrder->started_at)) {
                $workOrder->started_at = now();
            }
            $workOrder->save();

            return back()->with('success', __('production.messages.in_production'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function submitToQcReview(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('update', $workOrder);

        try {
            $workOrder->status->validateTransitionTo(WorkOrderStatus::QcReview);
            $workOrder->status = WorkOrderStatus::QcReview;
            $workOrder->save();

            return back()->with('success', __('production.messages.qc_review'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function dispense(DispenseMaterialRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('dispense', $workOrder);

        $batch = RawMaterialBatch::findOrFail($request->input('raw_material_batch_id'));
        $qty = (float) $request->input('quantity');
        $barcode = $request->input('scanned_barcode');

        try {
            RawMaterialManager::getInstance()->dispenseMaterial(
                $workOrder,
                $batch,
                $qty,
                auth()->user(),
                $barcode
            );

            return back()->with('success', __('production.messages.material_dispensed'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('complete', $workOrder);

        // Support both field naming conventions
        if ($request->has('actual_quantity') && ! $request->has('produced_qty')) {
            $request->merge(['produced_qty' => $request->input('actual_quantity')]);
        }
        if ($request->has('scrap_quantity') && ! $request->has('rejected_qty')) {
            $request->merge(['rejected_qty' => $request->input('scrap_quantity')]);
        }

        $validated = $request->validate([
            'produced_qty' => ['required', 'integer', 'min:1'],
            'rejected_qty' => ['nullable', 'integer', 'min:0'],
            'manufacture_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after:manufacture_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'landed_costs' => ['nullable', 'array'],
            'landed_costs.*.category' => ['nullable', 'string'],
            'landed_costs.*.amount' => ['nullable', 'numeric', 'min:0'],
            'landed_costs.*.note' => ['nullable', 'string'],
            'landed_costs.*.expense_id' => ['nullable'],
        ]);

        try {
            $workOrder->status->validateTransitionTo(WorkOrderStatus::Completed);

            $workOrder->produced_qty = (int) $validated['produced_qty'];
            $workOrder->rejected_qty = (int) ($validated['rejected_qty'] ?? 0);
            if (! empty($validated['manufacture_date'])) {
                $workOrder->setAttribute('manufacture_date', $validated['manufacture_date']);
            }
            if (! empty($validated['expiry_date'])) {
                $workOrder->setAttribute('expiry_date', $validated['expiry_date']);
            }
            if (! empty($validated['notes'])) {
                $workOrder->notes = trim(($workOrder->notes ? $workOrder->notes . "\n" : '') . $validated['notes']);
            }

            if (!empty($validated['landed_costs']) && is_array($validated['landed_costs'])) {
                $cleanLanded = [];
                $totalLanded = 0.0;
                foreach ($validated['landed_costs'] as $lc) {
                    $amount = (float) ($lc['amount'] ?? 0);
                    if ($amount > 0) {
                        $cleanLanded[] = [
                            'category' => $lc['category'] ?? 'General Overhead',
                            'amount' => $amount,
                            'note' => $lc['note'] ?? null,
                            'expense_id' => $lc['expense_id'] ?? null,
                        ];
                        $totalLanded += $amount;
                    }
                }
                $workOrder->landed_costs = $cleanLanded;
                $workOrder->total_landed_cost = $totalLanded;
            }
            $workOrder->status = WorkOrderStatus::Completed;
            $workOrder->save(); // Triggers WorkOrderObserver to credit finished product inventory and calculate landed cost!

            return back()->with('success', __('production.messages.completed'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('cancel', $workOrder);

        try {
            $workOrder->status->validateTransitionTo(WorkOrderStatus::Cancelled);

            // Return any unconsumed reserved quantities back to available_qty
            if (in_array($workOrder->status, [WorkOrderStatus::Scheduled, WorkOrderStatus::Dispensing])) {
                DB::transaction(function () use ($workOrder) {
                    $recipeItems = $workOrder->product?->recipeItems;
                    if ($recipeItems) {
                        foreach ($recipeItems as $recipeItem) {
                            $batches = RawMaterialBatch::where('raw_material_id', $recipeItem->raw_material_id)
                                ->where('reserved_qty', '>', 0)
                                ->lockForUpdate()
                                ->get();

                            foreach ($batches as $b) {
                                // Return reserved to available
                                $b->available_qty = round((float) $b->available_qty + (float) $b->reserved_qty, 4);
                                $b->reserved_qty = 0.0000;
                                $b->save();
                                $b->assertInvariant();
                            }
                        }
                    }
                });
            }

            $workOrder->status = WorkOrderStatus::Cancelled;
            $workOrder->save();

            return back()->with('success', __('production.messages.cancelled'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
