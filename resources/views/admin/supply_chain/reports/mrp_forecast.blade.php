<x-layouts.admin 
    :pageTitle="__('supply_chain_reports.mrp_forecast')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تخطيط متطلبات المواد الأولية، مضاهاة المخزون بالطلب التصنيعي، ومقترحات الشراء الذكية' : 'Material Requirements Planning (MRP), Net Demand Forecast & Automated Reorder Suggestions'"
    :breadcrumbs="[__('raw_materials.title') => route('admin.raw-materials.index'), __('supply_chain_reports.mrp_forecast') => '#']"
>
    <x-slot name="actions">
        <a href="{{ route('admin.supply-chain-reports.mrp-forecast', ['export' => 'xlsx']) }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-file-excel mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}
        </a>
    </x-slot>

    <!-- Top KPI Ribbon -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي المواد المراقبة' : 'Active Catalog SKUs' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $materials->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
        </div>
        <div class="card p-4 border border-rose-100 dark:border-rose-950 bg-rose-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-700 uppercase">{{ app()->getLocale() === 'ar' ? 'مواد تحت حد الأمان (عجز)' : 'Deficit / Stockout Alert' }}</p>
                @php
                    $deficitCount = $materials->filter(fn($m) => ($m->current_stock ?? 0) <= $m->reorder_level)->count();
                @endphp
                <p class="text-2xl font-black text-rose-600 mt-1 font-mono">{{ $deficitCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
        <div class="card p-4 border border-blue-100 dark:border-blue-950 bg-blue-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-blue-700 uppercase">{{ app()->getLocale() === 'ar' ? 'طلب التصنيع المجدول' : 'Allocated in Production' }}</p>
                <p class="text-2xl font-black text-blue-700 mt-1 font-mono">{{ number_format($materials->sum('reserved_stock') ?? 0, 1) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-industry"></i>
            </div>
        </div>
        <div class="card p-4 border border-emerald-100 dark:border-emerald-950 bg-emerald-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-700 uppercase">{{ app()->getLocale() === 'ar' ? 'مخزون صحي ومستقر' : 'Adequately Stocked' }}</p>
                <p class="text-2xl font-black text-emerald-700 mt-1 font-mono">{{ $materials->count() - $deficitCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- MRP Table -->
    <x-admin.table-toolbar 
        table="#mrpTable" 
        :title="app()->getLocale() === 'ar' ? 'جدول تخطيط متطلبات المواد الأولية (MRP)' : 'Material Requirements Planning (MRP) Forecast Ledger'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table text-xs" id="mrpTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('raw_materials.sku') }}</th>
                        <th data-sort-type="text">{{ __('raw_materials.name') }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'المخزون المتاح' : 'Available Stock' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'المحجوز للإنتاج' : 'Reserved (WIP)' }}</th>
                        <th data-sort-type="number">{{ __('raw_materials.safety_stock') }}</th>
                        <th data-sort-type="number">{{ __('raw_materials.reorder_level') }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'الكمية المقترحة للشراء' : 'Suggested Reorder' }}</th>
                        <th data-sort-type="text">{{ __('raw_materials.default_supplier') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Action' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $material)
                        @php
                            $avail = $material->available_stock ?? $material->batches->where('status', \App\Enums\RawMaterialBatchStatus::Approved)->sum('available_qty');
                            $reserved = $material->reserved_stock ?? $material->batches->sum('reserved_qty');
                            $isDeficit = $avail <= $material->reorder_level;
                            $suggested = max(0, ($material->reorder_level * 2) - $avail);
                        @endphp
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                <a href="{{ route('admin.raw-materials.show', $material) }}" class="hover:underline">
                                    {{ $material->sku }}
                                </a>
                            </td>
                            <td class="font-bold text-slate-800 dark:text-white">
                                {{ $material->name }}
                            </td>
                            <td class="font-mono font-bold {{ $isDeficit ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ number_format($avail, 2) }} {{ $material->uom?->value }}
                            </td>
                            <td class="font-mono text-blue-600">
                                {{ number_format($reserved, 2) }}
                            </td>
                            <td class="font-mono text-gray-500">{{ number_format($material->safety_stock, 2) }}</td>
                            <td class="font-mono font-bold text-amber-600">{{ number_format($material->reorder_level, 2) }}</td>
                            <td class="font-mono font-black {{ $suggested > 0 ? 'text-rose-600 text-sm' : 'text-gray-400' }}">
                                {{ $suggested > 0 ? number_format($suggested, 2) . ' ' . $material->uom?->value : '—' }}
                            </td>
                            <td>
                                @if($material->defaultSupplier)
                                    <a href="{{ route('admin.suppliers.show', $material->defaultSupplier) }}" class="text-sky-600 hover:underline">
                                        {{ $material->defaultSupplier->name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($suggested > 0)
                                    <a href="{{ route('admin.purchase-orders.create', ['material_id' => $material->id, 'supplier_id' => $material->default_supplier_id]) }}" class="btn btn-primary btn-xs font-bold">
                                        <i class="fa-solid fa-cart-plus mr-1"></i> {{ app()->getLocale() === 'ar' ? 'طلب شراء' : 'Draft PO' }}
                                    </a>
                                @else
                                    <span class="text-emerald-600 text-[11px] font-bold">OK</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
