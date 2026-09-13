@props([
    'table' => 'table.table',
    'title' => 'Report',
    'showExcel' => true,
    'showCsv' => true,
    'showPrint' => true,
    'showSearch' => true,
    'searchPlaceholder' => null,
])

@php
    $isAr = app()->getLocale() === 'ar';
    $defaultPlaceholder = $isAr ? 'بحث سريع داخل الجدول...' : 'Quick filter in table...';
    $placeholder = $searchPlaceholder ?? $defaultPlaceholder;
    $uniqueId = 'tbl_tool_' . substr(md5($table . $title), 0, 8);
@endphp

<div class="bz-table-toolbar flex items-center justify-between gap-3 flex-wrap my-3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm" id="{{ $uniqueId }}">
    <!-- Left: Quick In-Table Search Input -->
    <div class="flex items-center gap-2 flex-1 min-w-[220px] max-w-sm">
        @if($showSearch)
            <div class="relative w-full">
                <span class="absolute inset-y-0 {{ $isAr ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" 
                    data-table-filter="{{ $table }}" 
                    placeholder="{{ $placeholder }}"
                    class="w-full text-xs rounded-lg py-2 {{ $isAr ? 'pr-8 pl-3' : 'pl-8 pr-3' }} bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0A4F78] focus:border-transparent text-slate-800 dark:text-slate-100 transition-all shadow-inner"
                >
            </div>
        @endif
    </div>

    <!-- Right: Export & Print Action Buttons -->
    <div class="flex items-center gap-2 flex-wrap">
        {{ $slot ?? '' }}

        @if($showExcel)
            <button type="button" 
                data-export-excel="{{ $table }}" 
                data-title="{{ $title }}"
                class="btn-table-action inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-all cursor-pointer hover:shadow hover:-translate-y-0.5 active:translate-y-0"
                title="{{ $isAr ? 'تصدير كملف إكسل منسق بالكامل' : 'Export formatted Excel (.xls)' }}">
                <i class="fa-solid fa-file-excel text-sm"></i>
                <span>{{ $isAr ? 'تصدير إكسل' : 'Excel' }}</span>
            </button>
        @endif

        @if($showCsv)
            <button type="button" 
                data-export-csv="{{ $table }}" 
                data-title="{{ $title }}"
                class="btn-table-action inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-700 hover:bg-sky-800 text-white shadow-sm transition-all cursor-pointer hover:shadow hover:-translate-y-0.5 active:translate-y-0"
                title="{{ $isAr ? 'تصدير CSV متوافق مع كافة الأنظمة' : 'Export universal UTF-8 CSV' }}">
                <i class="fa-solid fa-file-csv text-sm"></i>
                <span>{{ $isAr ? 'تصدير CSV' : 'CSV' }}</span>
            </button>
        @endif

        @if($showPrint)
            <button type="button" 
                data-print-table="{{ $table }}" 
                data-title="{{ $title }}"
                class="btn-table-action inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-[#0A4F78] hover:bg-[#083D5D] text-white shadow-sm transition-all cursor-pointer hover:shadow hover:-translate-y-0.5 active:translate-y-0"
                title="{{ $isAr ? 'طباعة تقرير منسق بشعار الشركة ومعايير الطباعة' : 'Print Branded Official Report' }}">
                <i class="fa-solid fa-print text-sm"></i>
                <span>{{ $isAr ? 'طباعة التقرير' : 'Print' }}</span>
            </button>
        @endif
    </div>
</div>
