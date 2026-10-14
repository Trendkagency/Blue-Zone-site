<x-layouts.admin 
    :pageTitle="__('supply_chain_reports.qc_log')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل الفحوصات المخبرية الشامل، تتبع نسب النجاح والرفض، ومطابقة وثائق COA' : 'Analytical Quality Testing Register, Defect Rates & Dual Verification Audit History'"
    :breadcrumbs="[__('qc.title') => route('admin.qc.index'), __('supply_chain_reports.qc_log') => '#']"
>
    <x-slot name="actions">
        <a href="{{ route('admin.supply-chain-reports.qc-log', ['export' => 'xlsx']) }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-file-excel mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}
        </a>
    </x-slot>

    <!-- Top KPI Ribbon -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي الفحوصات' : 'Total Tests Run' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $inspections->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-microscope"></i>
            </div>
        </div>
        <div class="card p-4 border border-emerald-100 dark:border-emerald-950 bg-emerald-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-700 uppercase">{{ app()->getLocale() === 'ar' ? 'نسبة اجتياز الفحص' : 'Overall Pass Rate' }}</p>
                @php
                    $passedCount = $inspections->where('verdict', \App\Enums\QcVerdict::Passed)->count();
                    $passRate = $inspections->count() > 0 ? round(($passedCount / $inspections->count()) * 100, 1) : 100;
                @endphp
                <p class="text-2xl font-black text-emerald-700 mt-1 font-mono">{{ $passRate }}%</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-rose-100 dark:border-rose-950 bg-rose-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-700 uppercase">{{ app()->getLocale() === 'ar' ? 'تشغيلات مرفوضة' : 'Lots Rejected' }}</p>
                <p class="text-2xl font-black text-rose-700 mt-1 font-mono">{{ $inspections->where('verdict', \App\Enums\QcVerdict::Failed)->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-ban"></i>
            </div>
        </div>
        <div class="card p-4 border border-amber-100 dark:border-amber-950 bg-amber-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-700 uppercase">{{ app()->getLocale() === 'ar' ? 'إفراج مشروط بموافقة' : 'Conditional Waivers' }}</p>
                <p class="text-2xl font-black text-amber-700 mt-1 font-mono">{{ $inspections->where('verdict', \App\Enums\QcVerdict::ConditionalPass)->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-hand-holding-hand"></i>
            </div>
        </div>
    </div>

    <!-- QC Master Table -->
    <x-admin.table-toolbar 
        table="#qcLogTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل نتائج الفحوصات المخبرية الشامل' : 'Analytical QC Inspection Master Log'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table text-xs" id="qcLogTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('qc.inspection_number') }}</th>
                        <th data-sort-type="text">{{ __('batches.batch_number') }}</th>
                        <th data-sort-type="text">{{ __('purchase_orders.raw_material') }}</th>
                        <th data-sort-type="text">{{ __('batches.supplier_lot') }}</th>
                        <th data-sort-type="date">{{ __('qc.inspection_date') }}</th>
                        <th data-sort-type="text">{{ __('qc.inspector') }}</th>
                        <th data-sort-type="text">{{ __('qc.verifier') }}</th>
                        <th data-sort-type="text">{{ __('qc.verdict') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'عرض' : 'Action' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inspections as $qc)
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                <a href="{{ route('admin.qc.show', $qc) }}" class="hover:underline">
                                    {{ $qc->inspection_number }}
                                </a>
                            </td>
                            <td class="font-mono text-purple-600 font-bold">
                                <a href="{{ route('admin.batches.show', $qc->batch) }}" class="hover:underline">
                                    {{ $qc->batch?->batch_number }}
                                </a>
                            </td>
                            <td class="font-bold text-slate-800 dark:text-white">
                                {{ $qc->batch?->rawMaterial?->name }}
                            </td>
                            <td class="font-mono text-gray-500">{{ $qc->batch?->supplier_lot }}</td>
                            <td class="font-mono">{{ $qc->inspection_date?->format('Y-m-d') }}</td>
                            <td>{{ $qc->inspector?->name }}</td>
                            <td>
                                @if($qc->verifier)
                                    <span class="text-emerald-600 font-bold">{{ $qc->verifier->name }}</span>
                                @else
                                    <span class="text-amber-500 text-[10px] font-bold">PENDING</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $qc->verdict->badgeClass() }} text-[11px]">
                                    {{ $qc->verdict->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.qc.show', $qc) }}" class="btn btn-ghost btn-xs text-sky-600">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
