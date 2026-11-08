<x-layouts.admin 
    :pageTitle="__('production.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'نظام إدارة التنفيذ التصنيعي (MES)، وتخصيص وحجز المواد الأولية FEFO، وصرف المكونات المعقمة' : 'Manufacturing Execution System (MES), BOM Consumption, Cleanroom Dispense & Finished Goods Ledger'"
    :breadcrumbs="[__('production.title') => route('admin.work-orders.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'تشغيلات المواد FEFO' : 'FEFO Batches' }}</span>
        </a>
        <a href="{{ route('admin.qc.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-microscope text-purple-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'فحوصات الجودة (QC)' : 'QC Inspections' }}</span>
        </a>
        <a href="{{ route('admin.work-orders.create') }}" class="btn btn-primary font-bold shadow-sm inline-flex items-center gap-2 px-4 py-2 text-sm">
            <i class="fa-solid fa-plus"></i>
            <span>{{ __('production.new_work_order') }}</span>
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('production.kpis.total') }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($kpis['total'] ?? $workOrders->total()) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-industry"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('production.kpis.active') }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ number_format($kpis['in_production'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-gears"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('production.kpis.completed') }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($kpis['completed'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('production.kpis.planned') }}</p>
                <p class="text-2xl font-black text-sky-600 mt-1">{{ number_format($kpis['scheduled'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.work-orders.index') }}" class="flex items-center justify-between gap-4 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    class="form-control search-input text-sm w-full" 
                    placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم أمر التشغيل WO-YYYY-XXXX أو تشغيلة المنتج أو اسم المنتج...' : 'Search WO number, product batch, or product SKU...' }}"
                >
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.work-orders.index') }}" class="btn btn-ghost btn-sm text-gray-500 hover:text-rose-600">
                        <i class="fa-solid fa-xmark mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Work Orders Table -->
    <x-admin.table-toolbar 
        table="#woTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل أوامر تشغيل التصنيع' : 'Manufacturing Work Orders'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table" id="woTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('production.wo_number') }}</th>
                        <th data-sort-type="text">{{ __('production.target_product') }}</th>
                        <th data-sort-type="text">{{ __('production.batch_number') }}</th>
                        <th data-sort-type="number" style="text-align: right;">{{ __('production.target_quantity') }}</th>
                        <th data-sort-type="number" style="text-align: right;">{{ __('production.actual_quantity') }}</th>
                        <th data-sort-type="date">{{ __('production.scheduled_date') }}</th>
                        <th data-sort-type="text">{{ __('production.status') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $wo)
                        <tr>
                            <td class="font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.work-orders.show', $wo) }}" class="hover:underline">
                                    {{ $wo->order_number ?? $wo->wo_number }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ app()->getLocale() === 'ar' && !empty($wo->product?->name_ar) ? $wo->product->name_ar : $wo->product?->name_en }}
                                </div>
                                <span class="text-[11px] text-gray-500 font-mono">{{ $wo->product?->sku }}</span>
                            </td>
                            <td class="font-mono text-xs text-purple-600 dark:text-purple-400 font-bold">
                                {{ $wo->target_finished_lot ?? $wo->product_batch_number ?? '—' }}
                            </td>
                            <td class="font-mono font-bold" style="text-align: right;">
                                {{ number_format($wo->planned_qty ?? $wo->target_quantity) }} 
                                <span class="text-xs text-gray-400 font-normal">{{ app()->getLocale() === 'ar' ? 'عبوة' : 'units' }}</span>
                            </td>
                            <td class="font-mono font-bold text-emerald-600 dark:text-emerald-400" style="text-align: right;">
                                {{ number_format($wo->produced_qty ?? $wo->actual_quantity ?? 0) }}
                            </td>
                            <td class="font-mono text-xs">
                                {{ ($wo->scheduled_start ?? $wo->scheduled_date)?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td>
                                <span class="badge {{ $wo->status->badgeClass() }} text-xs">
                                    {{ $wo->status->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.work-orders.show', $wo) }}" class="btn btn-ghost btn-sm text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30" title="{{ app()->getLocale() === 'ar' ? 'عرض تفاصيل أمر التشغيل والصرف' : 'View Work Order Details' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <i class="fa-solid fa-industry text-3xl mb-2 text-gray-400"></i>
                                <p>{{ app()->getLocale() === 'ar' ? 'لا توجد أوامر تشغيل مطابقة.' : 'No manufacturing work orders recorded.' }}</p>
                                <a href="{{ route('admin.work-orders.create') }}" class="btn btn-primary btn-sm mt-3 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-plus"></i> {{ __('production.new_work_order') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($workOrders->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $workOrders->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
