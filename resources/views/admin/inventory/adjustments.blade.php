<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تسويات الجرد والمخزون' : 'Stock Adjustments & Reconciliations'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'معالجة فروقات الجرد الفعلي، إثبات التوالف، تعديل العجز والزيادة مع التوثيق المحاسبي والرقابي.' : 'Physical stock count discrepancy reconciliations, shrinkage audit, damaged items write-off, and manual ledger adjustments.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.dashboard'),
        (app()->getLocale() === 'ar' ? 'تسويات الجرد' : 'Stock Adjustments') => route('admin.inventory.adjustments')
    ]"
>
    <x-slot name="actions">
        <!-- New Adjustment Modal Trigger -->
        <button type="button" onclick="openNewAdjustmentModal()" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm">
            <i class="fa-solid fa-plus-minus mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'إجراء تسوية جرد جديدة' : 'New Stock Adjustment' }}
        </button>

        <!-- Adjustment Types Management Hub -->
        <a href="{{ route('admin.inventory.adjustments.types.index') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-tags text-indigo-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'أنواع التسويات' : 'Adjustment Types' }}
        </a>

        <!-- Fast Control Hub Link -->
        <a href="{{ route('admin.inventory.control') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-sliders text-emerald-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'مركز التحكم السريع' : 'Quick Control Hub' }}
        </a>

        <!-- Movements History Link -->
        <a href="{{ route('admin.inventory.movements') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-clock-rotate-left mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'سجل الحركات' : 'Movements Log' }}
        </a>
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Notices -->
        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->has('adjustment_error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <span>{{ $errors->first('adjustment_error') }}</span>
            </div>
        @endif

        <!-- 1. Adjustments Monthly Intelligence Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Adjustments this month -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-purple-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'تسويات هذا الشهر' : 'Monthly Adjustments' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $totalAdjustmentsMonth }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'عملية' : 'actions' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'معتمدة ومقيدة بسجل التدقيق' : 'Logged into central auditable ledger' }}
                </div>
            </div>

            <!-- Inward / Surplus Found -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-emerald-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'كميات مضافة (فائض/توريد)' : 'Inward Additions / Surplus' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-trend-up text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight font-mono">
                    +{{ number_format($inwardUnitsMonth) }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'فروق جرد موجبة ومرتجعات' : 'Positive count surplus & returns' }}
                </div>
            </div>

            <!-- Damaged / Expired Deductions -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-rose-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'توالف ومنتهي الصلاحية' : 'Damaged & Expired Loss' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-dumpster-fire text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 tracking-tight font-mono">
                    -{{ number_format($damagedUnitsMonth) }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'استبعاد وإتلاف موثق بالسبب' : 'Audited shrinkage & damage deductions' }}
                </div>
            </div>

            <!-- Net Unit Variance -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-sky-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'صافي أثر التسويات' : 'Net Unit Variance' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-scale-balanced text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black {{ $netVarianceMonth >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} tracking-tight font-mono">
                    {{ $netVarianceMonth > 0 ? "+{$netVarianceMonth}" : $netVarianceMonth }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'محصلة التغير في الرصيد الكلي' : 'Overall fleet net unit balance delta' }}
                </div>
            </div>
        </div>

        <!-- 2. Search & Filter Bar -->
        <div class="card p-4 sm:p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
            <form method="GET" action="{{ route('admin.inventory.adjustments') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 flex-wrap">
                <!-- Search -->
                <div class="flex-1 min-w-[220px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 start-3.5 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ $search }}" class="form-control ps-9 text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برمز SKU، اسم الصنف، السبب، رقم السجل، المستخدم...' : 'Filter by SKU, product name, reason, user...' }}">
                    </div>
                </div>

                <!-- Movement Type Filter -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="movement_type" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع أنواع التسويات' : 'All Adjustment Types' }}</option>
                        @foreach($adjustmentTypes as $typeOpt)
                            <option value="{{ $typeOpt->code }}" {{ $selectedType === $typeOpt->code ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' && $typeOpt->name_ar ? $typeOpt->name_ar : $typeOpt->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="location_id" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع المستودعات' : 'All Locations & Hubs' }}</option>
                        @foreach($locations as $loc)
                            @php
                                $locId = is_array($loc) ? $loc['id'] : $loc->id;
                                $locName = app()->getLocale() === 'ar' ? (is_array($loc) ? ($loc['name_ar'] ?? $loc['name_en']) : ($loc->name_ar ?? $loc->name_en)) : (is_array($loc) ? $loc['name_en'] : $loc->name_en);
                            @endphp
                            <option value="{{ $locId }}" {{ $selectedLocation === $locId ? 'selected' : '' }}>
                                {{ $locName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold px-3">
                        <i class="fa-solid fa-filter mr-1 ml-1 text-slate-400"></i>
                        {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if($search || $selectedType !== 'all' || $selectedLocation !== 'all')
                        <a href="{{ route('admin.inventory.adjustments') }}" class="btn btn-neutral btn-sm text-xs" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Reset' }}
                        </a>
                    @endif
                    <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm text-xs" title="Print Audit Sheet">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Adjustments Audit Ledger Table -->
        <div class="card bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs overflow-hidden">
            <div class="table-responsive">
                <table class="table w-full text-start" id="stockAdjustmentsTable">
                    <thead class="bg-slate-50 dark:bg-[#041E34] border-b border-slate-200/80 dark:border-[#0E4A7A] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'رقم السجل' : 'Ref / ID' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'المنتج والمواصفة' : 'Product & Formulation' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'المستودع / الموقع' : 'Location' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'نوع التسوية' : 'Adjustment Type' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'الكمية المعدلة' : 'Qty Delta' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'الرصيد السابق &rarr; الجديد' : 'Prev &rarr; New Balance' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'المسؤول' : 'Audited By' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'التوقيت والتبرير الرقابي' : 'Timestamp & Justification' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @forelse($adjustments as $adj)
                            @php
                                $mId = $adj->movement_number ?? ('ADJ-' . $adj->id);
                                $prodName = app()->getLocale() === 'ar' ? ($adj->product_name_ar ?? $adj->product_name_en) : ($adj->product_name_en ?? 'Active Formulation');
                                $qty = (int) $adj->quantity;
                                $isPositive = $qty > 0;
                                $adjType = $adj->movement_type;

                                $badgeClass = match($adjType) {
                                    'Stock In', 'Return' => 'badge-success',
                                    'Manual Adjustment' => 'badge-primary',
                                    'Damaged', 'Expired', 'Stock Out' => 'badge-danger',
                                    default => 'badge-neutral',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- ID / Ref -->
                                <td class="py-3.5 px-4 font-mono font-bold text-primary">
                                    {{ $mId }}
                                </td>

                                <!-- Product Info -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        <a href="{{ route('admin.inventory.show', $adj->product_id ?? 1) }}" class="hover:underline hover:text-primary">
                                            {{ $prodName }}
                                        </a>
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                                        {{ $adj->sku ?? 'BZ-SKU' }}
                                    </div>
                                </td>

                                <!-- Location -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        <i class="fa-solid fa-warehouse text-[10px] text-slate-400"></i>
                                        {{ $adj->from_location ?: ($adj->to_location ?: 'Central WH') }}
                                    </span>
                                </td>

                                <!-- Adjustment Type -->
                                <td class="py-3.5 px-4">
                                    <span class="badge {{ $badgeClass }} text-[11px] font-black py-0.5 px-2">
                                        {{ $adjType }}
                                    </span>
                                </td>

                                <!-- Delta -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-mono text-sm font-black {{ $isPositive ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $isPositive ? "+{$qty}" : $qty }}
                                    </span>
                                </td>

                                <!-- Balance Transition -->
                                <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                    <span>{{ $adj->previous_qty ?? 0 }}</span>
                                    <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px] text-slate-400 mx-1"></i>
                                    <strong class="text-slate-900 dark:text-white">{{ $adj->new_qty ?? 0 }}</strong>
                                </td>

                                <!-- User -->
                                <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-user-shield text-[11px] text-slate-400"></i>
                                        <span>{{ $adj->user ?? 'Admin' }}</span>
                                    </div>
                                </td>

                                <!-- Timestamp & Justification -->
                                <td class="py-3.5 px-4">
                                    <div class="text-[11px] font-mono text-slate-400">
                                        {{ $adj->date?->format('Y-m-d') }} {{ $adj->time }}
                                    </div>
                                    @if($adj->note)
                                        <div class="text-[11px] text-slate-600 dark:text-slate-300 italic mt-0.5">
                                            "{{ $adj->note }}"
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 mx-auto flex items-center justify-center text-xl mb-3 opacity-60">
                                        <i class="fa-solid fa-sliders"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ app()->getLocale() === 'ar' ? 'لا توجد تسويات مطابقة لخيارات التصفية' : 'No stock adjustments match your filter' }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">{{ app()->getLocale() === 'ar' ? 'يمكنك إنشاء تسوية جديدة من زر "إجراء تسوية جرد جديدة" بالأعلى.' : 'Create a new stock adjustment using the top action button.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($adjustments->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$adjustments->total()" />
                </div>
            @endif
        </div>

    </div>

    <!-- 4. Interactive "New Stock Adjustment" Modal -->
    <div id="newAdjustmentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden transition-opacity">
        <div class="bg-white dark:bg-[#062B49] border border-slate-200 dark:border-[#0E4A7A] rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-plus-minus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'إجراء تسوية جردية معتمدة' : 'Initiate Stock Adjustment' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'تعديل الرصيد الفعلي للمنتج وتسجيل القيد في سجل التدقيق المحاسبي.' : 'Adjust physical count and immediately log auditable variance.' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeNewAdjustmentModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('admin.inventory.adjustments.store') }}" method="POST" id="adjustmentForm" class="p-6 space-y-4">
                @csrf

                <!-- Product Selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'المنتج / التركيبة المستهدفة' : 'Target Product Formulation' }} <span class="text-rose-500">*</span>
                    </label>
                    <select name="product_id" id="adjProductId" class="form-select text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" required>
                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المنتج المراد تعديل رصيده --' : '-- Select Product to Adjust --' }}</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">
                                {{ app()->getLocale() === 'ar' ? ($prod->name_ar ?? $prod->name_en) : $prod->name_en }} ({{ $prod->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location & Adjustment Type -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'المستودع / الموقع' : 'Location / Hub' }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="location_id" id="adjLocationId" class="form-select text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" required>
                            @foreach($locations as $loc)
                                @php
                                    $locId = is_array($loc) ? $loc['id'] : $loc->id;
                                    $locName = app()->getLocale() === 'ar' ? (is_array($loc) ? ($loc['name_ar'] ?? $loc['name_en']) : ($loc->name_ar ?? $loc->name_en)) : (is_array($loc) ? $loc['name_en'] : $loc->name_en);
                                @endphp
                                <option value="{{ $locId }}">{{ $locName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 m-0">
                                {{ app()->getLocale() === 'ar' ? 'نوع التسوية' : 'Adjustment Type' }} <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" onclick="openQuickCreateTypeModal()" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-plus-circle"></i>
                                {{ app()->getLocale() === 'ar' ? '+ إنشاء نوع جديد لحظي' : '+ New Type (Real-Time)' }}
                            </button>
                        </div>
                        <select name="movement_type" id="adjMovementType" class="form-select text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" required onchange="handleAdjustmentTypeChange(this)">
                            @foreach($adjustmentTypes as $typeOpt)
                                <option value="{{ $typeOpt->code }}" data-direction="{{ $typeOpt->direction }}">
                                    {{ app()->getLocale() === 'ar' && $typeOpt->name_ar ? $typeOpt->name_ar : $typeOpt->name_en }} ({{ ucfirst($typeOpt->direction) }})
                                </option>
                            @endforeach
                            <option value="__NEW__" class="font-bold text-indigo-600">
                                ➕ {{ app()->getLocale() === 'ar' ? 'إنشاء نوع تسوية جديد في الوقت الفعلي...' : 'Create New Type in Real-Time...' }}
                            </option>
                        </select>
                        <div id="typeEffectIndicator" class="text-[10px] text-slate-400 mt-1 flex items-center gap-1 font-semibold"></div>
                    </div>
                </div>

                <!-- Live Created Notice -->
                <div id="quickTypeCreatedBanner" class="hidden p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2"></div>

                <!-- Quantity & Reference Number -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الكمية المراد تسويتها' : 'Adjustment Quantity' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="quantity" id="adjQuantity" min="1" value="1" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl font-mono font-bold" required>
                        <span class="text-[10px] text-slate-400 mt-0.5 block">
                            {{ app()->getLocale() === 'ar' ? 'أدخل عدد الوحدات الإيجابية (سيقوم النظام بتحديد الخصم أو الإضافة آلياً)' : 'Positive integer (System determines sign based on type)' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'رقم الإسناد / محضر الجرد' : 'Reference / Audit Cycle #' }}
                        </label>
                        <input type="text" name="reference_number" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: AUD-2026-04' : 'e.g. AUD-2026-04' }}" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl font-mono">
                    </div>
                </div>

                <!-- Justification Reason -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'سبب وتبرير التسوية الرقابي' : 'Audit Justification Reason' }} <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="reason" rows="2" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: مطابقة الجرد الفصلي، عجز في الصناديق المستلمة، تلف أثناء النقل اللوجستي...' : 'e.g. Quarterly physical cycle count discrepancy, transit damage write-off...' }}" required></textarea>
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeNewAdjustmentModal()" class="btn btn-neutral text-xs sm:text-sm font-semibold">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary text-xs sm:text-sm font-bold shadow-md">
                        <i class="fa-solid fa-check mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'تأكيد وقيد التسوية' : 'Commit Adjustment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. Real-Time "Quick Create Adjustment Type" Modal -->
    <div id="quickCreateTypeModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4 hidden transition-opacity">
        <div class="bg-white dark:bg-[#062B49] border border-slate-200 dark:border-[#0E4A7A] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i class="fa-solid fa-plus-circle text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white m-0">
                            {{ app()->getLocale() === 'ar' ? 'إنشاء نوع تسوية جديد (في الوقت الفعلي)' : 'Create Adjustment Type in Real-Time' }}
                        </h4>
                        <p class="text-[11px] text-slate-400 m-0">
                            {{ app()->getLocale() === 'ar' ? 'سيتم حفظ النوع واختياره مباشرة في النموذج الحالي' : 'Instantly saved and selected in your adjustment form' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickCreateTypeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-base">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="quickCreateTypeForm" onsubmit="submitQuickCreateType(event)" class="p-5 space-y-3.5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'اسم النوع (بالإنجليزية)' : 'Type Name (English)' }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="quickNameEn" required placeholder="e.g. Internal Clinic Sampling" class="form-control text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الاسم بالعربية (اختياري)' : 'Type Name (Arabic - Optional)' }}
                    </label>
                    <input type="text" id="quickNameAr" placeholder="مثال: عينات استخدام سريري داخلي" class="form-control text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الأثر التلقائي على حركة المخزون' : 'Stock Direction / Effect' }} <span class="text-rose-500">*</span>
                    </label>
                    <select id="quickDirection" required class="form-select text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                        <option value="decrease">{{ app()->getLocale() === 'ar' ? 'خصم من المخزون (- خروج / إتلاف / عجز)' : 'Decrease Stock (- Outward / Deduction)' }}</option>
                        <option value="increase">{{ app()->getLocale() === 'ar' ? 'إضافة للمخزون (+ دخول / توريد / فائض)' : 'Increase Stock (+ Inward / Restock)' }}</option>
                        <option value="neutral">{{ app()->getLocale() === 'ar' ? 'تسوية ومطابقة فارق جرد (± حيادي)' : 'Count Reconciliation (± Neutral Discrepancy)' }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الوصف ومبرر التدقيق (اختياري)' : 'Description (Optional)' }}
                    </label>
                    <textarea id="quickDescription" rows="2" placeholder="Audit justification note..." class="form-control text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl"></textarea>
                </div>

                <div id="quickCreateError" class="hidden text-rose-500 text-xs font-medium"></div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeQuickCreateTypeModal()" class="btn btn-neutral text-xs font-semibold py-1.5 px-3">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" id="quickSubmitBtn" class="btn btn-primary text-xs font-bold py-1.5 px-3 shadow-md bg-indigo-600 hover:bg-indigo-500 text-white border-0">
                        <i class="fa-solid fa-plus-circle mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'إنشاء وتحديد الآن' : 'Create & Select' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Modal -->
    <script>
        function openNewAdjustmentModal(productId = null) {
            const modal = document.getElementById('newAdjustmentModal');
            if (!modal) return;
            if (productId) {
                const select = document.getElementById('adjProductId');
                if (select) select.value = productId;
            }
            modal.classList.remove('hidden');

            const typeSelect = document.getElementById('adjMovementType');
            if (typeSelect) updateEffectIndicator(typeSelect);
        }

        function closeNewAdjustmentModal() {
            const modal = document.getElementById('newAdjustmentModal');
            if (modal) modal.classList.add('hidden');
        }

        function handleAdjustmentTypeChange(select) {
            if (select.value === '__NEW__') {
                openQuickCreateTypeModal();
                return;
            }
            updateEffectIndicator(select);
        }

        function updateEffectIndicator(select) {
            const ind = document.getElementById('typeEffectIndicator');
            if (!ind) return;
            const opt = select.options[select.selectedIndex];
            const dir = opt ? opt.getAttribute('data-direction') : null;
            if (dir === 'decrease') {
                ind.innerHTML = '<span class="text-rose-500"><i class="fa-solid fa-arrow-trend-down"></i> {{ app()->getLocale() === "ar" ? "سيتم خصم الكمية من رصيد المستودع (-)" : "Will deduct quantity from stock (-)" }}</span>';
            } else if (dir === 'increase') {
                ind.innerHTML = '<span class="text-emerald-500"><i class="fa-solid fa-arrow-trend-up"></i> {{ app()->getLocale() === "ar" ? "سيتم إضافة الكمية إلى رصيد المستودع (+)" : "Will add quantity to stock (+)" }}</span>';
            } else {
                ind.innerHTML = '<span class="text-sky-500"><i class="fa-solid fa-scale-balanced"></i> {{ app()->getLocale() === "ar" ? "تسوية فارق الجرد الفعلي (±)" : "Physical count reconciliation variance (±)" }}</span>';
            }
        }

        function openQuickCreateTypeModal() {
            const modal = document.getElementById('quickCreateTypeModal');
            if (!modal) return;
            document.getElementById('quickCreateError').classList.add('hidden');
            modal.classList.remove('hidden');
            setTimeout(() => document.getElementById('quickNameEn')?.focus(), 100);
        }

        function closeQuickCreateTypeModal() {
            const modal = document.getElementById('quickCreateTypeModal');
            if (modal) modal.classList.add('hidden');
            // If select was on __NEW__, reset to first option
            const select = document.getElementById('adjMovementType');
            if (select && select.value === '__NEW__') {
                select.selectedIndex = 0;
                updateEffectIndicator(select);
            }
        }

        async function submitQuickCreateType(e) {
            e.preventDefault();
            const btn = document.getElementById('quickSubmitBtn');
            const errDiv = document.getElementById('quickCreateError');
            errDiv.classList.add('hidden');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 ml-1"></i> Saving...';

            const payload = {
                name_en: document.getElementById('quickNameEn').value.trim(),
                name_ar: document.getElementById('quickNameAr').value.trim(),
                direction: document.getElementById('quickDirection').value,
                description: document.getElementById('quickDescription').value.trim(),
                _token: '{{ csrf_token() }}'
            };

            try {
                const res = await fetch('{{ route("admin.inventory.adjustments.types.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to create adjustment type.'));
                }

                // Add to select
                const select = document.getElementById('adjMovementType');
                const newOpt = document.createElement('option');
                newOpt.value = data.type.code;
                newOpt.textContent = data.type.display_name + ' (' + data.type.direction + ')';
                newOpt.setAttribute('data-direction', data.type.direction);
                newOpt.selected = true;

                // Insert before the __NEW__ option
                const lastOpt = select.querySelector('option[value="__NEW__"]');
                if (lastOpt) {
                    select.insertBefore(newOpt, lastOpt);
                } else {
                    select.appendChild(newOpt);
                }

                select.value = data.type.code;
                updateEffectIndicator(select);

                // Show banner in main modal
                const banner = document.getElementById('quickTypeCreatedBanner');
                if (banner) {
                    banner.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-500"></i> <span>${data.message}</span>`;
                    banner.classList.remove('hidden');
                    setTimeout(() => banner.classList.add('hidden'), 5000);
                }

                // Reset quick form
                document.getElementById('quickCreateTypeForm').reset();
                closeQuickCreateTypeModal();
            } catch (err) {
                errDiv.textContent = err.message;
                errDiv.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-plus-circle mr-1 ml-1"></i> {{ app()->getLocale() === "ar" ? "إنشاء وتحديد الآن" : "Create & Select" }}';
            }
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const quickModal = document.getElementById('quickCreateTypeModal');
                if (quickModal && !quickModal.classList.contains('hidden')) {
                    closeQuickCreateTypeModal();
                } else {
                    closeNewAdjustmentModal();
                }
            }
        });
    </script>
</x-layouts.admin>
