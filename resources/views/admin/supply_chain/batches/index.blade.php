<x-layouts.admin 
    :pageTitle="__('batches.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل تشغيلات المواد الأولية، أسبقية الصرف حسب انتهاء الصلاحية FEFO، وتتبع الحجر الصحي والضمان' : 'Raw Material Batch Ledger, FEFO Pick Queue, Quarantine Tracking & Expiry Alerts'"
    :breadcrumbs="[__('batches.title') => route('admin.batches.index')]"
>
    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي التشغيلات' : 'Total Active Batches' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $kpis['total_batches'] ?? $batches->total() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تشغيلات معتمدة ومتاحة' : 'Approved Available' }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $kpis['approved'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تشغيلات قيد الحجر QC' : 'Quarantine Isolation' }}</p>
                <p class="text-2xl font-black text-purple-600 mt-1">{{ $kpis['quarantine'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-flask-vial"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تنتهي خلال (<60 يوم)' : 'Expiring Soon (<60d)' }}</p>
                <p class="text-2xl font-black text-rose-500 mt-1">{{ $kpis['expiring_soon'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.batches.index') }}" class="flex items-center justify-between gap-4 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم التشغيلة LOT أو تشغيلة المورد...' : 'Search batch LOT or vendor lot...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    @foreach(\App\Enums\RawMaterialBatchStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <select name="raw_material_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المواد الأولية' : 'All Raw Materials' }}</option>
                    @foreach($materials as $mat)
                        <option value="{{ $mat->id }}" {{ request('raw_material_id') == $mat->id ? 'selected' : '' }}>
                            {{ $mat->name }} ({{ $mat->sku }})
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Batches Table -->
    <x-admin.table-toolbar 
        table="#batchesTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل تشغيلات المواد الأولية (FEFO)' : 'Raw Material Batches (FEFO Queue)'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table" id="batchesTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('batches.batch_number') }}</th>
                        <th data-sort-type="text">{{ __('purchase_orders.raw_material') }}</th>
                        <th data-sort-type="text">{{ __('batches.supplier_lot') }}</th>
                        <th data-sort-type="text">{{ __('batches.location') }}</th>
                        <th data-sort-type="number">{{ __('batches.current_qty') }}</th>
                        <th data-sort-type="number">{{ __('batches.available_qty') }}</th>
                        <th data-sort-type="date">{{ __('batches.expiry_date') }}</th>
                        <th data-sort-type="text">{{ __('batches.status') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td class="font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.batches.show', $batch) }}" class="hover:underline">
                                    {{ $batch->batch_number }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    <a href="{{ route('admin.raw-materials.show', $batch->rawMaterial) }}" class="hover:underline text-sky-600">
                                        {{ $batch->rawMaterial?->name }}
                                    </a>
                                </div>
                                <span class="text-[11px] text-gray-500 font-mono">{{ $batch->rawMaterial?->sku }}</span>
                            </td>
                            <td class="font-mono text-xs text-gray-600 dark:text-gray-300">
                                {{ $batch->supplier_lot ?? '—' }}
                            </td>
                            <td class="font-mono text-xs">{{ $batch->location_id }}</td>
                            <td class="font-mono font-bold">
                                {{ number_format($batch->current_qty, 2) }} <span class="text-xs text-gray-500">{{ $batch->rawMaterial?->uom?->value }}</span>
                            </td>
                            <td class="font-mono font-bold text-emerald-600">
                                {{ number_format($batch->available_qty, 2) }}
                            </td>
                            <td class="font-mono text-xs">
                                @php
                                    $days = now()->diffInDays($batch->expiry_date, false);
                                @endphp
                                <span class="{{ $days <= 30 ? 'text-rose-600 font-bold' : ($days <= 90 ? 'text-amber-500 font-bold' : '') }}">
                                    {{ $batch->expiry_date?->format('Y-m-d') }}
                                    @if($days <= 0)
                                        <span class="text-rose-600 font-bold block text-[10px]">EXPIRED</span>
                                    @elseif($days <= 90)
                                        <span class="text-amber-500 block text-[10px]">{{ $days }} days left</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $batch->status->badgeClass() }} text-xs">
                                    {{ $batch->status->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-ghost btn-sm text-sky-600" title="{{ app()->getLocale() === 'ar' ? 'عرض السجل وتاريخ الحركات' : 'View Batch Ledger' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-gray-400"></i>
                                <p>{{ app()->getLocale() === 'ar' ? 'لا توجد تشغيلات مطابقة لمعايير البحث.' : 'No batches found matching criteria.' }}</p>
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
</x-layouts.admin>
