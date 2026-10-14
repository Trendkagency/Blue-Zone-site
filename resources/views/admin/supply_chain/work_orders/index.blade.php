<x-layouts.admin 
    :pageTitle="__('production.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'أوامر تشغيل التصنيع، تخصيص وحجز المواد الأولية FEFO، وصرف المكونات غير القابل للتعديل' : 'Manufacturing Execution System (MES), BOM Consumption, Cleanroom Dispense & Finished Goods Ledger'"
    :breadcrumbs="[__('production.title') => route('admin.work-orders.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.work-orders.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('production.new_work_order') }}
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي أوامر التصنيع' : 'Total Work Orders' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $kpis['total'] ?? $workOrders->total() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-industry"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'قيد التنفيذ بالمصنع' : 'Active In-Progress' }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $kpis['in_production'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-gears"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تم اكتمال الإنتاج' : 'Batches Completed' }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $kpis['completed'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'مخطط وبانتظار الصرف' : 'Planned / Queued' }}</p>
                <p class="text-2xl font-black text-sky-600 mt-1">{{ $kpis['scheduled'] ?? 0 }}</p>
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
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم أمر التشغيل WO-YYYY-XXXX أو تشغيلة المنتج...' : 'Search WO number or product batch...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    @foreach(\App\Enums\WorkOrderStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
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
                        <th data-sort-type="text">{{ __('production.product') }}</th>
                        <th data-sort-type="text">{{ __('production.batch_number') }}</th>
                        <th data-sort-type="number">{{ __('production.target_quantity') }}</th>
                        <th data-sort-type="number">{{ __('production.actual_quantity') }}</th>
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
                                    {{ $wo->wo_number }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ $wo->product?->name_en }}
                                </div>
                                <span class="text-[11px] text-gray-500 font-mono">{{ $wo->product?->sku }}</span>
                            </td>
                            <td class="font-mono text-xs text-purple-600 font-bold">
                                {{ $wo->product_batch_number ?? '—' }}
                            </td>
                            <td class="font-mono font-bold">
                                {{ number_format($wo->target_quantity) }} {{ app()->getLocale() === 'ar' ? 'عبوة' : 'units' }}
                            </td>
                            <td class="font-mono font-bold text-emerald-600">
                                {{ number_format($wo->actual_quantity) }}
                            </td>
                            <td class="font-mono text-xs">
                                {{ $wo->scheduled_date?->format('Y-m-d') }}
                            </td>
                            <td>
                                <span class="badge {{ $wo->status->badgeClass() }} text-xs">
                                    {{ $wo->status->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.work-orders.show', $wo) }}" class="btn btn-ghost btn-sm text-sky-600" title="{{ app()->getLocale() === 'ar' ? 'عرض أمر التشغيل والصرف' : 'View MES Work Order' }}">
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
