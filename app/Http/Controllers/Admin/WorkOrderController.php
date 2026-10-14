<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DispenseMaterialRequest;
use App\Http\Requests\Admin\WorkOrderRequest;
use App\Models\Location;
use App\Models\Product;
use App\Models\RawMaterialBatch;
use App\Models\WorkOrder;
use App\Services\RawMaterial\RawMaterialManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
                        $pq->where('name_en', 'like', "%{$search}%")->orWhere('name_ar', 'like', "%{$search}%");
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
            'total' => $orders->total(),
            'in_production' => WorkOrder::whereIn('status', [WorkOrderStatus::Dispensing, WorkOrderStatus::InProduction])->count(),
            'completed' => WorkOrder::where('status', WorkOrderStatus::Completed)->count(),
            'scheduled' => WorkOrder::where('status', WorkOrderStatus::Scheduled)->count(),
        ];

        $statuses = WorkOrderStatus::cases();

        return view('admin.supply_chain.work_orders.index', compact('orders', 'workOrders', 'statuses', 'kpis'));
    }

    public function create(): View
    {
        $this->authorize('create', WorkOrder::class);

        $products = Product::where('status', 'active')
            ->whereHas('recipeItems')
            ->orderBy('name_en')
            ->get();

        $locations = Location::where('is_active', true)->get();

        return view('admin.supply_chain.work_orders.create', compact('products', 'locations'));
    }

    public function store(WorkOrderRequest $request): RedirectResponse
    {
        $this->authorize('create', WorkOrder::class);

        $data = $request->validated();
        $data['order_number'] = WorkOrder::generateOrderNumber();
        $data['target_finished_lot'] = WorkOrder::generateFinishedLot();
        $data['status'] = WorkOrderStatus::Draft;
        $data['created_by'] = auth()->id();

        $order = WorkOrder::create($data);

        return redirect()->route('admin.work-orders.show', $order)
            ->with('success', __('production.messages.created', ['default' => 'Production Work Order drafted successfully.']));
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

        return view('admin.supply_chain.work_orders.show', compact('workOrder'));
    }

    public function schedule(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('update', $workOrder);

        $workOrder->status->validateTransitionTo(WorkOrderStatus::Scheduled);

        // FEFO reserve ingredients
        $reservations = RawMaterialManager::getInstance()->reserveForWorkOrder($workOrder);

        $workOrder->status = WorkOrderStatus::Scheduled;
        $workOrder->save();

        return back()->with('success', __('production.messages.scheduled', [
            'default' => 'Work order scheduled. FEFO batches successfully reserved for compounding.',
        ]));
    }

    public function startDispensing(WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('dispense', $workOrder);

        $workOrder->status->validateTransitionTo(WorkOrderStatus::Dispensing);
        $workOrder->status = WorkOrderStatus::Dispensing;
        $workOrder->started_at = now();
        $workOrder->save();

        return back()->with('success', __('production.messages.dispensing_started', [
            'default' => 'Clean-room dispensing initiated.',
        ]));
    }

    public function dispense(DispenseMaterialRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('dispense', $workOrder);

        $batch = RawMaterialBatch::findOrFail($request->input('raw_material_batch_id'));
        $qty = (float) $request->input('quantity');
        $barcode = $request->input('scanned_barcode');

        RawMaterialManager::getInstance()->dispenseMaterial(
            $workOrder,
            $batch,
            $qty,
            auth()->user(),
            $barcode
        );

        return back()->with('success', __('production.messages.material_dispensed', [
            'default' => 'Material successfully dispensed and deducted from lot inventory.',
        ]));
    }

    public function complete(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->authorize('complete', $workOrder);

        $validated = $request->validate([
            'produced_qty' => ['required', 'integer', 'min:1'],
            'rejected_qty' => ['nullable', 'integer', 'min:0'],
        ]);

        $workOrder->status->validateTransitionTo(WorkOrderStatus::Completed);
        $workOrder->produced_qty = (int) $validated['produced_qty'];
        $workOrder->rejected_qty = (int) ($validated['rejected_qty'] ?? 0);
        $workOrder->status = WorkOrderStatus::Completed;
        $workOrder->save(); // Triggers WorkOrderObserver to credit finished product inventory!

        return back()->with('success', __('production.messages.completed', [
            'default' => 'Work Order completed. Finished goods stock auto-credited to warehouse inventory and unit cost calculated.',
        ]));
    }
}
