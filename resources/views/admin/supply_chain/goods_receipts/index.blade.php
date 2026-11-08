<x-layouts.admin 
    :pageTitle="__('goods_receipts.title')" 
    :pageSubtitle="__('goods_receipts.subtitle')"
    :breadcrumbs="[__('goods_receipts.title') => route('admin.goods-receipts.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.goods-receipts.create') }}" class="btn btn-primary font-bold shadow-sm inline-flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span>{{ __('goods_receipts.new_grn') }}</span>
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('goods_receipts.total_grns') }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($kpis['total_grns'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('goods_receipts.received_quarantined') }}</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($kpis['received'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('goods_receipts.draft_receipts') }}</p>
                <p class="text-2xl font-black text-amber-500 dark:text-amber-400 mt-1">{{ number_format($kpis['drafts'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 dark:text-amber-400 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-file-pen"></i>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between transition hover:shadow-md">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('goods_receipts.damaged_seals') }}</p>
                <p class="text-2xl font-black text-rose-500 dark:text-rose-400 mt-1">{{ number_format($kpis['broken_seals'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-500 dark:text-rose-400 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.goods-receipts.index') }}" class="flex items-center justify-between gap-3 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm w-full" placeholder="{{ __('goods_receipts.search_placeholder') }}">
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto min-w-[150px]">
                    <option value="">{{ __('goods_receipts.all_statuses') }}</option>
                    @foreach(\App\Enums\GoodsReceiptStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <select name="supplier_id" onchange="this.form.submit()" class="form-select text-sm w-auto min-w-[180px]">
                    <option value="">{{ __('goods_receipts.all_suppliers') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }} ({{ $supplier->code }})
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>{{ __('goods_receipts.filter') }}</span>
                </button>

                @if(request()->hasAny(['search', 'status', 'supplier_id']))
                    <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-ghost btn-sm text-gray-500 hover:text-rose-600 inline-flex items-center gap-1">
                        <i class="fa-solid fa-xmark"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إلغاء التصفية' : 'Reset' }}</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- GRN Table -->
    <x-admin.table-toolbar 
        table="#grnTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل إشعارات الاستلام بالرصيف' : 'Goods Receipts (GRN) Log'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="table-responsive">
            <table class="table w-full text-sm" id="grnTable">
                <thead>
                    <tr class="bg-gray-50/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300">
                        <th class="whitespace-nowrap px-4 py-3" data-sort-type="text">{{ __('goods_receipts.grn_number') }}</th>
                        <th class="whitespace-nowrap px-4 py-3" data-sort-type="text">{{ __('goods_receipts.po_number') }}</th>
                        <th class="whitespace-nowrap px-4 py-3" data-sort-type="text">{{ __('goods_receipts.supplier') }}</th>
                        <th class="whitespace-nowrap px-4 py-3" data-sort-type="date">{{ __('goods_receipts.received_date') }}</th>
                        <th class="whitespace-nowrap px-4 py-3" data-sort-type="text">{{ __('goods_receipts.temperature_reading') }}</th>
                        <th class="whitespace-nowrap px-4 py-3" data-sort-type="text">{{ __('goods_receipts.seal_intact') }}</th>
                        <th class="whitespace-nowrap px-4 py-3" data-sort-type="text">{{ __('goods_receipts.status') }}</th>
                        <th class="whitespace-nowrap px-4 py-3 text-center" data-no-sort data-no-export>{{ __('goods_receipts.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($goodsReceipts as $grn)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="whitespace-nowrap px-4 py-3.5 font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.goods-receipts.show', $grn) }}" class="inline-flex items-center gap-1.5 hover:underline">
                                    <i class="fa-solid fa-truck-ramp-box opacity-70 text-xs"></i>
                                    <span>{{ $grn->grn_number }}</span>
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 font-mono text-xs">
                                @if($grn->purchaseOrder)
                                    <a href="{{ route('admin.purchase-orders.show', $grn->purchaseOrder) }}" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                                        <i class="fa-solid fa-file-invoice text-[11px] opacity-70"></i>
                                        <span>{{ $grn->purchaseOrder->po_number }}</span>
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ $grn->supplier?->name ?? '—' }}
                                </div>
                                @if($grn->supplier?->code)
                                    <span class="text-[11px] font-mono text-gray-400 block">{{ $grn->supplier->code }}</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-400">
                                {{ $grn->received_date?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                @if($grn->temperature_reading !== null)
                                    <span class="inline-flex items-center gap-1 text-xs font-mono font-bold px-2 py-0.5 rounded {{ ($grn->temperature_within_range ?? true) ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300' }}">
                                        <i class="fa-solid fa-temperature-half text-[11px]"></i>
                                        <span>{{ number_format($grn->temperature_reading, 1) }} °C</span>
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs font-mono">N/A</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                @if($grn->seal_intact)
                                    <span class="badge badge-emerald text-xs whitespace-nowrap inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-check"></i>
                                        <span>{{ __('goods_receipts.intact') }}</span>
                                    </span>
                                @else
                                    <span class="badge badge-rose text-xs whitespace-nowrap inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <span>{{ __('goods_receipts.broken') }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                <span class="badge {{ $grn->status->badgeClass() }} text-xs whitespace-nowrap">
                                    {{ $grn->status->label() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-center">
                                <a href="{{ route('admin.goods-receipts.show', $grn) }}" class="btn btn-ghost btn-sm text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/40" title="{{ __('goods_receipts.view_grn') }}">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-slate-800 text-gray-400 flex items-center justify-center text-2xl mx-auto mb-3">
                                    <i class="fa-solid fa-truck-ramp-box"></i>
                                </div>
                                <p class="font-semibold">{{ __('goods_receipts.no_records') }}</p>
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
