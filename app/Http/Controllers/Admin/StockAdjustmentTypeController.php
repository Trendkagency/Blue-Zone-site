<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdjustmentType;
use App\Models\InventoryMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StockAdjustmentTypeController extends Controller
{
    /**
     * Display listing of adjustment types with full management controls.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $direction = $request->query('direction', 'all');
        $status = $request->query('status', 'all');

        $query = AdjustmentType::withCount('movements')->orderBy('sort_order')->orderBy('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($direction && $direction !== 'all') {
            $query->where('direction', $direction);
        }

        if ($status && $status !== 'all') {
            $query->where('is_active', $status === 'active');
        }

        $types = $query->paginate(20)->withQueryString();

        // Statistical intelligence
        $allTypes = AdjustmentType::all();
        $totalTypes = $allTypes->count();
        $activeTypes = $allTypes->where('is_active', true)->count();
        $increaseTypes = $allTypes->where('direction', 'increase')->count();
        $decreaseTypes = $allTypes->where('direction', 'decrease')->count();

        return view('admin.inventory.adjustments.types', [
            'types' => $types,
            'totalTypes' => $totalTypes,
            'activeTypes' => $activeTypes,
            'increaseTypes' => $increaseTypes,
            'decreaseTypes' => $decreaseTypes,
            'search' => $search,
            'selectedDirection' => $direction,
            'selectedStatus' => $status,
            'currentPage' => $types->currentPage(),
            'totalPages' => $types->lastPage(),
        ]);
    }

    /**
     * Store a new adjustment type (Standard Form Submission).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'direction' => 'required|in:increase,decrease,neutral',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $code = Str::title(trim($validated['name_en']));
        // Avoid duplicate codes
        $originalCode = $code;
        $counter = 1;
        while (AdjustmentType::where('code', $code)->exists()) {
            $code = "{$originalCode} ({$counter})";
            $counter++;
        }

        AdjustmentType::create([
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'] ?? null,
            'code' => $code,
            'direction' => $validated['direction'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
            'is_system' => false,
            'sort_order' => (int) ($validated['sort_order'] ?? 10),
        ]);

        return back()->with('status', app()->getLocale() === 'ar'
            ? "تم إنشاء نوع التسوية ({$validated['name_en']}) بنجاح."
            : "Adjustment type '{$validated['name_en']}' created successfully.");
    }

    /**
     * Real-time creation endpoint (AJAX from the Initiate Adjustment Modal).
     */
    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'direction' => 'required|in:increase,decrease,neutral',
            'description' => 'nullable|string|max:1000',
        ]);

        $code = Str::title(trim($validated['name_en']));
        $originalCode = $code;
        $counter = 1;
        while (AdjustmentType::where('code', $code)->exists()) {
            $code = "{$originalCode} ({$counter})";
            $counter++;
        }

        $maxSort = (int) AdjustmentType::max('sort_order') + 1;

        $type = AdjustmentType::create([
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'] ?? null,
            'code' => $code,
            'direction' => $validated['direction'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'is_system' => false,
            'sort_order' => $maxSort,
        ]);

        $displayName = app()->getLocale() === 'ar' && !empty($type->name_ar) 
            ? "{$type->name_ar} ({$type->name_en})" 
            : $type->name_en;

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? "تم إنشاء نوع التسوية '{$type->name_en}' واعتماده بنجاح في الوقت الفعلي."
                : "Adjustment type '{$type->name_en}' created and selected in real-time.",
            'type' => [
                'id' => $type->id,
                'name_en' => $type->name_en,
                'name_ar' => $type->name_ar,
                'code' => $type->code,
                'direction' => $type->direction,
                'display_name' => $displayName,
            ],
        ]);
    }

    /**
     * Update an adjustment type.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $type = AdjustmentType::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'direction' => 'required|in:increase,decrease,neutral',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data = [
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];

        // Protect system direction if it's a foundational core type
        if (!$type->is_system) {
            $data['direction'] = $validated['direction'];
        }

        $type->update($data);

        return back()->with('status', app()->getLocale() === 'ar'
            ? "تم تحديث نوع التسوية ({$type->name_en}) بنجاح."
            : "Adjustment type '{$type->name_en}' updated successfully.");
    }

    /**
     * Toggle active state.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $type = AdjustmentType::findOrFail($id);
        $type->is_active = !$type->is_active;
        $type->save();

        $statusStr = $type->is_active ? 'Active' : 'Inactive';

        return back()->with('status', app()->getLocale() === 'ar'
            ? "تم تغيير حالة نوع التسوية إلى ({$statusStr})."
            : "Adjustment type status changed to {$statusStr}.");
    }

    /**
     * Delete an adjustment type.
     */
    public function destroy(int $id): RedirectResponse
    {
        $type = AdjustmentType::findOrFail($id);

        if ($type->is_system) {
            return back()->withErrors([
                'type_error' => app()->getLocale() === 'ar'
                    ? 'لا يمكن حذف نوع تسوية أساسي في النظام. يمكنك تعطيله بدلاً من ذلك.'
                    : 'System core adjustment types cannot be deleted. You may deactivate it instead.'
            ]);
        }

        $hasMovements = InventoryMovement::where('movement_type', $type->code)->exists();
        if ($hasMovements) {
            return back()->withErrors([
                'type_error' => app()->getLocale() === 'ar'
                    ? 'لا يمكن حذف هذا النوع لوجود حركات تسوية تاريخية مقيدة به في سجل التدقيق. يمكنك إلغاء تفعيله.'
                    : 'Cannot delete this type because historical audit ledger movements are linked to it. Deactivate it instead.'
            ]);
        }

        $name = $type->name_en;
        $type->delete();

        return back()->with('status', app()->getLocale() === 'ar'
            ? "تم حذف نوع التسوية ({$name}) بنجاح."
            : "Adjustment type '{$name}' deleted successfully.");
    }
}
