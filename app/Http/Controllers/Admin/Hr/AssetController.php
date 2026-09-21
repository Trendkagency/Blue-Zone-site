<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAssetAssignment;
use App\Models\HrAsset;
use App\Services\Hr\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    protected AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index(Request $request): View
    {
        $query = HrAsset::with(['currentAssignment.employee']);

        if ($type = $request->input('asset_type')) {
            $query->where('asset_type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $assets = $query->latest()->paginate(15)->withQueryString();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.assets.index', compact('assets', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'asset_code' => ['required', 'string', 'max:50', 'unique:hr_assets,asset_code'],
            'asset_type' => ['required', 'string'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        HrAsset::create(array_merge($validated, ['status' => 'available']));

        return back()->with('success', __('hr.asset_created', ['default' => 'Company asset added successfully.']));
    }

    public function assignments(): View
    {
        $assignments = EmployeeAssetAssignment::with(['asset', 'employee.department'])->latest('assigned_at')->paginate(15);
        $availableAssets = HrAsset::where('status', 'available')->get();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.assets.assignments', compact('assignments', 'availableAssets', 'employees'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hr_asset_id' => ['required', 'exists:hr_assets,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'assigned_at' => ['required', 'date'],
            'expected_return_date' => ['nullable', 'date', 'after_or_equal:assigned_at'],
            'condition_on_assignment' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $asset = HrAsset::findOrFail($validated['hr_asset_id']);
        $employee = Employee::findOrFail($validated['employee_id']);

        try {
            $this->assetService->assignAsset($asset, $employee, $validated, auth()->id());
            return back()->with('success', __('hr.asset_assigned', ['default' => 'Asset assigned to employee.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function returnAsset(Request $request, int $assignmentId): RedirectResponse
    {
        $assignment = EmployeeAssetAssignment::findOrFail($assignmentId);

        $validated = $request->validate([
            'returned_at' => ['required', 'date'],
            'condition_on_return' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->assetService->returnAsset($assignment, $validated);
            return back()->with('success', __('hr.asset_returned', ['default' => 'Asset returned and inventory updated.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function returns(): View
    {
        $returnedAssignments = EmployeeAssetAssignment::with(['asset', 'employee.department'])
            ->whereNotNull('returned_at')
            ->latest('returned_at')
            ->paginate(15);

        return view('admin.hr.assets.returns', compact('returnedAssignments'));
    }
}
