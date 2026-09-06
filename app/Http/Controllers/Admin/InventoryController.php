<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\View\ViewModels\InventoryViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $stockItems = InventoryViewModel::stockItems();
        $locations = InventoryViewModel::locations();
=======
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Product;
use App\Services\InventoryService;
use App\View\ViewModels\InventoryViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        // Ensure all active products have location items provisioned
        InventoryService::syncAllProductsInventory();

        $selectedLocation = $request->query('location', 'all');
        $selectedStatus = $request->query('status', 'all');
        $search = $request->query('search');

        // Query Builder for inventory items
        $query = InventoryItem::with('product')->join('products', 'inventory_items.product_id', '=', 'products.id')
            ->select('inventory_items.*');

        if ($selectedLocation && $selectedLocation !== 'all') {
            $query->where('inventory_items.location_id', $selectedLocation);
        }

        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('inventory_items.status', $selectedStatus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.name_en', 'like', "%{$search}%")
                  ->orWhere('products.name_ar', 'like', "%{$search}%")
                  ->orWhere('products.sku', 'like', "%{$search}%")
                  ->orWhere('products.barcode', 'like', "%{$search}%")
                  ->orWhere('inventory_items.variant_en', 'like', "%{$search}%");
            });
        }

        $stockItems = $query->orderBy('products.id')->orderBy('inventory_items.location_id')->paginate(20)->withQueryString();

        // System Locations
        $locations = Location::where('is_active', true)->get();
        if ($locations->isEmpty()) {
            $locations = collect(InventoryViewModel::locations());
        }

        // Live KPI Metrics
        $kpis = [
            'total_units' => (int) InventoryItem::sum('current_stock'),
            'online_units' => (int) InventoryItem::where('location_id', 'online')->sum('current_stock'),
            'offline_units' => (int) InventoryItem::where('location_id', 'offline')->sum('current_stock'),
            'central_units' => (int) InventoryItem::where('location_id', 'central_wh')->sum('current_stock'),
            'low_stock_count' => (int) InventoryItem::where('status', 'low_stock')->count(),
            'out_of_stock_count' => (int) InventoryItem::where('status', 'out_of_stock')->count(),
        ];

        $allProducts = Product::orderBy('name_en')->get();
>>>>>>> origin/main

        return view('admin.inventory.index', [
            'stockItems' => $stockItems,
            'locations' => $locations,
<<<<<<< HEAD
            'currentPage' => 1,
            'totalPages' => 1,
=======
            'selectedLocation' => $selectedLocation,
            'selectedStatus' => $selectedStatus,
            'search' => $search,
            'kpis' => $kpis,
            'allProducts' => $allProducts,
            'currentPage' => $stockItems->currentPage(),
            'totalPages' => $stockItems->lastPage(),
>>>>>>> origin/main
        ]);
    }

    public function show(int $id): View
    {
<<<<<<< HEAD
        $stockItems = InventoryViewModel::stockItems();
        $item = null;
        foreach ($stockItems as $si) {
            if ($si['id'] === $id) {
                $item = $si;
                break;
            }
        }

        $movements = InventoryViewModel::movements();

        return view('admin.inventory.show', [
            'item' => $item ?? $stockItems[0],
=======
        // Try finding inventory item first
        $item = InventoryItem::with('product')->find($id);
        $product = null;

        if ($item) {
            $product = $item->product;
        } else {
            $product = Product::find($id);
            if ($product) {
                $item = InventoryItem::where('product_id', $product->id)->first();
            }
        }

        if (!$product && !$item) {
            abort(404, 'Product or inventory item not found.');
        }

        // Stock across all locations for this product
        $locationBreakdowns = InventoryItem::where('product_id', $product->id)->get();

        // Movements specifically for this product
        $movements = InventoryMovement::where('product_id', $product->id)
            ->latest('date')
            ->latest('time')
            ->latest('id')
            ->paginate(15);

        return view('admin.inventory.show', [
            'item' => $item ?? $locationBreakdowns->first(),
            'product' => $product,
            'locationBreakdowns' => $locationBreakdowns,
>>>>>>> origin/main
            'movements' => $movements,
        ]);
    }

    public function transfers(): View
    {
<<<<<<< HEAD
        $products = ProductViewModel::all();
        $locations = InventoryViewModel::locations();
        $recentTransfers = array_filter(InventoryViewModel::movements(), fn ($m) => $m['movement_type'] === 'Stock Transfer');
=======
        $products = Product::orderBy('name_en')->get();
        $locations = Location::where('is_active', true)->get();

        $transfers = InventoryMovement::where('movement_type', 'Stock Transfer')
            ->latest('id')
            ->take(20)
            ->get();

        // Map product location stock for live javascript projection
        $inventoryMap = [];
        $allItems = InventoryItem::all();
        foreach ($allItems as $it) {
            $inventoryMap[$it->product_id][$it->location_id] = [
                'current' => $it->current_stock,
                'available' => $it->available_stock,
            ];
        }
>>>>>>> origin/main

        return view('admin.inventory.transfers', [
            'products' => $products,
            'locations' => $locations,
<<<<<<< HEAD
            'transfers' => $recentTransfers,
        ]);
    }

    public function history(): View
    {
        $movements = InventoryViewModel::movements();
        $locations = InventoryViewModel::locations();

        return view('admin.inventory.history', [
            'movements' => $movements,
            'locations' => $locations,
            'currentPage' => 1,
            'totalPages' => 1,
=======
            'transfers' => $transfers,
            'inventoryMap' => $inventoryMap,
        ]);
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_location' => 'required|string',
            'to_location' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        try {
            InventoryService::transferStock(
                product: $product,
                fromLocationId: $validated['from_location'],
                toLocationId: $validated['to_location'],
                quantity: (int) $validated['quantity'],
                reason: $validated['reason'] ?? null,
                userName: auth()->user()?->name ?? 'Admin'
            );

            return redirect()->route('admin.inventory.transfers')
                ->with('status', app()->getLocale() === 'ar'
                    ? "تم تحويل {$validated['quantity']} وحدة بنجاح وتسجيل الحركة في السجل المركزي."
                    : "Stock transfer of {$validated['quantity']} units completed and logged in central ledger.");
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['transfer_error' => $e->getMessage()]);
        }
    }

    public function storeAdjustment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|string',
            'movement_type' => 'required|string|in:Stock In,Stock Out,Return,Damaged,Expired,Manual Adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];
        $type = $validated['movement_type'];

        // Determine sign based on movement type
        $delta = in_array($type, ['Stock Out', 'Damaged', 'Expired'], true) ? -$qty : $qty;

        try {
            InventoryService::adjustStock(
                product: $product,
                locationId: $validated['location_id'],
                quantityDelta: $delta,
                movementType: $type,
                reason: $validated['reason'],
                userName: auth()->user()?->name ?? 'Admin'
            );

            return back()->with('status', app()->getLocale() === 'ar'
                ? "تم تسجيل تسوية المخزون بنجاح ({$type}: {$delta} وحدة)."
                : "Inventory adjustment processed successfully ({$type}: {$delta} units).");
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['adjustment_error' => $e->getMessage()]);
        }
    }

    public function history(Request $request): View
    {
        $query = InventoryMovement::with('product')->latest('id');

        // Movement Types Filter
        if ($request->filled('movement_type') && $request->movement_type !== 'all') {
            $query->where('movement_type', $request->movement_type);
        }

        // Product Filter
        if ($request->filled('product_id') && $request->product_id !== 'all') {
            $query->where('product_id', $request->product_id);
        }

        // Location Filter
        if ($request->filled('location') && $request->location !== 'all') {
            $loc = $request->location;
            $query->where(function ($q) use ($loc) {
                $q->where('from_location', 'like', "%{$loc}%")
                  ->orWhere('to_location', 'like', "%{$loc}%");
            });
        }

        // Search Query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('product_name_en', 'like', "%{$search}%")
                  ->orWhere('product_name_ar', 'like', "%{$search}%")
                  ->orWhere('user', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        $movements = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name_en')->get();
        $locations = Location::where('is_active', true)->get();

        // 10 Standard Movement Types as per Requirement 23
        $movementTypes = [
            'Stock In' => ['en' => 'Stock In', 'ar' => 'إدخال مخزون جديد (توريد)'],
            'Stock Out' => ['en' => 'Stock Out', 'ar' => 'إخراج مخزون'],
            'Online Sale' => ['en' => 'Online Sale', 'ar' => 'بيع إلكتروني'],
            'Offline Sale' => ['en' => 'Offline Sale', 'ar' => 'بيع في المعرض / POS'],
            'Stock Transfer' => ['en' => 'Stock Transfer', 'ar' => 'تحويل بين المواقع'],
            'Return' => ['en' => 'Return', 'ar' => 'مرتجع عميل'],
            'Damaged' => ['en' => 'Damaged', 'ar' => 'تالف'],
            'Expired' => ['en' => 'Expired', 'ar' => 'منتهي الصلاحية'],
            'Manual Adjustment' => ['en' => 'Manual Adjustment', 'ar' => 'تسوية يدوية'],
            'Cancelled Order' => ['en' => 'Cancelled Order', 'ar' => 'طلب ملغي (استرجاع للمخزون)'],
        ];

        return view('admin.inventory.history', [
            'movements' => $movements,
            'products' => $products,
            'locations' => $locations,
            'movementTypes' => $movementTypes,
            'selectedType' => $request->query('movement_type', 'all'),
            'selectedProduct' => $request->query('product_id', 'all'),
            'selectedLocation' => $request->query('location', 'all'),
            'search' => $request->query('search', ''),
            'currentPage' => $movements->currentPage(),
            'totalPages' => $movements->lastPage(),
>>>>>>> origin/main
        ]);
    }
}
