<x-layouts.admin 
    :pageTitle="__('supply_chain_reports.valuation_expiry')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تقييم رأس المال المستثمر في المخزون وحساب المخاطر المالية للتشغيلات المعرضة للانتهاء' : 'Raw Material Inventory Capital Valuation & Expiry Risk Analysis (30 / 60 / 90 Days)'"
    :breadcrumbs="[__('raw_materials.title') => route('admin.raw-materials.index'), __('supply_chain_reports.valuation_expiry') => '#']"
>
    <x-slot name="actions">
        <a href="{{ route('admin.supply-chain-reports.valuation-expiry', ['export' => 'xlsx']) }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-file-excel mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}
        </a>
    </x-slot>

    <!-- Capital Risk KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي قيمة المخزون' : 'Total Inventory Value' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1 font-mono">{{ number_format($totalValuation, 2) }} <span class="text-xs font-normal">AED</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-vault"></i>
            </div>
        </div>
        <div class="card p-4 border border-rose-100 dark:border-rose-950 bg-rose-50/20 dark:bg-rose-950/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase">{{ app()->getLocale() === 'ar' ? 'رأس مال حرج (<30 يوم)' : 'Capital Risk (<30d)' }}</p>
                <p class="text-2xl font-black text-rose-600 mt-1 font-mono">{{ number_format($risk30, 2) }} <span class="text-xs font-normal">AED</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-fire"></i>
            </div>
        </div>
        <div class="card p-4 border border-amber-100 dark:border-amber-950 bg-amber-50/20 dark:bg-amber-950/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase">{{ app()->getLocale() === 'ar' ? 'معرض للخطر (<60 يوم)' : 'Capital Risk (<60d)' }}</p>
                <p class="text-2xl font-black text-amber-600 mt-1 font-mono">{{ number_format($risk60, 2) }} <span class="text-xs font-normal">AED</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'مراقبة (<90 يوم)' : 'Capital Watch (<90d)' }}</p>
                <p class="text-2xl font-black text-indigo-600 mt-1 font-mono">{{ number_format($risk90, 2) }} <span class="text-xs font-normal">AED</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>

    <!-- Table -->
    <x-admin.table-toolbar 
        table="#valuationTable" 
        :title="app()->getLocale() === 'ar' ? 'تفاصيل تقييم التشغيلات ومخاطر الصلاحية' : 'Raw Material Lot Valuation & Aging Matrix'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table text-xs" id="valuationTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('batches.batch_number') }}</th>
                        <th data-sort-type="text">{{ __('purchase_orders.raw_material') }}</th>
                        <th data-sort-type="text">{{ __('batches.supplier_lot') }}</th>
                        <th data-sort-type="number">{{ __('batches.current_qty') }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'تكلفة الوحدة (AED)' : 'Unit Cost' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'إجمالي القيمة (AED)' : 'Valuation (AED)' }}</th>
                        <th data-sort-type="date">{{ __('batches.expiry_date') }}</th>
                        <th data-sort-type="text">{{ app()->getLocale() === 'ar' ? 'مستوى الخطورة' : 'Risk Tier' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                        @php
                            $cost = $batch->rawMaterial?->standard_cost ?? 0;
                            $val = $batch->current_qty * $cost;
                            $days = now()->diffInDays($batch->expiry_date, false);
                        @endphp
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                <a href="{{ route('admin.batches.show', $batch) }}" class="hover:underline">
                                    {{ $batch->batch_number }}
                                </a>
                            </td>
                            <td class="font-bold text-slate-800 dark:text-white">
                                {{ $batch->rawMaterial?->name }}
                                <span class="block text-[10px] text-gray-400 font-mono">{{ $batch->rawMaterial?->sku }}</span>
                            </td>
                            <td class="font-mono text-gray-500">{{ $batch->supplier_lot }}</td>
                            <td class="font-mono font-bold">{{ number_format($batch->current_qty, 2) }} {{ $batch->rawMaterial?->uom?->value }}</td>
                            <td class="font-mono">{{ number_format($cost, 2) }}</td>
                            <td class="font-mono font-black text-slate-800 dark:text-white">{{ number_format($val, 2) }}</td>
                            <td class="font-mono">{{ $batch->expiry_date?->format('Y-m-d') }}</td>
                            <td>
                                @if($days <= 0)
                                    <span class="badge badge-rose text-[10px] font-bold">EXPIRED</span>
                                @elseif($days <= 30)
                                    <span class="badge badge-rose text-[10px] font-bold">CRITICAL (<30d)</span>
                                @elseif($days <= 60)
                                    <span class="badge badge-amber text-[10px] font-bold">WARNING (<60d)</span>
                                @elseif($days <= 90)
                                    <span class="badge badge-sky text-[10px] font-bold">WATCH (<90d)</span>
                                @else
                                    <span class="badge badge-emerald text-[10px]">SAFE ({{ $days }}d)</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
