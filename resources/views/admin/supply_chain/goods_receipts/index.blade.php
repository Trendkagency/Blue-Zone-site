<x-layouts.admin 
    :pageTitle="__('goods_receipts.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إشعارات الاستلام بالرصيف، تدقيق سلامة الأختام ومسجلات الحرارة، وإيداع التشغيلات في الحجر الصحي' : 'Inbound Dock Receipts, Seal & Temperature Logging, Automatic Quarantine Isolation'"
    :breadcrumbs="[__('goods_receipts.title') => route('admin.goods-receipts.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.goods-receipts.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('goods_receipts.new_grn') }}
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي إشعارات الاستلام' : 'Total GRNs' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $kpis['total_grns'] ?? $goodsReceipts->total() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تم الاستلام وإيداع الحجر' : 'Received & Quarantined' }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $kpis['received'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'مسودات قيد المعالجة' : 'Draft Receipts' }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $kpis['drafts'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-file-pen"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'اختراق الأختام أو تلف' : 'Damaged / Broken Seals' }}</p>
                <p class="text-2xl font-black text-rose-500 mt-1">{{ $kpis['broken_seals'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.goods-receipts.index') }}" class="flex items-center justify-between gap-4 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم الإشعار GRN-YYYY-XXXX...' : 'Search GRN number GRN-YYYY-XXXX...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    @foreach(\App\Enums\GoodsReceiptStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <select name="supplier_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الموردين' : 'All Suppliers' }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- GRN Table -->
    <x-admin.table-toolbar 
        table="#grnTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل إشعارات الاستلام بالرصيف' : 'Goods Receipts (GRN) Log'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table" id="grnTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('goods_receipts.grn_number') }}</th>
                        <th data-sort-type="text">{{ __('goods_receipts.po_number') }}</th>
                        <th data-sort-type="text">{{ __('goods_receipts.supplier') }}</th>
                        <th data-sort-type="date">{{ __('goods_receipts.received_date') }}</th>
                        <th data-sort-type="text">{{ __('goods_receipts.temperature_reading') }}</th>
                        <th data-sort-type="text">{{ __('goods_receipts.seal_intact') }}</th>
                        <th data-sort-type="text">{{ __('goods_receipts.status') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($goodsReceipts as $grn)
                        <tr>
                            <td class="font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.goods-receipts.show', $grn) }}" class="hover:underline">
                                    {{ $grn->grn_number }}
                                </a>
                            </td>
                            <td class="font-mono text-xs">
                                <a href="{{ route('admin.purchase-orders.show', $grn->purchaseOrder) }}" class="text-indigo-600 hover:underline">
                                    {{ $grn->purchaseOrder?->po_number }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ $grn->supplier?->name }}
                                </div>
                            </td>
                            <td class="font-mono text-xs">
                                {{ $grn->received_date?->format('Y-m-d') }}
                            </td>
                            <td class="font-mono text-xs">
                                {{ $grn->temperature_reading !== null ? $grn->temperature_reading . ' °C' : 'N/A' }}
                            </td>
                            <td>
                                @if($grn->seal_intact)
                                    <span class="badge badge-emerald text-xs">
                                        <i class="fa-solid fa-check mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'سليم' : 'Intact' }}
                                    </span>
                                @else
                                    <span class="badge badge-rose text-xs">
                                        <i class="fa-solid fa-triangle-exclamation mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'مكسور' : 'Broken' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $grn->status->badgeClass() }} text-xs">
                                    {{ $grn->status->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.goods-receipts.show', $grn) }}" class="btn btn-ghost btn-sm text-sky-600" title="{{ app()->getLocale() === 'ar' ? 'عرض الإشعار' : 'View GRN' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <i class="fa-solid fa-truck-ramp-box text-3xl mb-2 text-gray-400"></i>
                                <p>{{ app()->getLocale() === 'ar' ? 'لا توجد إشعارات استلام مطابقة.' : 'No goods receipts found matching filter.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($goodsReceipts->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $goodsReceipts->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
