<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Product;
use App\Services\InventoryService;
use App\View\ViewModels\InventoryViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        return view('admin.inventory.index', [
            'stockItems' => $stockItems,
            'locations' => $locations,
            'selectedLocation' => $selectedLocation,
            'selectedStatus' => $selectedStatus,
            'search' => $search,
            'kpis' => $kpis,
            'allProducts' => $allProducts,
            'currentPage' => $stockItems->currentPage(),
            'totalPages' => $stockItems->lastPage(),
        ]);
    }

    public function show(int $id): View
    {
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
            'movements' => $movements,
        ]);
    }

    public function transfers(): View
    {
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

        return view('admin.inventory.transfers', [
            'products' => $products,
            'locations' => $locations,
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

    public function storeAdjustment(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|string',
            'movement_type' => 'required|string|in:Stock In,Stock Out,Return,Damaged,Expired,Manual Adjustment,Initial Stock',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];
        $type = $validated['movement_type'];

        // Determine sign based on movement type
        $delta = in_array($type, ['Stock Out', 'Damaged', 'Expired'], true) ? -$qty : $qty;

        $note = $validated['reason'];
        if (!empty($validated['reference_number'])) {
            $note = "[Ref: {$validated['reference_number']}] " . $note;
        }

        try {
            $movement = InventoryService::adjustStock(
                product: $product,
                locationId: $validated['location_id'],
                quantityDelta: $delta,
                movementType: $type,
                reason: $note,
                userName: auth()->user()?->name ?? 'Admin'
            );

            if ($request->wantsJson() || $request->ajax()) {
                $product->refresh();
                $items = InventoryItem::where('product_id', $product->id)->get();
                $centralUnits = (int) $items->where('location_id', 'central_wh')->sum('current_stock');
                $totalStock = (int) $product->stock_online + (int) $product->stock_offline + $centralUnits;

                return response()->json([
                    'success' => true,
                    'message' => app()->getLocale() === 'ar'
                        ? "تم تسجيل حركة المخزون بنجاح ({$type}: " . ($delta > 0 ? "+{$delta}" : "{$delta}") . " وحدة)."
                        : "Inventory movement recorded successfully ({$type}: " . ($delta > 0 ? "+{$delta}" : "{$delta}") . " units).",
                    'stock_online' => (int) $product->stock_online,
                    'stock_offline' => (int) $product->stock_offline,
                    'stock_central' => $centralUnits,
                    'total_stock' => $totalStock,
                    'location_id' => $validated['location_id'],
                    'movement' => [
                        'id' => $movement->id,
                        'movement_type' => $movement->movement_type,
                        'quantity' => $movement->quantity,
                        'date' => $movement->date?->format('Y-m-d') ?? now()->toDateString(),
                        'time' => $movement->time,
                        'user' => $movement->user,
                        'note' => $movement->note,
                    ],
                ]);
            }

            return back()->with('status', app()->getLocale() === 'ar'
                ? "تم تسجيل تسوية المخزون بنجاح ({$type}: " . ($delta > 0 ? "+{$delta}" : "{$delta}") . " وحدة)."
                : "Inventory adjustment processed successfully ({$type}: " . ($delta > 0 ? "+{$delta}" : "{$delta}") . " units).");
        } catch (InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
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
        ]);
    }

    /**
     * Interactive Drag & Drop Multi-Hub Stock Allocator workspace.
     */
    public function allocator(Request $request): View
    {
        InventoryService::syncAllProductsInventory();

        $categories = Category::where('is_active', true)->orderBy('name_en')->get();
        $dbProducts = Product::with(['category', 'inventoryItems'])->orderBy('name_en')->get();

        $onlineUnits = 0;
        $offlineUnits = 0;
        $centralUnits = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;

        $allocatedProducts = $dbProducts->map(function ($product) use (&$onlineUnits, &$offlineUnits, &$centralUnits, &$lowStockCount, &$outOfStockCount) {
            $onlineItem = $product->inventoryItems->firstWhere('location_id', 'online');
            $offlineItem = $product->inventoryItems->firstWhere('location_id', 'offline');
            $centralItem = $product->inventoryItems->firstWhere('location_id', 'central_wh');

            $oStock = $onlineItem ? (int) $onlineItem->current_stock : (int) $product->stock_online;
            $fStock = $offlineItem ? (int) $offlineItem->current_stock : (int) $product->stock_offline;
            $cStock = $centralItem ? (int) $centralItem->current_stock : 50;

            $totalStock = $oStock + $fStock + $cStock;
            $threshold = (int) ($product->low_stock_threshold ?? 10);

            $onlineUnits += $oStock;
            $offlineUnits += $fStock;
            $centralUnits += $cStock;

            $status = 'healthy';
            if ($totalStock <= 0) {
                $status = 'out_of_stock';
                $outOfStockCount++;
            } elseif ($oStock <= $threshold || $fStock <= $threshold) {
                $status = 'low_stock';
                $lowStockCount++;
            }

            return [
                'id' => $product->id,
                'name_en' => $product->name_en,
                'name_ar' => $product->name_ar ?? $product->name_en,
                'sku' => $product->sku,
                'barcode' => $product->barcode ?? '6281100' . $product->id,
                'category_id' => $product->category_id,
                'category_en' => $product->category?->name_en ?? 'Cellular Longevity',
                'category_ar' => $product->category?->name_ar ?? 'طول العمر الخلوي',
                'price' => (float) $product->price,
                'image' => Product::normalizeUrl($product->primary_image_url ?? $product->image),
                'stock_online' => $oStock,
                'stock_offline' => $fStock,
                'stock_central' => $cStock,
                'total_stock' => $totalStock,
                'low_stock_threshold' => $threshold,
                'status' => $status,
            ];
        })->values();

        $totalUnits = $onlineUnits + $offlineUnits + $centralUnits;

        $kpis = [
            'total_units' => $totalUnits,
            'online_units' => $onlineUnits,
            'offline_units' => $offlineUnits,
            'central_units' => $centralUnits,
            'online_pct' => $totalUnits > 0 ? round(($onlineUnits / $totalUnits) * 100, 1) : 0,
            'offline_pct' => $totalUnits > 0 ? round(($offlineUnits / $totalUnits) * 100, 1) : 0,
            'central_pct' => $totalUnits > 0 ? round(($centralUnits / $totalUnits) * 100, 1) : 0,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_products' => $allocatedProducts->count(),
        ];

        $recentMovements = InventoryMovement::latest('id')->take(10)->get();

        return view('admin.inventory.allocator', [
            'products' => $allocatedProducts,
            'categories' => $categories,
            'kpis' => $kpis,
            'recentMovements' => $recentMovements,
        ]);
    }

    /**
     * Handle AJAX Drag & Drop transfer between locations.
     */
    public function ajaxTransfer(Request $request): JsonResponse
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
            $movement = InventoryService::transferStock(
                product: $product,
                fromLocationId: $validated['from_location'],
                toLocationId: $validated['to_location'],
                quantity: (int) $validated['quantity'],
                reason: $validated['reason'] ?? 'Visual Drag & Drop Rebalancing',
                userName: auth()->user()?->name ?? 'Admin Allocator'
            );

            $product->refresh();
            $onlineItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'online')->first();
            $offlineItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'offline')->first();
            $centralItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'central_wh')->first();

            $oStock = $onlineItem ? (int) $onlineItem->current_stock : (int) $product->stock_online;
            $fStock = $offlineItem ? (int) $offlineItem->current_stock : (int) $product->stock_offline;
            $cStock = $centralItem ? (int) $centralItem->current_stock : 0;
            $total = $oStock + $fStock + $cStock;

            // Global KPI update
            $allOnline = (int) InventoryItem::where('location_id', 'online')->sum('current_stock');
            $allOffline = (int) InventoryItem::where('location_id', 'offline')->sum('current_stock');
            $allCentral = (int) InventoryItem::where('location_id', 'central_wh')->sum('current_stock');
            $allTotal = $allOnline + $allOffline + $allCentral;

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? "تم خصم {$validated['quantity']} وحدة من [{$validated['from_location']}] وإضافتها إلى [{$validated['to_location']}] بنجاح!"
                    : "Deducted {$validated['quantity']} units from [{$validated['from_location']}] and added to [{$validated['to_location']}] successfully!",
                'product' => [
                    'id' => $product->id,
                    'stock_online' => $oStock,
                    'stock_offline' => $fStock,
                    'stock_central' => $cStock,
                    'total_stock' => $total,
                ],
                'movement' => [
                    'id' => $movement->id,
                    'product_name' => app()->getLocale() === 'ar' ? $product->name_ar : $product->name_en,
                    'sku' => $product->sku,
                    'from' => $movement->from_location,
                    'to' => $movement->to_location,
                    'quantity' => $movement->quantity,
                    'time' => now()->format('H:i:s'),
                    'user' => $movement->user,
                    'note' => $movement->note,
                ],
                'kpis' => [
                    'total_units' => $allTotal,
                    'online_units' => $allOnline,
                    'offline_units' => $allOffline,
                    'central_units' => $allCentral,
                    'online_pct' => $allTotal > 0 ? round(($allOnline / $allTotal) * 100, 1) : 0,
                    'offline_pct' => $allTotal > 0 ? round(($allOffline / $allTotal) * 100, 1) : 0,
                    'central_pct' => $allTotal > 0 ? round(($allCentral / $allTotal) * 100, 1) : 0,
                ],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Batch Ratio Stock Split across selected products.
     */
    public function ajaxBatchSplit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',
            'online_pct' => 'required|numeric|min:0|max:100',
            'offline_pct' => 'required|numeric|min:0|max:100',
            'central_pct' => 'required|numeric|min:0|max:100',
            'reason' => 'nullable|string',
        ]);

        $onlinePct = (float) $validated['online_pct'];
        $offlinePct = (float) $validated['offline_pct'];
        $centralPct = (float) $validated['central_pct'];

        $sumPct = $onlinePct + $offlinePct + $centralPct;
        if (abs($sumPct - 100.0) > 0.5) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'يجب أن يكون مجموع نسب التوزيع مساوياً لـ 100%.'
                    : 'Total allocation ratio must equal 100%.',
            ], 422);
        }

        $updatedProducts = [];

        DB::beginTransaction();
        try {
            foreach ($validated['product_ids'] as $pId) {
                $product = Product::findOrFail($pId);
                $onlineItem = InventoryService::getItem($product, 'online');
                $offlineItem = InventoryService::getItem($product, 'offline');
                $centralItem = InventoryService::getItem($product, 'central_wh');

                $totalStock = $onlineItem->current_stock + $offlineItem->current_stock + $centralItem->current_stock;

                if ($totalStock <= 0) {
                    continue;
                }

                // Compute targets
                $targetOnline = (int) round(($onlinePct / 100.0) * $totalStock);
                $targetOffline = (int) round(($offlinePct / 100.0) * $totalStock);
                $targetCentral = $totalStock - ($targetOnline + $targetOffline);
                if ($targetCentral < 0) {
                    $targetCentral = 0;
                    $targetOnline = $totalStock - $targetOffline;
                }

                $onlineItem->current_stock = $targetOnline;
                $onlineItem->available_stock = $targetOnline;
                $onlineItem->refreshStatus();
                $onlineItem->save();

                $offlineItem->current_stock = $targetOffline;
                $offlineItem->available_stock = $targetOffline;
                $offlineItem->refreshStatus();
                $offlineItem->save();

                $centralItem->current_stock = $targetCentral;
                $centralItem->available_stock = $targetCentral;
                $centralItem->refreshStatus();
                $centralItem->save();

                InventoryService::syncProductModelStock($product);

                // Record movement audit log
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'product_name_en' => $product->name_en,
                    'product_name_ar' => $product->name_ar ?? $product->name_en,
                    'sku' => $product->sku,
                    'movement_type' => 'Stock Transfer',
                    'from_location' => 'Multi-Hub Rebalance',
                    'to_location' => "Online: {$targetOnline}, POS: {$targetOffline}, Central: {$targetCentral}",
                    'quantity' => $totalStock,
                    'previous_qty' => $totalStock,
                    'new_qty' => $totalStock,
                    'date' => now()->toDateString(),
                    'time' => now()->format('H:i:s'),
                    'user' => auth()->user()?->name ?? 'Admin Allocator',
                    'note' => $validated['reason'] ?? "Batch split ratio ({$onlinePct}% Online / {$offlinePct}% POS / {$centralPct}% Central)",
                ]);

                $updatedProducts[] = [
                    'id' => $product->id,
                    'stock_online' => $targetOnline,
                    'stock_offline' => $targetOffline,
                    'stock_central' => $targetCentral,
                    'total_stock' => $totalStock,
                ];
            }

            DB::commit();

            $allOnline = (int) InventoryItem::where('location_id', 'online')->sum('current_stock');
            $allOffline = (int) InventoryItem::where('location_id', 'offline')->sum('current_stock');
            $allCentral = (int) InventoryItem::where('location_id', 'central_wh')->sum('current_stock');
            $allTotal = $allOnline + $allOffline + $allCentral;

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم تطبيق التوزيع بالنسب على المنتجات المحددة بنجاح!'
                    : 'Batch ratio allocation applied successfully!',
                'products' => $updatedProducts,
                'kpis' => [
                    'total_units' => $allTotal,
                    'online_units' => $allOnline,
                    'offline_units' => $allOffline,
                    'central_units' => $allCentral,
                    'online_pct' => $allTotal > 0 ? round(($allOnline / $allTotal) * 100, 1) : 0,
                    'offline_pct' => $allTotal > 0 ? round(($allOffline / $allTotal) * 100, 1) : 0,
                    'central_pct' => $allTotal > 0 ? round(($allCentral / $allTotal) * 100, 1) : 0,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dedicated Product Inventory Control Center & Quantity Management Hub.
     */
    public function control(Request $request): View
    {
        InventoryService::syncAllProductsInventory();

        $categories = Category::where('is_active', true)->orderBy('name_en')->get();
        $locations = Location::where('is_active', true)->get();
        if ($locations->isEmpty()) {
            $locations = collect(InventoryViewModel::locations());
        }

        $query = Product::with(['category', 'inventoryItems']);

        // Filter by Category
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Search query
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name_en', 'like', "%{$s}%")
                  ->orWhere('name_ar', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        $allProducts = $query->orderBy('name_en')->get();

        $onlineUnits = 0;
        $offlineUnits = 0;
        $centralUnits = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;
        $totalValuation = 0.0;

        $controlProducts = $allProducts->map(function ($product) use (&$onlineUnits, &$offlineUnits, &$centralUnits, &$lowStockCount, &$outOfStockCount, &$totalValuation) {
            $onlineItem = $product->inventoryItems->firstWhere('location_id', 'online');
            $offlineItem = $product->inventoryItems->firstWhere('location_id', 'offline');
            $centralItem = $product->inventoryItems->firstWhere('location_id', 'central_wh');

            $oStock = $onlineItem ? (int) $onlineItem->current_stock : (int) $product->stock_online;
            $fStock = $offlineItem ? (int) $offlineItem->current_stock : (int) $product->stock_offline;
            $cStock = $centralItem ? (int) $centralItem->current_stock : 50;

            $totalStock = $oStock + $fStock + $cStock;
            $threshold = (int) ($product->low_stock_threshold ?? 10);
            $cost = (float) ($product->cost_price ?? ($product->price * 0.4));

            $onlineUnits += $oStock;
            $offlineUnits += $fStock;
            $centralUnits += $cStock;
            $totalValuation += ($totalStock * $cost);

            $status = 'healthy';
            if ($totalStock <= 0) {
                $status = 'out_of_stock';
                $outOfStockCount++;
            } elseif ($oStock <= $threshold || $fStock <= $threshold) {
                $status = 'low_stock';
                $lowStockCount++;
            }

            return [
                'id' => $product->id,
                'name_en' => $product->name_en,
                'name_ar' => $product->name_ar ?? $product->name_en,
                'sku' => $product->sku,
                'barcode' => $product->barcode ?? '6281100' . $product->id,
                'category_id' => $product->category_id,
                'category_name' => app()->getLocale() === 'ar' ? ($product->category?->name_ar ?? $product->category?->name_en ?? 'طول العمر الخلوي') : ($product->category?->name_en ?? 'Cellular Longevity'),
                'price' => (float) $product->price,
                'cost_price' => $cost,
                'image' => Product::normalizeUrl($product->primary_image_url ?? $product->image),
                'stock_online' => $oStock,
                'stock_offline' => $fStock,
                'stock_central' => $cStock,
                'total_stock' => $totalStock,
                'valuation' => round($totalStock * $cost, 2),
                'low_stock_threshold' => $threshold,
                'status' => $status,
                'updated_at' => $product->updated_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
            ];
        });

        // Filter by Status if specified
        if ($request->filled('status') && $request->status !== 'all') {
            $statusFilter = $request->status;
            $controlProducts = $controlProducts->filter(function ($item) use ($statusFilter) {
                return $item['status'] === $statusFilter;
            })->values();
        }

        $totalUnits = $onlineUnits + $offlineUnits + $centralUnits;

        $kpis = [
            'total_units' => $totalUnits,
            'online_units' => $onlineUnits,
            'offline_units' => $offlineUnits,
            'central_units' => $centralUnits,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_valuation' => $totalValuation,
            'total_products' => Product::count(),
        ];

        return view('admin.inventory.control', [
            'products' => $controlProducts,
            'categories' => $categories,
            'locations' => $locations,
            'kpis' => $kpis,
            'selectedCategory' => $request->query('category_id', 'all'),
            'selectedStatus' => $request->query('status', 'all'),
            'search' => $request->query('search', ''),
        ]);
    }

    /**
     * AJAX 1-Click Quick Incremental/Decremental Stepper Adjustment.
     */
    public function ajaxQuickAdjust(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|string',
            'action' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'movement_type' => 'nullable|string|in:Stock In,Stock Out,Return,Damaged,Expired,Manual Adjustment',
            'reason' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];
        $isIncrease = $validated['action'] === 'increase';
        $delta = $isIncrease ? $qty : -$qty;

        $movementType = $validated['movement_type'] ?? ($isIncrease ? 'Stock In' : 'Manual Adjustment');
        $defaultReason = $isIncrease 
            ? (app()->getLocale() === 'ar' ? "زيادة رصيد مخزني سريع (+{$qty})" : "Quick Stock Incremental (+{$qty})")
            : (app()->getLocale() === 'ar' ? "تخفيض رصيد مخزني سريع (-{$qty})" : "Quick Stock Deduction (-{$qty})");
            
        $reason = $validated['reason'] ?? $defaultReason;
        if (!empty($validated['reference_number'])) {
            $reason = "[Ref: {$validated['reference_number']}] " . $reason;
        }

        try {
            $movement = InventoryService::adjustStock(
                product: $product,
                locationId: $validated['location_id'],
                quantityDelta: $delta,
                movementType: $movementType,
                reason: $reason,
                userName: auth()->user()?->name ?? 'Admin Controller'
            );

            $product->refresh();
            $onlineItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'online')->first();
            $offlineItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'offline')->first();
            $centralItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'central_wh')->first();

            $oStock = $onlineItem ? (int) $onlineItem->current_stock : (int) $product->stock_online;
            $fStock = $offlineItem ? (int) $offlineItem->current_stock : (int) $product->stock_offline;
            $cStock = $centralItem ? (int) $centralItem->current_stock : 0;
            $total = $oStock + $fStock + $cStock;

            $cost = (float) ($product->cost_price ?? ($product->price * 0.4));
            $threshold = (int) ($product->low_stock_threshold ?? 10);

            $status = 'healthy';
            if ($total <= 0) {
                $status = 'out_of_stock';
            } elseif ($oStock <= $threshold || $fStock <= $threshold) {
                $status = 'low_stock';
            }

            // Global KPI update
            $allOnline = (int) InventoryItem::where('location_id', 'online')->sum('current_stock');
            $allOffline = (int) InventoryItem::where('location_id', 'offline')->sum('current_stock');
            $allCentral = (int) InventoryItem::where('location_id', 'central_wh')->sum('current_stock');
            $allTotal = $allOnline + $allOffline + $allCentral;

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? "تم " . ($isIncrease ? "إضافة" : "خصم") . " {$qty} وحدة للمنتج ({$product->sku}) في " . ($validated['location_id'] === 'online' ? 'المتجر الإلكتروني' : ($validated['location_id'] === 'offline' ? 'المعرض وPOS' : 'المستودع المركزي')) . " بنجاح!"
                    : ($isIncrease ? "Added" : "Deducted") . " {$qty} units for {$product->sku} in [{$validated['location_id']}] successfully!",
                'product' => [
                    'id' => $product->id,
                    'stock_online' => $oStock,
                    'stock_offline' => $fStock,
                    'stock_central' => $cStock,
                    'total_stock' => $total,
                    'valuation' => round($total * $cost, 2),
                    'status' => $status,
                ],
                'movement' => [
                    'id' => $movement->id,
                    'movement_type' => $movement->movement_type,
                    'quantity' => $movement->quantity,
                    'time' => now()->format('H:i:s'),
                    'user' => $movement->user,
                    'note' => $movement->note,
                ],
                'kpis' => [
                    'total_units' => $allTotal,
                    'online_units' => $allOnline,
                    'offline_units' => $allOffline,
                    'central_units' => $allCentral,
                ],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX Batch Adjust Multiple Selected Products.
     */
    public function ajaxBatchAdjust(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',
            'location_id' => 'required|string',
            'movement_type' => 'required|string|in:Stock In,Stock Out,Return,Damaged,Expired,Manual Adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $qty = (int) $validated['quantity'];
        $type = $validated['movement_type'];
        $delta = in_array($type, ['Stock Out', 'Damaged', 'Expired'], true) ? -$qty : $qty;
        $note = $validated['reason'];
        if (!empty($validated['reference_number'])) {
            $note = "[Ref: {$validated['reference_number']}] " . $note;
        }

        $updatedProducts = [];

        DB::beginTransaction();
        try {
            foreach ($validated['product_ids'] as $pId) {
                $product = Product::findOrFail($pId);
                InventoryService::adjustStock(
                    product: $product,
                    locationId: $validated['location_id'],
                    quantityDelta: $delta,
                    movementType: $type,
                    reason: $note,
                    userName: auth()->user()?->name ?? 'Admin Controller'
                );

                $product->refresh();
                $onlineItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'online')->first();
                $offlineItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'offline')->first();
                $centralItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'central_wh')->first();

                $oStock = $onlineItem ? (int) $onlineItem->current_stock : (int) $product->stock_online;
                $fStock = $offlineItem ? (int) $offlineItem->current_stock : (int) $product->stock_offline;
                $cStock = $centralItem ? (int) $centralItem->current_stock : 0;
                $total = $oStock + $fStock + $cStock;

                $cost = (float) ($product->cost_price ?? ($product->price * 0.4));
                $threshold = (int) ($product->low_stock_threshold ?? 10);

                $status = 'healthy';
                if ($total <= 0) {
                    $status = 'out_of_stock';
                } elseif ($oStock <= $threshold || $fStock <= $threshold) {
                    $status = 'low_stock';
                }

                $updatedProducts[] = [
                    'id' => $product->id,
                    'stock_online' => $oStock,
                    'stock_offline' => $fStock,
                    'stock_central' => $cStock,
                    'total_stock' => $total,
                    'valuation' => round($total * $cost, 2),
                    'status' => $status,
                ];
            }

            DB::commit();

            $allOnline = (int) InventoryItem::where('location_id', 'online')->sum('current_stock');
            $allOffline = (int) InventoryItem::where('location_id', 'offline')->sum('current_stock');
            $allCentral = (int) InventoryItem::where('location_id', 'central_wh')->sum('current_stock');
            $allTotal = $allOnline + $allOffline + $allCentral;

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? "تم تطبيق التعديل الجماعي بنجاح على " . count($updatedProducts) . " منتج ({$type}: " . ($delta > 0 ? "+{$delta}" : "{$delta}") . " وحدة)."
                    : "Batch adjustment processed successfully on " . count($updatedProducts) . " products ({$type}: " . ($delta > 0 ? "+{$delta}" : "{$delta}") . " units).",
                'products' => $updatedProducts,
                'kpis' => [
                    'total_units' => $allTotal,
                    'online_units' => $allOnline,
                    'offline_units' => $allOffline,
                    'central_units' => $allCentral,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
