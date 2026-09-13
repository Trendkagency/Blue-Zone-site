<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    /**
     * Display a listing of all warehouses and storage facilities.
     */
    public function index(Request $request): View
    {
        // Ensure all locations are synced with current inventory items
        InventoryService::syncAllProductsInventory();

        $query = Location::with(['country', 'cityModel'])->withCount(['inventoryItems as active_skus_count' => function ($q) {
            $q->where('current_stock', '>', 0);
        }]);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('manager_name', 'like', "%{$search}%")
                  ->orWhereHas('country', function ($cq) use ($search) {
                      $cq->where('name_en', 'like', "%{$search}%")->orWhere('name_ar', 'like', "%{$search}%");
                  });
            });
        }

        // Type Filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $warehouses = $query->orderBy('created_at', 'asc')->get();

        // Active Countries for dynamic modal creation
        $countries = Country::with('activeCities')->where('is_active', true)->orderBy('sort_order')->orderBy('name_en')->get();

        // Calculate KPI Metrics across facilities
        $kpis = [
            'total_warehouses' => Location::count(),
            'active_warehouses' => Location::where('is_active', true)->count(),
            'total_stock_units' => (int) InventoryItem::sum('current_stock'),
            'total_stock_valuation' => (float) InventoryItem::selectRaw('SUM(current_stock * unit_cost) as val')->value('val') ?? 0.0,
            'low_stock_facilities' => Location::whereHas('inventoryItems', function ($q) {
                $q->whereIn('status', ['low_stock', 'out_of_stock']);
            })->count(),
        ];

        return view('admin.warehouses.index', [
            'warehouses' => $warehouses,
            'countries' => $countries,
            'kpis' => $kpis,
            'types' => [
                'warehouse' => ['en' => 'Logistics Warehouse', 'ar' => 'مستودع لوجستي'],
                'branch' => ['en' => 'Regional Branch', 'ar' => 'فرع إقليمي'],
                'online' => ['en' => 'E-Commerce Hub', 'ar' => 'مركز طلبات إلكترونية'],
                'offline' => ['en' => 'Warehouse / POS', 'ar' => 'مستودع / نقطة بيع'],
            ],
        ]);
    }

    /**
     * Show form for creating a new warehouse.
     */
    public function create(): View
    {
        $countries = Country::with('activeCities')->where('is_active', true)->orderBy('sort_order')->orderBy('name_en')->get();
        return view('admin.warehouses.create', compact('countries'));
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'id' => 'nullable|string|max:50|unique:locations,id|regex:/^[a-zA-Z0-9_\-]+$/',
            'code' => 'nullable|string|max:50|unique:locations,code',
            'type' => 'required|in:warehouse,branch,online,offline',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'capacity_units' => 'nullable|integer|min:10',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'id.regex' => 'The identifier slug may only contain letters, numbers, dashes, and underscores.',
        ]);

        // Generate slug ID if not provided
        $id = !empty($validated['id'])
            ? Str::slug($validated['id'], '_')
            : Str::slug($validated['name_en'], '_');

        // Ensure slug uniqueness
        $baseId = $id;
        $counter = 1;
        while (Location::where('id', $id)->exists()) {
            $id = $baseId . '_' . $counter++;
        }

        // Generate unique code if not provided
        $code = !empty($validated['code'])
            ? strtoupper(trim($validated['code']))
            : 'LOC-' . strtoupper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name_en']), 0, 4)) . rand(10, 99);

        // Resolve City string name if city_id provided
        $cityName = $validated['city'] ?? null;
        if (!empty($validated['city_id'])) {
            $cityObj = City::find($validated['city_id']);
            if ($cityObj) {
                $cityName = $cityObj->name_en;
                if (empty($validated['country_id'])) {
                    $validated['country_id'] = $cityObj->country_id;
                }
            }
        }

        // Format phone with country dial code if country selected and phone lacks '+'
        $phone = $validated['phone'] ?? null;
        if ($phone && !empty($validated['country_id']) && !str_starts_with($phone, '+') && !str_starts_with($phone, '00')) {
            $country = Country::find($validated['country_id']);
            if ($country && $country->phone_code) {
                $phone = $country->phone_code . ' ' . ltrim($phone, '0');
            }
        }

        $location = Location::create([
            'id' => $id,
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'],
            'code' => $code,
            'type' => $validated['type'],
            'country_id' => $validated['country_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $cityName,
            'manager_name' => $validated['manager_name'] ?? null,
            'phone' => $phone,
            'email' => $validated['email'] ?? null,
            'capacity_units' => $validated['capacity_units'] ?? 10000,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        // Provision inventory items across all catalog formulations immediately
        InventoryService::provisionLocationForProducts($location);

        $msg = app()->getLocale() === 'ar'
            ? "تم إضافة المستودع [{$location->name_ar}] بنجاح، وتهيئة أصناف المخزون فورياً."
            : "Storage facility [{$location->name_en}] created successfully and stocked items initialized.";

        return redirect()->route('admin.warehouses.index')->with('status', $msg);
    }

    /**
     * Display warehouse details, localized SKUs and movement audit trail.
     */
    public function show(string $id, Request $request): View
    {
        $warehouse = Location::findOrFail($id);

        $query = InventoryItem::with(['product', 'product.category'])
            ->where('location_id', $warehouse->id)
            ->join('products', 'inventory_items.product_id', '=', 'products.id')
            ->select('inventory_items.*');

        // Search inside warehouse
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('products.name_en', 'like', "%{$search}%")
                  ->orWhere('products.name_ar', 'like', "%{$search}%")
                  ->orWhere('products.sku', 'like', "%{$search}%")
                  ->orWhere('products.barcode', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('inventory_items.status', $request->status);
        }

        $stockItems = $query->orderBy('products.name_en')->paginate(25)->withQueryString();

        // Warehouse Specific Metrics
        $totalUnits = (int) $warehouse->inventoryItems()->sum('current_stock');
        $capacity = max(1, $warehouse->capacity_units ?? 10000);
        $utilizationPercent = min(100, round(($totalUnits / $capacity) * 100, 1));
        $valuation = (float) $warehouse->inventoryItems()->selectRaw('SUM(current_stock * unit_cost) as val')->value('val') ?? 0.0;

        // Recent Movements involving this warehouse
        $recentMovements = InventoryMovement::with('product')
            ->where(function ($q) use ($warehouse) {
                $q->where('from_location', 'like', "%{$warehouse->name_en}%")
                  ->orWhere('to_location', 'like', "%{$warehouse->name_en}%")
                  ->orWhere('from_location', 'like', "%{$warehouse->name_ar}%")
                  ->orWhere('to_location', 'like', "%{$warehouse->name_ar}%")
                  ->orWhere('from_location', $warehouse->id)
                  ->orWhere('to_location', $warehouse->id);
            })
            ->latest('id')
            ->take(15)
            ->get();

        $allLocations = Location::where('id', '!=', $warehouse->id)->where('is_active', true)->get();

        return view('admin.warehouses.show', [
            'warehouse' => $warehouse,
            'stockItems' => $stockItems,
            'totalUnits' => $totalUnits,
            'capacity' => $capacity,
            'utilizationPercent' => $utilizationPercent,
            'valuation' => $valuation,
            'recentMovements' => $recentMovements,
            'allLocations' => $allLocations,
        ]);
    }

    /**
     * Show form for editing the specified warehouse.
     */
    public function edit(string $id): View
    {
        $warehouse = Location::findOrFail($id);
        $countries = Country::with('activeCities')->where('is_active', true)->orderBy('sort_order')->orderBy('name_en')->get();
        return view('admin.warehouses.edit', [
            'warehouse' => $warehouse,
            'countries' => $countries,
            'types' => [
                'warehouse' => ['en' => 'Logistics Warehouse', 'ar' => 'مستودع لوجستي'],
                'branch' => ['en' => 'Regional Branch', 'ar' => 'فرع إقليمي'],
                'online' => ['en' => 'E-Commerce Hub', 'ar' => 'مركز طلبات إلكترونية'],
                'offline' => ['en' => 'Warehouse / POS', 'ar' => 'مستودع / نقطة بيع'],
            ],
        ]);
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $warehouse = Location::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:locations,code,' . $warehouse->id . ',id',
            'type' => 'required|in:warehouse,branch,online,offline',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'capacity_units' => 'nullable|integer|min:10',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Resolve City string name if city_id provided
        $cityName = $validated['city'] ?? $warehouse->city;
        if (!empty($validated['city_id'])) {
            $cityObj = City::find($validated['city_id']);
            if ($cityObj) {
                $cityName = $cityObj->name_en;
                if (empty($validated['country_id'])) {
                    $validated['country_id'] = $cityObj->country_id;
                }
            }
        }

        // Format phone with country dial code if country selected and phone lacks '+'
        $phone = $validated['phone'] ?? null;
        if ($phone && !empty($validated['country_id']) && !str_starts_with($phone, '+') && !str_starts_with($phone, '00')) {
            $country = Country::find($validated['country_id']);
            if ($country && $country->phone_code) {
                $phone = $country->phone_code . ' ' . ltrim($phone, '0');
            }
        }

        $warehouse->update([
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'],
            'code' => strtoupper(trim($validated['code'])),
            'type' => $validated['type'],
            'country_id' => $validated['country_id'] ?? $warehouse->country_id,
            'city_id' => $validated['city_id'] ?? $warehouse->city_id,
            'address' => $validated['address'] ?? null,
            'city' => $cityName,
            'manager_name' => $validated['manager_name'] ?? null,
            'phone' => $phone,
            'email' => $validated['email'] ?? null,
            'capacity_units' => $validated['capacity_units'] ?? 10000,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : false,
        ]);

        // Synchronize updated names across items
        InventoryService::syncLocationNames($warehouse);

        $msg = app()->getLocale() === 'ar'
            ? "تم تحديث بيانات المستودع [{$warehouse->name_ar}] بنجاح."
            : "Warehouse facility [{$warehouse->name_en}] updated successfully.";

        return redirect()->route('admin.warehouses.index')->with('status', $msg);
    }

    /**
     * Toggle active status of a warehouse.
     */
    public function toggleStatus(string $id): RedirectResponse
    {
        $warehouse = Location::findOrFail($id);

        if ($warehouse->is_system_core && $warehouse->is_active) {
            // Check if user tries to disable a critical core node
            $activeCount = Location::where('is_active', true)->count();
            if ($activeCount <= 1) {
                $err = app()->getLocale() === 'ar'
                    ? "لا يمكن تعطيل هذا المستودع لأنه المستودع النشط الوحيد في النظام."
                    : "Cannot deactivate this facility as it is the only active hub.";
                return back()->withErrors(['status_error' => $err]);
            }
        }

        $warehouse->is_active = !$warehouse->is_active;
        $warehouse->save();

        $stateText = $warehouse->is_active
            ? (app()->getLocale() === 'ar' ? 'تفعيل' : 'activated')
            : (app()->getLocale() === 'ar' ? 'تعطيل' : 'deactivated');

        $msg = app()->getLocale() === 'ar'
            ? "تم {$stateText} المستودع [{$warehouse->name_ar}] بنجاح."
            : "Warehouse [{$warehouse->name_en}] has been {$stateText} successfully.";

        return back()->with('status', $msg);
    }

    /**
     * Remove the specified warehouse from storage safely.
     */
    public function destroy(string $id): RedirectResponse
    {
        $warehouse = Location::findOrFail($id);

        if ($warehouse->is_system_core) {
            $err = app()->getLocale() === 'ar'
                ? "لا يمكن حذف المستودعات الأساسية للنظام ({$warehouse->name_ar}). يمكنك تعديل بياناتها أو تعطيلها بدلاً من ذلك."
                : "System core facilities ({$warehouse->name_en}) cannot be deleted. You may edit or deactivate them instead.";
            return back()->withErrors(['delete_error' => $err]);
        }

        $currentStockSum = (int) $warehouse->inventoryItems()->sum('current_stock');
        if ($currentStockSum > 0) {
            $err = app()->getLocale() === 'ar'
                ? "لا يمكن حذف المستودع لوجود رصيد مخزون فعلي به ({$currentStockSum} وحدة). يرجى تحويل أو تصفية المخزون أولاً."
                : "Cannot delete warehouse holding {$currentStockSum} active units. Please relocate or write off inventory first.";
            return back()->withErrors(['delete_error' => $err]);
        }

        // Delete 0-stock inventory items attached to this warehouse
        $warehouse->inventoryItems()->delete();
        $warehouse->delete();

        $msg = app()->getLocale() === 'ar'
            ? "تم حذف المستودع بنجاح."
            : "Storage facility deleted successfully.";

        return redirect()->route('admin.warehouses.index')->with('status', $msg);
    }
}
