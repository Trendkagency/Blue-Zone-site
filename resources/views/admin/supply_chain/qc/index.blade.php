<x-layouts.admin 
    :pageTitle="__('qc.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل فحوصات الجودة المخبرية، تدقيق شهادات التحليل COA، والاعتماد المزدوج للإفراج الدوائي' : 'Analytical Quality Testing, COA Document Verification, Dual cGMP Sign-off & Batch Release'"
    :breadcrumbs="[__('qc.title') => route('admin.qc.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.qc.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('qc.new_inspection') }}
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي الفحوصات' : 'Total Inspections' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $kpis['total_inspections'] ?? $inspections->total() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-microscope"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'معتمد ومفرج عنه' : 'Passed & Released' }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $kpis['passed'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'بانتظار الاعتماد المزدوج' : 'Pending Verification' }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $kpis['pending'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تشغيلات مرفوضة' : 'Rejected Lots' }}</p>
                <p class="text-2xl font-black text-rose-500 mt-1">{{ $kpis['failed'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-ban"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.qc.index') }}" class="flex items-center justify-between gap-4 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم الفحص QC-YYYY-XXXX...' : 'Search inspection number QC-YYYY-XXXX...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="verdict" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع القرارات' : 'All Verdicts' }}</option>
                    @foreach(\App\Enums\QcVerdict::cases() as $v)
                        <option value="{{ $v->value }}" {{ request('verdict') === $v->value ? 'selected' : '' }}>
                            {{ $v->label() }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- QC Table -->
    <x-admin.table-toolbar 
        table="#qcTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل الفحوصات المخبرية COA' : 'Analytical QC Inspections Log'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table" id="qcTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('qc.inspection_number') }}</th>
                        <th data-sort-type="text">{{ __('batches.batch_number') }}</th>
                        <th data-sort-type="text">{{ __('purchase_orders.raw_material') }}</th>
                        <th data-sort-type="date">{{ __('qc.inspection_date') }}</th>
                        <th data-sort-type="text">{{ __('qc.inspector') }}</th>
                        <th data-sort-type="text">{{ __('qc.verifier') }}</th>
                        <th data-sort-type="text">{{ __('qc.verdict') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $qc)
                        <tr>
                            <td class="font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.qc.show', $qc) }}" class="hover:underline">
                                    {{ $qc->inspection_number }}
                                </a>
                            </td>
                            <td class="font-mono text-xs">
                                <a href="{{ route('admin.batches.show', $qc->batch) }}" class="text-purple-600 hover:underline">
                                    {{ $qc->batch?->batch_number }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ $qc->batch?->rawMaterial?->name }}
                                </div>
                                <span class="text-[11px] text-gray-500 font-mono">{{ $qc->batch?->rawMaterial?->sku }}</span>
                            </td>
                            <td class="font-mono text-xs">
                                {{ $qc->inspection_date?->format('Y-m-d') }}
                            </td>
                            <td class="text-xs">{{ $qc->inspector?->name ?? 'Inspector' }}</td>
                            <td class="text-xs">
                                @if($qc->verifier)
                                    <span class="text-emerald-600 font-bold"><i class="fa-solid fa-check"></i> {{ $qc->verifier->name }}</span>
                                @else
                                    <span class="badge badge-amber text-[10px]">{{ app()->getLocale() === 'ar' ? 'بانتظار الاعتماد' : 'Pending' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $qc->verdict->badgeClass() }} text-xs">
                                    {{ $qc->verdict->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.qc.show', $qc) }}" class="btn btn-ghost btn-sm text-sky-600" title="{{ app()->getLocale() === 'ar' ? 'عرض الفحص' : 'View Inspection' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <i class="fa-solid fa-microscope text-3xl mb-2 text-gray-400"></i>
                                <p>{{ app()->getLocale() === 'ar' ? 'لا توجد فحوصات جودة مطابقة.' : 'No QC inspections recorded.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inspections->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
