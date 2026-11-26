<x-layouts.admin
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل تشغيلات المنتجات التامة والصلاحية (FEFO)' : 'Finished Product Batches & Expiry (FEFO)'"
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة تواريخ الإنتاج والانتهاء، الأولوية الآلية للصرف في المبيعات (FEFO)، وإدارة تشغيلات المخازن' : 'Lot-Level Manufacturing & Expiry Dates, Automated Sales FEFO Pick Queue & Shelf-Life Controls'"
    :breadcrumbs="[app()->getLocale() === 'ar' ? 'تشغيلات المنتجات التامة' : 'Finished Product Batches' => route('admin.product-batches.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-flask text-indigo-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'تشغيلات المواد الخام' : 'Raw Material Batches' }}</span>
        </a>
        <a href="{{ route('admin.supply-chain-reports.valuation-expiry') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-clock-rotate-left text-rose-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'تقرير مخاطر الصلاحية' : 'Expiry Risk Report' }}</span>
        </a>
        <button type="button" onclick="document.getElementById('registerBatchModal').classList.remove('hidden')" class="btn btn-primary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-plus"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'تسجيل تشغيلة منتج' : 'Register Product Batch' }}</span>
        </button>
    </x-slot>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي التشغيلات' : 'Total Product Batches' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $kpis['total_batches'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'وحدات صالحة للبيع' : 'Unexpired Available Units' }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($kpis['approved_units']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-shield-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تنتهي خلال <60 يوم' : 'Expiring Soon (<60d)' }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $kpis['expiring_soon'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'منتهية برصيد فعلي' : 'Expired w/ Stock' }}</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $kpis['expired_count'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.product-batches.index') }}" class="flex items-center justify-between gap-3 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[240px] max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم التشغيلة، المنتج، أو SKU...' : 'Search batch #, product, or SKU...' }}">
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <select name="product_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المنتجات' : 'All Products' }}</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ (string) request('product_id') === (string) $p->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' && $p->name_ar ? $p->name_ar : $p->name_en }} ({{ $p->sku }})
                        </option>
                    @endforeach
                </select>
                <select name="location_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل المستودعات' : 'All Hubs' }}</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') === $loc->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' && $loc->name_ar ? $loc->name_ar : $loc->name_en }}
                        </option>
                    @endforeach
                </select>
                <select name="expiry_filter" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل حالات الصلاحية' : 'All Expiry Horizons' }}</option>
                    <option value="expired" {{ request('expiry_filter') === 'expired' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'منتهي الصلاحية' : 'Expired' }}</option>
                    <option value="critical" {{ request('expiry_filter') === 'critical' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'حرج (≤ 30 يوم)' : 'Critical (≤ 30d)' }}</option>
                    <option value="warning" {{ request('expiry_filter') === 'warning' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'تحذير (31 - 90 يوم)' : 'Warning (31-90d)' }}</option>
                    <option value="valid" {{ request('expiry_filter') === 'valid' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'سليم (> 90 يوم)' : 'Optimal (> 90d)' }}</option>
                </select>
                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="quarantine" {{ request('status') === 'quarantine' ? 'selected' : '' }}>Quarantine</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm font-bold">{{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}</button>
                @if(request()->hasAny(['search', 'product_id', 'location_id', 'expiry_filter', 'status']))
                    <a href="{{ route('admin.product-batches.index') }}" class="btn btn-ghost btn-sm text-rose-500">{{ app()->getLocale() === 'ar' ? 'مسح' : 'Clear' }}</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Product Batches FEFO Table -->
    <div class="card border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table text-xs w-full">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'رقم التشغيلة' : 'Batch Number' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المنتج' : 'Finished Product' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المصدر' : 'Source' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المستودع' : 'Hub' }}</th>
                        <th class="text-center">{{ app()->getLocale() === 'ar' ? 'تاريخ الإنتاج' : 'Mfg Date' }}</th>
                        <th class="text-center">{{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                        <th class="text-right">{{ app()->getLocale() === 'ar' ? 'المتاح / الفعلي' : 'Avail / Initial' }}</th>
                        <th class="text-right">{{ app()->getLocale() === 'ar' ? 'تكلفة الوحدة' : 'Unit Cost' }}</th>
                        <th class="text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th class="text-center">{{ app()->getLocale() === 'ar' ? 'تعديل الصلاحية' : 'Action' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        @php $badge = $batch->expiry_badge; @endphp
                        <tr>
                            <td class="font-mono font-bold text-slate-900 dark:text-white">
                                {{ $batch->batch_number }}
                                @if($batch->supplier_lot)
                                    <span class="block text-[10px] text-gray-400">Lot: {{ $batch->supplier_lot }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $batch->product_id) }}" class="font-bold text-sky-600 hover:underline">
                                    {{ app()->getLocale() === 'ar' && $batch->product?->name_ar ? $batch->product->name_ar : ($batch->product?->name_en ?? "Product #{$batch->product_id}") }}
                                </a>
                                <span class="block font-mono text-[10px] text-gray-400">{{ $batch->product?->sku }}</span>
                            </td>
                            <td class="text-[11px]">
                                @if($batch->workOrder)
                                    <a href="{{ route('admin.work-orders.show', $batch->workOrder) }}" class="badge badge-indigo font-mono">WO {{ $batch->workOrder->order_number }}</a>
                                @elseif($batch->goodsReceipt)
                                    <a href="{{ route('admin.goods-receipts.show', $batch->goodsReceipt) }}" class="badge badge-sky font-mono">GRN {{ $batch->goodsReceipt->grn_number }}</a>
                                @else
                                    <span class="text-gray-400">{{ app()->getLocale() === 'ar' ? 'مخزون / فاتورة مباشرة' : 'Direct / Sync' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary font-mono">{{ $batch->location?->name_en ?? $batch->location_id }}</span>
                            </td>
                            <td class="text-center font-mono text-gray-500">
                                {{ $batch->manufacture_date?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td class="text-center">
                                <span class="font-mono font-black {{ $badge['color'] === 'rose' ? 'text-rose-600' : ($badge['color'] === 'orange' ? 'text-orange-600' : ($badge['color'] === 'amber' ? 'text-amber-600' : 'text-emerald-600')) }}">
                                    {{ $batch->expiry_date?->format('Y-m-d') ?? '—' }}
                                </span>
                                <span class="block text-[10px] text-gray-400">
                                    {{ $badge['label_' . (app()->getLocale() === 'ar' ? 'ar' : 'en')] }}
                                </span>
                            </td>
                            <td class="text-right font-mono font-bold">
                                <span class="{{ $batch->available_qty > 0 ? 'text-emerald-600' : 'text-gray-400' }}">{{ $batch->available_qty }}</span>
                                <span class="text-gray-400 text-[10px]">/ {{ $batch->initial_qty }}</span>
                            </td>
                            <td class="text-right font-mono">
                                @currency($batch->unit_cost)
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $batch->status === 'approved' ? 'emerald' : ($batch->status === 'quarantine' ? 'amber' : 'rose') }}">
                                    {{ ucfirst($batch->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $editBatchJson = [
                                        'id' => $batch->id,
                                        'batch_number' => $batch->batch_number,
                                        'manufacture_date' => $batch->manufacture_date?->format('Y-m-d'),
                                        'expiry_date' => $batch->expiry_date?->format('Y-m-d'),
                                        'supplier_lot' => $batch->supplier_lot,
                                        'storage_bin' => $batch->storage_bin,
                                        'status' => $batch->status,
                                    ];
                                @endphp
                                <button type="button"
                                    onclick='openEditProductBatchModal(@json($editBatchJson))'
                                    class="btn btn-xs btn-ghost text-sky-600 hover:text-sky-700 font-bold">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-8 text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد تشغيلات منتجات مسجلة.' : 'No finished product batches found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($batches->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    <!-- Register New Product Batch Modal -->
    <div id="registerBatchModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="card max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">
                    <i class="fa-solid fa-boxes-packing text-sky-500 mr-1.5"></i>
                    {{ app()->getLocale() === 'ar' ? 'تسجيل تشغيلة منتج وتاريخ صلاحية' : 'Register Product Batch & Expiry' }}
                </h3>
                <button type="button" onclick="document.getElementById('registerBatchModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form method="POST" action="{{ route('admin.product-batches.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'المنتج *' : 'Product *' }}</label>
                        <select name="product_id" class="form-select text-sm w-full" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ app()->getLocale() === 'ar' && $p->name_ar ? $p->name_ar : $p->name_en }} ({{ $p->sku }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'المستودع *' : 'Warehouse Hub *' }}</label>
                        <select name="location_id" class="form-select text-sm w-full" required>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ app()->getLocale() === 'ar' && $loc->name_ar ? $loc->name_ar : $loc->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'رقم التشغيلة (تلقائي لو فارغ)' : 'Batch # (auto if blank)' }}</label>
                        <input type="text" name="batch_number" class="form-control text-sm font-mono" placeholder="LOT-FG-{{ date('Ym') }}-XXXX">
                    </div>
                    <div>
                        <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'الكمية *' : 'Quantity *' }}</label>
                        <input type="number" name="quantity" min="1" value="10" class="form-control text-sm font-mono font-bold" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'تاريخ الإنتاج' : 'Manufacture Date' }}</label>
                        <input type="date" name="manufacture_date" value="{{ now()->toDateString() }}" class="form-control text-sm font-mono">
                    </div>
                    <div>
                        <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'تاريخ الصلاحية *' : 'Expiry Date *' }}</label>
                        <input type="date" name="expiry_date" value="{{ now()->addMonths(24)->toDateString() }}" class="form-control text-sm font-mono" required>
                    </div>
                </div>
                <div>
                    <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'بيان التسجيل' : 'Notes / Reason' }}</label>
                    <input type="text" name="reason" class="form-control text-sm" placeholder="Batch intake audit note">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" onclick="document.getElementById('registerBatchModal').classList.add('hidden')" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary btn-sm font-bold">{{ app()->getLocale() === 'ar' ? 'حفظ التشغيلة' : 'Save Batch' }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Batch Expiry Modal -->
    <div id="editProductBatchModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="card max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">
                    <i class="fa-solid fa-calendar-pen text-amber-500 mr-1.5"></i>
                    <span id="editBatchModalTitle">Edit Batch Expiry</span>
                </h3>
                <button type="button" onclick="document.getElementById('editProductBatchModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form id="editProductBatchForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'تاريخ الإنتاج' : 'Manufacture Date' }}</label>
                            <input type="date" name="manufacture_date" id="ebMfgDate" class="form-control text-sm font-mono">
                        </div>
                        <div>
                            <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'تاريخ الصلاحية *' : 'Expiry Date *' }}</label>
                            <input type="date" name="expiry_date" id="ebExpDate" class="form-control text-sm font-mono" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'تشغيلة المورد' : 'Supplier Lot' }}</label>
                            <input type="text" name="supplier_lot" id="ebSupplierLot" class="form-control text-sm font-mono">
                        </div>
                        <div>
                            <label class="form-label text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</label>
                            <select name="status" id="ebStatus" class="form-select text-sm">
                                <option value="approved">Approved</option>
                                <option value="quarantine">Quarantine</option>
                                <option value="expired">Expired</option>
                                <option value="recalled">Recalled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" onclick="document.getElementById('editProductBatchModal').classList.add('hidden')" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary btn-sm font-bold">{{ app()->getLocale() === 'ar' ? 'تحديث الصلاحية' : 'Update Expiry' }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.openEditProductBatchModal = function(batch) {
            const form = document.getElementById('editProductBatchForm');
            form.action = '/admin/product-batches/' + batch.id;
            document.getElementById('editBatchModalTitle').innerText = batch.batch_number;
            document.getElementById('ebMfgDate').value = batch.manufacture_date || '';
            document.getElementById('ebExpDate').value = batch.expiry_date || '';
            document.getElementById('ebSupplierLot').value = batch.supplier_lot || '';
            document.getElementById('ebStatus').value = batch.status || 'approved';
            document.getElementById('editProductBatchModal').classList.remove('hidden');
        };
    </script>
</x-layouts.admin>
