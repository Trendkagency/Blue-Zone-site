<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RawMaterialBatchStatus;
use App\Enums\RawMaterialCategory;
use App\Enums\StorageCondition;
use App\Enums\UnitOfMeasure;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RawMaterialRequest;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RawMaterialController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', RawMaterial::class);

        $query = RawMaterial::with('defaultSupplier')
            ->withCount(['batches', 'products']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('cas_number', 'like', "%{$search}%")
                    ->orWhere('inci_name', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($storage = $request->query('storage_condition')) {
            $query->where('storage_condition', $storage);
        }

        $allowedSorts = ['name_en', 'code', 'category', 'standard_cost', 'created_at'];
        $sortBy = in_array($request->query('sort_by'), $allowedSorts, true) ? $request->query('sort_by') : 'created_at';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';

        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $rawMaterials = $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString();

        $categories = RawMaterialCategory::cases();
        $storageConditions = StorageCondition::cases();

        $materials = $rawMaterials;

        $kpis = [
            'total_skus' => $rawMaterials->total(),
            'active_ingredients' => RawMaterial::where('category', RawMaterialCategory::ActiveIngredient)->count(),
            'quarantine_batches' => RawMaterialBatch::where('status', RawMaterialBatchStatus::Quarantine)->count(),
            'low_stock' => RawMaterial::withSum(['batches as total_avail' => function ($q) {
                $q->where('status', RawMaterialBatchStatus::Approved);
            }], 'available_qty')
                ->get()
                ->filter(fn ($m) => ($m->total_avail ?? 0) <= ($m->min_reorder_qty ?? 0))
                ->count(),
        ];

        return view('admin.supply_chain.raw_materials.index', compact('rawMaterials', 'materials', 'categories', 'storageConditions', 'kpis'));
    }

    public function create(): View
    {
        $this->authorize('create', RawMaterial::class);
        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();
        $categories = RawMaterialCategory::cases();
        $storageConditions = StorageCondition::cases();
        $units = UnitOfMeasure::cases();

        return view('admin.supply_chain.raw_materials.create', compact('suppliers', 'categories', 'storageConditions', 'units'));
    }

    public function store(RawMaterialRequest $request): RedirectResponse
    {
        $this->authorize('create', RawMaterial::class);
        $material = RawMaterial::create($request->validated());

        return redirect()->route('admin.raw-materials.show', $material)
            ->with('success', __('raw_materials.messages.created', ['default' => 'Raw material added to catalog successfully.']));
    }

    public function show(RawMaterial $rawMaterial): View
    {
        $this->authorize('view', $rawMaterial);
        $rawMaterial->load(['defaultSupplier', 'supplierPrices.supplier', 'batches.location', 'products']);

        return view('admin.supply_chain.raw_materials.show', compact('rawMaterial'));
    }

    public function edit(RawMaterial $rawMaterial): View
    {
        $this->authorize('update', $rawMaterial);
        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();
        $categories = RawMaterialCategory::cases();
        $storageConditions = StorageCondition::cases();
        $units = UnitOfMeasure::cases();

        return view('admin.supply_chain.raw_materials.edit', compact('rawMaterial', 'suppliers', 'categories', 'storageConditions', 'units'));
    }

    public function update(RawMaterialRequest $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $this->authorize('update', $rawMaterial);
        $rawMaterial->update($request->validated());

        return redirect()->route('admin.raw-materials.show', $rawMaterial)
            ->with('success', __('raw_materials.messages.updated', ['default' => 'Raw material details updated successfully.']));
    }

    public function destroy(RawMaterial $rawMaterial): RedirectResponse
    {
        $this->authorize('delete', $rawMaterial);
        $rawMaterial->delete();

        return redirect()->route('admin.raw-materials.index')
            ->with('success', __('raw_materials.messages.deleted', ['default' => 'Raw material deleted successfully.']));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', RawMaterial::class);

        $materials = RawMaterial::with('defaultSupplier')->orderBy('name_en')->get();
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Code', 'Name (EN)', 'Name (AR)', 'CAS #', 'Category', 'UOM', 'Storage', 'Standard Cost', 'Min Reorder Qty', 'Safety Stock', 'Default Supplier'],
        ], null, 'A1');

        $row = 2;
        foreach ($materials as $m) {
            $sheet->fromArray([
                [$m->code, $m->name_en, $m->name_ar, $m->cas_number, $m->category->value, $m->unit_of_measure->value, $m->storage_condition->value, $m->standard_cost, $m->min_reorder_qty, $m->safety_stock, $m->defaultSupplier?->name_en ?? 'N/A'],
            ], null, 'A'.$row);
            $row++;
        }

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="raw_materials_catalog_'.date('Ymd_His').'.xlsx"',
        ]);
    }
}
