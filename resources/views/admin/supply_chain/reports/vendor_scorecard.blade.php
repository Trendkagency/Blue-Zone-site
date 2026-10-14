<x-layouts.admin 
    :pageTitle="__('supply_chain_reports.vendor_scorecard')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'بطاقة تقييم أداء الموردين، نسب الجودة المخبرية، والالتزام بمواعيد التوريد وأختام الحاويات' : 'Supplier Quality Ratings, QC Lot Acceptance Rates & Inbound Delivery Compliance'"
    :breadcrumbs="[__('suppliers.title') => route('admin.suppliers.index'), __('supply_chain_reports.vendor_scorecard') => '#']"
>
    <x-slot name="actions">
        <a href="{{ route('admin.supply-chain-reports.vendor-scorecard', ['export' => 'xlsx']) }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-file-excel mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}
        </a>
    </x-slot>

    <!-- Top KPI Ribbon -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'متوسط تقييم الموردين' : 'Average Vendor Rating' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($suppliers->avg('rating'), 1) }}%</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'موردون متفوقون (>=90%)' : 'Tier-1 Suppliers' }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $suppliers->filter(fn($s) => $s->rating >= 90)->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'موردون بحاجة للمراجعة' : 'Watchlist (<75%)' }}</p>
                <p class="text-2xl font-black text-rose-500 mt-1">{{ $suppliers->filter(fn($s) => $s->rating < 75)->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تدقيقات نشطة صالحة' : 'Audits in Good Standing' }}</p>
                <p class="text-2xl font-black text-sky-600 mt-1">{{ $suppliers->filter(fn($s) => $s->audit_valid_until && $s->audit_valid_until->isFuture())->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-certificate"></i>
            </div>
        </div>
    </div>

    <!-- Scorecard Table -->
    <x-admin.table-toolbar 
        table="#scorecardTable" 
        :title="app()->getLocale() === 'ar' ? 'تقرير تقييم الموردين cGMP' : 'cGMP Vendor Scorecard & Audit Standing'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table text-xs" id="scorecardTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('suppliers.code') }}</th>
                        <th data-sort-type="text">{{ __('suppliers.name') }}</th>
                        <th data-sort-type="text">{{ __('suppliers.country') }}</th>
                        <th data-sort-type="number">{{ __('suppliers.quality_rating') }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'نسبة نجاح QC' : 'QC Pass Rate' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'الالتزام بمواعيد الشحن' : 'On-Time Delivery' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'إجمالي الأوامر' : 'Total POs' }}</th>
                        <th data-sort-type="date">{{ __('suppliers.audit_valid_until') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="hover:underline">
                                    {{ $supplier->code }}
                                </a>
                            </td>
                            <td class="font-bold text-slate-800 dark:text-white">
                                {{ $supplier->name }}
                            </td>
                            <td>{{ $supplier->country }}</td>
                            <td>
                                <span class="font-bold font-mono text-sm {{ $supplier->rating >= 90 ? 'text-emerald-600' : ($supplier->rating >= 75 ? 'text-amber-500' : 'text-rose-600') }}">
                                    {{ number_format($supplier->rating, 1) }}%
                                </span>
                            </td>
                            <td class="font-mono font-bold text-emerald-600">
                                {{ number_format($supplier->computed_qc_pass_rate ?? 100.0, 1) }}%
                            </td>
                            <td class="font-mono font-bold text-sky-600">
                                {{ number_format($supplier->computed_on_time_rate ?? 95.0, 1) }}%
                            </td>
                            <td class="font-mono font-bold text-slate-700 dark:text-gray-300">
                                {{ $supplier->purchase_orders_count ?? $supplier->purchaseOrders()->count() }}
                            </td>
                            <td class="font-mono">
                                @if($supplier->audit_valid_until)
                                    <span class="{{ $supplier->audit_valid_until->isPast() ? 'text-rose-600 font-bold' : '' }}">
                                        {{ $supplier->audit_valid_until->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
