<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProductBatchController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('batches.view') || auth()->user()?->hasRole(['Super Admin', 'super_admin', 'Admin']), 403);

        $query = ProductBatch::with(['product', 'location', 'workOrder', 'goodsReceipt']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                    ->orWhere('supplier_lot', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($productId = $request->query('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($locationId = $request->query('location_id')) {
            $query->where('location_id', $locationId);
        }

        if ($expiryFilter = $request->query('expiry_filter')) {
            $today = now()->toDateString();
            if ($expiryFilter === 'expired') {
                $query->whereDate('expiry_date', '<=', $today);
            } elseif ($expiryFilter === 'critical') {
                $query->whereDate('expiry_date', '>', $today)
                    ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString());
            } elseif ($expiryFilter === 'warning') {
                $query->whereDate('expiry_date', '>', now()->addDays(30)->toDateString())
                    ->whereDate('expiry_date', '<=', now()->addDays(90)->toDateString());
            } elseif ($expiryFilter === 'valid') {
                $query->whereDate('expiry_date', '>', now()->addDays(90)->toDateString());
            }
        }

        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $batches = $query->orderBy('expiry_date', 'asc')->orderBy('id', 'asc')->paginate($perPage)->withQueryString();

        $products = Product::orderBy('name_en')->get();
        $locations = Location::where('is_active', true)->get();

        $kpis = [
            'total_batches' => ProductBatch::count(),
            'approved_units' => (int) ProductBatch::where('status', 'approved')->sum('available_qty'),
            'expiring_soon' => ProductBatch::where('status', 'approved')
                ->whereDate('expiry_date', '>', now()->toDateString())
                ->whereDate('expiry_date', '<=', now()->addDays(60)->toDateString())
                ->count(),
            'expired_count' => ProductBatch::where(function ($q) {
                $q->where('status', 'expired')
                    ->orWhereDate('expiry_date', '<=', now()->toDateString());
            })->where('current_qty', '>', 0)->count(),
        ];

        return view('admin.supply_chain.product_batches.index', compact('batches', 'products', 'locations', 'kpis'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()?->can('batches.adjust') || auth()->user()?->hasRole(['Super Admin', 'super_admin', 'Admin']), 403);

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'location_id' => ['required', 'string', 'max:64'],
            'batch_number' => ['nullable', 'string', 'max:64', 'unique:product_batches,batch_number'],
            'supplier_lot' => ['nullable', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'manufacture_date' => ['nullable', 'date'],
            'expiry_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];
        $mfg = !empty($validated['manufacture_date'])
            ? Carbon::parse($validated['manufacture_date'])->toDateString()
            : now()->toDateString();

        InventoryService::adjustStock(
            product: $product,
            locationId: $validated['location_id'],
            quantityDelta: $qty,
            movementType: 'Stock In',
            reason: $validated['reason'] ?? 'Registered Finished Product Batch',
            userName: auth()->user()?->name ?? 'Admin',
            sourceOrTarget: 'Batch Registry',
            syncBatch: true,
            batchMeta: [
                'batch_number' => $validated['batch_number'] ?? null,
                'supplier_lot' => $validated['supplier_lot'] ?? null,
                'manufacture_date' => $mfg,
                'expiry_date' => $validated['expiry_date'],
                'unit_cost' => $validated['unit_cost'] ?? $product->cost_price,
            ]
        );

        $latest = $product->batches()->latest('id')->first();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم تسجيل التشغيلة وتحديث رصيد المخزون بنجاح.'
                    : 'Finished product batch registered and inventory synchronized.',
                'batch' => $latest,
            ]);
        }

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تسجيل التشغيلة وتاريخ الصلاحية بنجاح.'
            : 'Product batch & expiry date registered successfully.');
    }

    public function update(Request $request, ProductBatch $productBatch): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()?->can('batches.adjust') || auth()->user()?->hasRole(['Super Admin', 'super_admin', 'Admin']), 403);

        $validated = $request->validate([
            'manufacture_date' => ['nullable', 'date'],
            'expiry_date' => ['required', 'date'],
            'supplier_lot' => ['nullable', 'string', 'max:100'],
            'storage_bin' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'in:approved,quarantine,expired,recalled'],
        ]);

        $productBatch->manufacture_date = $validated['manufacture_date'] ?? $productBatch->manufacture_date;
        $productBatch->expiry_date = $validated['expiry_date'];
        if (array_key_exists('supplier_lot', $validated)) {
            $productBatch->supplier_lot = $validated['supplier_lot'];
        }
        if (array_key_exists('storage_bin', $validated)) {
            $productBatch->storage_bin = $validated['storage_bin'];
        }
        if (!empty($validated['status'])) {
            $productBatch->status = $validated['status'];
            if ($validated['status'] === 'quarantine' && $productBatch->available_qty > 0) {
                $productBatch->quarantine_qty += $productBatch->available_qty;
                $productBatch->available_qty = 0;
            } elseif ($validated['status'] === 'approved' && $productBatch->quarantine_qty > 0 && Carbon::parse($validated['expiry_date'])->isFuture()) {
                $productBatch->available_qty += $productBatch->quarantine_qty;
                $productBatch->quarantine_qty = 0;
            }
        }

        $productBatch->assertInvariant();
        $productBatch->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم تحديث بيانات صلاحية التشغيلة بنجاح.'
                    : 'Batch expiry date and metadata updated successfully.',
                'batch' => [
                    'id' => $productBatch->id,
                    'batch_number' => $productBatch->batch_number,
                    'manufacture_date' => $productBatch->manufacture_date?->format('Y-m-d'),
                    'expiry_date' => $productBatch->expiry_date?->format('Y-m-d'),
                    'status' => $productBatch->status,
                    'expiry_badge' => $productBatch->expiry_badge,
                ],
            ]);
        }

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تحديث تاريخ الصلاحية وحالة التشغيلة بنجاح.'
            : 'Batch expiry date and status updated successfully.');
    }
}
