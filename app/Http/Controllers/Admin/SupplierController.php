<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SupplierType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Country;
use App\Models\Supplier;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Supplier::class);

        $query = Supplier::with(['country', 'qaApprover', 'procurementApprover'])
            ->withCount(['purchaseOrders', 'batches']);

        // Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        // Allow-listed sorting
        $allowedSorts = ['name_en', 'code', 'rating', 'created_at', 'lead_time_days'];
        $sortBy = in_array($request->query('sort_by'), $allowedSorts, true) ? $request->query('sort_by') : 'created_at';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';

        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $suppliers = $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString();

        $kpis = [
            'total_vendors' => $suppliers->total(),
            'active_qualified' => Supplier::where('is_active', true)->count(),
            'pending_approval' => Supplier::where(function ($q) {
                $q->whereNull('qa_approved_at')->orWhereNull('procurement_approved_at');
            })->count(),
            'audit_expiring' => Supplier::whereNotNull('audit_expiry')
                ->where('audit_expiry', '<=', now()->addDays(60))
                ->count(),
        ];

        return view('admin.supply_chain.suppliers.index', compact('suppliers', 'kpis'));
    }

    public function create(): View
    {
        $this->authorize('create', Supplier::class);
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();
        $types = SupplierType::cases();

        return view('admin.supply_chain.suppliers.create', compact('countries', 'types'));
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $this->authorize('create', Supplier::class);
        $supplier = Supplier::create($request->validated());

        if ($request->hasFile('cgmp_document')) {
            $supplier->addMediaFromRequest('cgmp_document')->toMediaCollection('cgmp_certificates');
        }
        if ($request->hasFile('iso_document')) {
            $supplier->addMediaFromRequest('iso_document')->toMediaCollection('iso_certificates');
        }

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', __('suppliers.messages.created', ['default' => 'Supplier created successfully and placed under onboarding review.']));
    }

    public function show(Supplier $supplier): View
    {
        $this->authorize('view', $supplier);
        $supplier->load(['country', 'qaApprover', 'procurementApprover', 'materialPrices.rawMaterial', 'purchaseOrders', 'batches']);

        return view('admin.supply_chain.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        $this->authorize('update', $supplier);
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();
        $types = SupplierType::cases();

        return view('admin.supply_chain.suppliers.edit', compact('supplier', 'countries', 'types'));
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $supplier->update($request->validated());

        if ($request->hasFile('cgmp_document')) {
            $supplier->addMediaFromRequest('cgmp_document')->toMediaCollection('cgmp_certificates');
        }
        if ($request->hasFile('iso_document')) {
            $supplier->addMediaFromRequest('iso_document')->toMediaCollection('iso_certificates');
        }

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', __('suppliers.messages.updated', ['default' => 'Supplier details updated successfully.']));
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete', $supplier);
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', __('suppliers.messages.deleted', ['default' => 'Supplier deleted successfully.']));
    }

    /**
     * Dual-approval workflow action for QA Director or Procurement Head
     */
    public function approve(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('approve', $supplier);
        $user = $request->user();
        $role = $request->validate([
            'approval_role' => ['required', 'in:qa,procurement'],
        ])['approval_role'];

        if ($role === 'qa') {
            if ($supplier->procurement_approved_by === $user->id) {
                throw new DomainException(__('suppliers.errors.same_user_approval', [
                    'default' => 'Separation of duties violation: You cannot provide both QA and Procurement approvals.',
                ]));
            }
            $supplier->qa_approved_by = $user->id;
            $supplier->qa_approved_at = now();
        } else {
            if ($supplier->qa_approved_by === $user->id) {
                throw new DomainException(__('suppliers.errors.same_user_approval', [
                    'default' => 'Separation of duties violation: You cannot provide both QA and Procurement approvals.',
                ]));
            }
            $supplier->procurement_approved_by = $user->id;
            $supplier->procurement_approved_at = now();
        }

        // Activate if dual approval is satisfied
        if ($supplier->isDualApproved()) {
            $supplier->is_active = true;
        }

        $supplier->save();

        return back()->with('success', __('suppliers.messages.approved', [
            'default' => 'Supplier onboarding sign-off recorded successfully.',
        ]));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = Supplier::with('country')->orderBy('name_en')->get();
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Code', 'Name (EN)', 'Name (AR)', 'Type', 'Country', 'Rating', 'Active', 'Lead Time (Days)', 'Payment Terms'],
        ], null, 'A1');

        $row = 2;
        foreach ($suppliers as $s) {
            $sheet->fromArray([
                [$s->code, $s->name_en, $s->name_ar, $s->type->value, $s->country?->name_en ?? 'N/A', $s->rating, $s->is_active ? 'Yes' : 'No', $s->lead_time_days, $s->payment_terms],
            ], null, 'A'.$row);
            $row++;
        }

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="suppliers_export_'.date('Ymd_His').'.xlsx"',
        ]);
    }
}
