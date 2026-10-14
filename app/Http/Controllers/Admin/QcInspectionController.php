<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QcVerdict;
use App\Enums\RawMaterialBatchStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QcInspectionRequest;
use App\Models\QcInspection;
use App\Models\RawMaterialBatch;
use App\Services\RawMaterial\RawMaterialManager;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QcInspectionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', QcInspection::class);

        $query = QcInspection::with(['batch.rawMaterial', 'batch.supplier', 'inspector', 'verifier'])
            ->withCount('parameters');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('inspection_code', 'like', "%{$search}%")
                    ->orWhereHas('batch', function ($bq) use ($search) {
                        $bq->where('batch_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($verdict = $request->query('verdict')) {
            $query->where('verdict', $verdict);
        }

        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $inspections = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $verdicts = QcVerdict::cases();

        $kpis = [
            'total_inspections' => $inspections->total(),
            'passed' => QcInspection::where('verdict', QcVerdict::Passed)->count(),
            'pending' => QcInspection::whereNull('verified_at')->count(),
            'failed' => QcInspection::where('verdict', QcVerdict::Failed)->count(),
        ];

        return view('admin.supply_chain.qc.index', compact('inspections', 'verdicts', 'kpis'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', QcInspection::class);

        $selectedBatch = null;
        if ($batchId = $request->query('batch_id')) {
            $selectedBatch = RawMaterialBatch::with(['rawMaterial', 'supplier'])->find($batchId);
        }

        $pendingBatches = RawMaterialBatch::with(['rawMaterial', 'supplier'])
            ->whereIn('status', [RawMaterialBatchStatus::Quarantine, RawMaterialBatchStatus::RetestRequired])
            ->orderBy('created_at', 'desc')
            ->get();

        $verdicts = QcVerdict::cases();

        return view('admin.supply_chain.qc.create', compact('selectedBatch', 'pendingBatches', 'verdicts'));
    }

    public function store(QcInspectionRequest $request): RedirectResponse
    {
        $this->authorize('create', QcInspection::class);

        $data = $request->validated();
        $paramsData = $data['parameters'];
        unset($data['parameters']);

        $data['inspector_id'] = auth()->id();
        $data['tested_at'] = now();
        $data['inspection_code'] = QcInspection::generateInspectionCode();

        $inspection = QcInspection::create($data);

        foreach ($paramsData as $param) {
            $inspection->parameters()->create($param);
        }

        if ($request->hasFile('coa_file')) {
            $inspection->addMediaFromRequest('coa_file')->toMediaCollection('coa_documents');
        }

        return redirect()->route('admin.qc-inspections.show', $inspection)
            ->with('success', __('qc.messages.created', [
                'default' => 'QC Inspection logged successfully. Awaiting second-tier verification.',
            ]));
    }

    public function show(QcInspection $qcInspection): View
    {
        $this->authorize('view', $qcInspection);
        $qcInspection->load(['batch.rawMaterial', 'batch.supplier', 'inspector', 'verifier', 'parameters']);

        return view('admin.supply_chain.qc.show', compact('qcInspection'));
    }

    /**
     * Dual-signoff verification action: Must be performed by a different user than the inspector.
     */
    public function verify(Request $request, QcInspection $qcInspection): RedirectResponse
    {
        $this->authorize('verify', $qcInspection);
        $user = auth()->user();

        if ($qcInspection->inspector_id === $user->id) {
            throw new DomainException(__('qc.errors.self_verification_prohibited', [
                'default' => 'Separation of duties violation: An inspector cannot verify their own inspection.',
            ]));
        }

        $rawManager = RawMaterialManager::getInstance();
        $batch = $qcInspection->batch;

        if (in_array($qcInspection->verdict, [QcVerdict::Passed, QcVerdict::ConditionalPass], true)) {
            $rawManager->releaseQuarantine($batch, $qcInspection, $user);
            $msg = __('qc.messages.approved', ['default' => 'Inspection verified and batch successfully released from quarantine into approved stock.']);
        } else {
            $rawManager->rejectBatch($batch, $qcInspection, $user, $qcInspection->notes ?? 'Quality parameters failed.');
            $msg = __('qc.messages.rejected', ['default' => 'Inspection verified and batch permanently locked as REJECTED.']);
        }

        return back()->with('success', $msg);
    }

    public function downloadCoa(QcInspection $qcInspection): BinaryFileResponse
    {
        $this->authorize('view', $qcInspection);
        $media = $qcInspection->getFirstMedia('coa_documents');

        if (! $media) {
            abort(404, 'COA document not found.');
        }

        return response()->download($media->getPath(), $media->file_name);
    }
}
