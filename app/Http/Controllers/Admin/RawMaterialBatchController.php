<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RawMaterialBatchStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BatchAdjustmentRequest;
use App\Http\Requests\Admin\BatchTransferRequest;
use App\Models\Location;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\User;
use App\Services\RawMaterial\RawMaterialManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RawMaterialBatchController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', RawMaterialBatch::class);

        $query = RawMaterialBatch::with(['rawMaterial', 'supplier', 'location']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                    ->orWhere('supplier_lot', 'like', "%{$search}%")
                    ->orWhereHas('rawMaterial', function ($mq) use ($search) {
                        $mq->where('name_en', 'like', "%{$search}%")->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($materialId = $request->query('raw_material_id')) {
            $query->where('raw_material_id', $materialId);
        }

        if ($locationId = $request->query('location_id')) {
            $query->where('location_id', $locationId);
        }

        // FEFO Ordering default
        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $batches = $query->orderBy('expiry_date', 'asc')->orderBy('id', 'asc')->paginate($perPage)->withQueryString();

        $statuses = RawMaterialBatchStatus::cases();
        $materials = RawMaterial::where('is_active', true)->orderBy('name_en')->get();
        $locations = Location::where('is_active', true)->get();

        $kpis = [
            'total_batches' => $batches->total(),
            'approved' => RawMaterialBatch::where('status', RawMaterialBatchStatus::Approved)->count(),
            'quarantine' => RawMaterialBatch::where('status', RawMaterialBatchStatus::Quarantine)->count(),
            'expiring_soon' => RawMaterialBatch::where('status', RawMaterialBatchStatus::Approved)
                ->where('expiry_date', '<=', now()->addDays(60))
                ->count(),
        ];

        return view('admin.supply_chain.batches.index', compact('batches', 'statuses', 'materials', 'locations', 'kpis'));
    }

    public function show(RawMaterialBatch $batch): View
    {
        $this->authorize('view', $batch);
        $batch->load([
            'rawMaterial',
            'supplier',
            'location',
            'purchaseOrder',
            'goodsReceipt',
            'inspections.inspector',
            'inspections.verifier',
            'movements.user',
            'consumptions.workOrder',
            'consumptions.operator',
        ]);

        $locations = Location::where('is_active', true)->get();
        $managers = User::where('id', '!=', auth()->id())->get();

        return view('admin.supply_chain.batches.show', compact('batch', 'locations', 'managers'));
    }

    public function transfer(BatchTransferRequest $request, RawMaterialBatch $batch): RedirectResponse
    {
        $this->authorize('transfer', $batch);

        RawMaterialManager::getInstance()->transferBatch(
            $batch,
            $request->input('to_location_id'),
            $request->input('to_storage_bin'),
            auth()->user(),
            $request->input('note')
        );

        return back()->with('success', __('batches.messages.transferred', [
            'default' => 'Batch successfully transferred to new location / storage bin.',
        ]));
    }

    public function adjust(BatchAdjustmentRequest $request, RawMaterialBatch $batch): RedirectResponse
    {
        $this->authorize('adjust', $batch);

        $secondApprover = null;
        if ($approverId = $request->input('second_approver_id')) {
            $secondApprover = User::findOrFail($approverId);
        }

        RawMaterialManager::getInstance()->adjustStock(
            $batch,
            (float) $request->input('new_quantity'),
            $request->input('reason'),
            auth()->user(),
            $secondApprover
        );

        return back()->with('success', __('batches.messages.adjusted', [
            'default' => 'Batch stock level adjusted and recorded into the audit ledger.',
        ]));
    }
}
