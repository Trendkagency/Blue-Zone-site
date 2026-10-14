<x-layouts.admin 
    :pageTitle="__('supply_chain_reports.scrap_variance')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحليل نسب الفاقد والتلف التصنيعي، انحرافات التشغيل، وتتبع الهدر في غرف الخلط والتعبئة' : 'Manufacturing Scrap Variance, Production Yield Discrepancies & Packaging Line Loss Analysis'"
    :breadcrumbs="[__('production.title') => route('admin.work-orders.index'), __('supply_chain_reports.scrap_variance') => '#']"
>
    <x-slot name="actions">
        <a href="{{ route('admin.supply-chain-reports.scrap-variance', ['export' => 'xlsx']) }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-file-excel mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}
        </a>
    </x-slot>

    <!-- Top KPI Ribbon -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'أوامر التصنيع المكتملة' : 'Completed Work Orders' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $workOrders->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-industry"></i>
            </div>
        </div>
        <div class="card p-4 border border-rose-100 dark:border-rose-950 bg-rose-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-700 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي العبوات التالفة' : 'Total Scrap Units' }}</p>
                <p class="text-2xl font-black text-rose-600 mt-1 font-mono">{{ number_format($workOrders->sum('scrap_quantity')) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-trash-can"></i>
            </div>
        </div>
        <div class="card p-4 border border-emerald-100 dark:border-emerald-950 bg-emerald-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-700 uppercase">{{ app()->getLocale() === 'ar' ? 'متوسط كفاءة العائد' : 'Average Yield Efficiency' }}</p>
                @php
                    $targetSum = $workOrders->sum('target_quantity');
                    $actualSum = $workOrders->sum('actual_quantity');
                    $yieldPct = $targetSum > 0 ? round(($actualSum / $targetSum) * 100, 1) : 100;
                @endphp
                <p class="text-2xl font-black text-emerald-700 mt-1 font-mono">{{ $yieldPct }}%</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
        <div class="card p-4 border border-amber-100 dark:border-amber-950 bg-amber-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-700 uppercase">{{ app()->getLocale() === 'ar' ? 'متوسط نسبة الهدر' : 'Average Scrap Rate' }}</p>
                @php
                    $scrapSum = $workOrders->sum('scrap_quantity');
                    $scrapRate = ($actualSum + $scrapSum) > 0 ? round(($scrapSum / ($actualSum + $scrapSum)) * 100, 2) : 0;
                @endphp
                <p class="text-2xl font-black text-amber-700 mt-1 font-mono">{{ $scrapRate }}%</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-percent"></i>
            </div>
        </div>
    </div>

    <!-- Scrap Table -->
    <x-admin.table-toolbar 
        table="#scrapTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل انحرافات الإنتاج والتلف' : 'Work Order Scrap & Yield Variance Log'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table text-xs" id="scrapTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('production.wo_number') }}</th>
                        <th data-sort-type="text">{{ __('production.product') }}</th>
                        <th data-sort-type="text">{{ __('production.batch_number') }}</th>
                        <th data-sort-type="number">{{ __('production.target_quantity') }}</th>
                        <th data-sort-type="number">{{ __('production.actual_quantity') }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'التالف' : 'Scrap Units' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'نسبة الهدر' : 'Scrap %' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'نسبة العائد' : 'Yield %' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workOrders as $wo)
                        @php
                            $target = $wo->target_quantity;
                            $actual = $wo->actual_quantity;
                            $scrap = $wo->scrap_quantity;
                            $yPct = $target > 0 ? round(($actual / $target) * 100, 1) : 0;
                            $sPct = ($actual + $scrap) > 0 ? round(($scrap / ($actual + $scrap)) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                <a href="{{ route('admin.work-orders.show', $wo) }}" class="hover:underline">
                                    {{ $wo->wo_number }}
                                </a>
                            </td>
                            <td class="font-bold text-slate-800 dark:text-white">
                                {{ $wo->product?->name_en }}
                            </td>
                            <td class="font-mono text-purple-600 font-bold">{{ $wo->product_batch_number }}</td>
                            <td class="font-mono">{{ number_format($target) }}</td>
                            <td class="font-mono font-bold text-emerald-600">{{ number_format($actual) }}</td>
                            <td class="font-mono font-bold text-rose-600">{{ number_format($scrap) }}</td>
                            <td class="font-mono {{ $sPct > 2 ? 'text-rose-600 font-bold' : 'text-gray-500' }}">
                                {{ $sPct }}%
                            </td>
                            <td class="font-mono font-bold {{ $yPct >= 98 ? 'text-emerald-600' : 'text-amber-500' }}">
                                {{ $yPct }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
