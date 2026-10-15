<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل حركات المخزون والتدقيق المركزي' : 'Stock Movements & Audit Ledger'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل تدقيق المخزون المالي والمكاني لجميع عمليات الإدخال والإخراج والتحويل والمبيعات والتسويات.' : 'Auditable central ledger tracking every single inbound, outbound, transfer, sale, and adjustment transaction.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.dashboard'),
        (app()->getLocale() === 'ar' ? 'حركات المخزون' : 'Stock Movements') => route('admin.inventory.movements')
    ]"
>
    <x-slot name="actions">
        <!-- New Stock Adjustment -->
        <a href="{{ route('admin.inventory.adjustments') }}" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm">
            <i class="fa-solid fa-plus-minus mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'تسوية جردية جديدة' : 'New Adjustment' }}
        </a>

        <!-- Stock Transfers -->
        <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-arrow-right-arrow-left text-teal-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'تحويل مخزون' : 'Stock Transfers' }}
        </a>

        <!-- Quick Control Hub -->
        <a href="{{ route('admin.inventory.control') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-sliders text-emerald-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'مركز التحكم السريع' : 'Control Hub' }}
        </a>
    </x-slot>

    <div class="space-y-6">

        <!-- Filter Bar Form -->
        <div class="card p-4 sm:p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
            <form method="GET" action="{{ route('admin.inventory.movements') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 flex-wrap">
                <!-- Search wrapper -->
                <div class="flex-1 min-w-[220px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 start-3.5 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ $search }}" class="form-control ps-9 text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث برقم الحركة، التركيبة، SKU، المستخدم، الملاحظة...' : 'Filter by Log ID, product, SKU, user, note...' }}">
                    </div>
                </div>

                <!-- Movement Type Dropdown (All 10 Types) -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="movement_type" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() == 'ar' ? 'جميع أنواع الحركات' : 'All Movement Types' }}</option>
                        @foreach($movementTypes as $key => $type)
                            <option value="{{ $key }}" {{ $selectedType === $key ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? $type['ar'] : $type['en'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Dropdown -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="product_id" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() == 'ar' ? 'جميع التركيبات' : 'All Formulations' }}</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ $selectedProduct == $prod->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? ($prod->name_ar ?? $prod->name_en) : $prod->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="location" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() == 'ar' ? 'جميع المواقع' : 'All Locations' }}</option>
                        @foreach($locations as $loc)
                            @php
                                $locId = is_array($loc) ? $loc['id'] : $loc->id;
                                $locDisplay = app()->getLocale() === 'ar' ? (is_array($loc) ? ($loc['name_ar'] ?? $loc['name_en']) : ($loc->name_ar ?? $loc->name_en)) : (is_array($loc) ? $loc['name_en'] : $loc->name_en);
                            @endphp
                            <option value="{{ $locId }}" {{ $selectedLocation === $locId ? 'selected' : '' }}>
                                {{ $locDisplay }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold px-3">
                        <i class="fa-solid fa-filter mr-1 ml-1 text-slate-400"></i>
                        {{ app()->getLocale() == 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if($search || $selectedType !== 'all' || $selectedProduct !== 'all' || $selectedLocation !== 'all')
                        <a href="{{ route('admin.inventory.movements') }}" class="btn btn-neutral btn-sm text-xs" title="Clear Filters">
                            <i class="fa-solid fa-rotate-left mr-1 ml-1"></i>
                            {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}
                        </a>
                    @endif
                    <button type="button" class="btn btn-secondary btn-sm text-xs" onclick="window.print()">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Action Toolbar (Search, Excel Export, CSV & Print) -->
        <x-admin.table-toolbar 
            table="#inventoryHistoryTable" 
            :title="app()->getLocale() === 'ar' ? 'سجل حركات المخزون والتدقيق المركزي' : 'Inventory Audit & Movement Ledger'" 
        />

        <!-- Movement Ledger Table -->
        <div class="card bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs overflow-hidden">
            <div class="table-responsive">
                <table class="table w-full text-start" id="inventoryHistoryTable">
                    <thead class="bg-slate-50 dark:bg-[#041E34] border-b border-slate-200/80 dark:border-[#0E4A7A] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 text-start">{{ __('admin.inventory.movement_id') }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() == 'ar' ? 'التركيبة والمواصفة' : 'Product & Variant' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ __('admin.inventory.movement_type') }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() == 'ar' ? 'مسار الحركة (من &rarr; إلى)' : 'Routing (From &rarr; To)' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() == 'ar' ? 'فرق الكمية' : 'Qty Delta' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('admin.inventory.prev_qty') }} <i class="fa-solid fa-arrow-right rtl:rotate-180 text-xs"></i> {{ __('admin.inventory.new_qty') }}</th>
                            <th class="py-3.5 px-4 text-start">{{ __('admin.inventory.logged_user') }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() == 'ar' ? 'الوقت والتبرير' : 'Timestamp & Justification' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @forelse($movements as $m)
                            @php
                                $mId = $m->movement_number ?? ('MOV-' . $m->id);
                                $mName = app()->getLocale() == 'ar' ? ($m->product_name_ar ?? $m->product_name_en ?? 'تركيبة حيوية') : ($m->product_name_en ?? 'Compound');
                                $mSku = $m->sku ?? 'BZ-SKU';
                                $mVariant = $m->variant ?? 'Standard Pack';
                                $mType = $m->movement_type ?? 'Transfer';
                                $mFrom = $m->from_location ?? 'Central Warehouse';
                                $mTo = $m->to_location ?? 'Warehouse POS';
                                $mQty = (int) $m->quantity;
                                $mPrev = $m->previous_qty ?? 0;
                                $mNew = $m->new_qty ?? 0;
                                $mUser = $m->user ?? 'Admin';
                                $mDate = $m->date?->format('Y-m-d') ?? '';
                                $mTime = $m->time ?? '';
                                $mReason = $m->note ?? '';

                                // Badge color mapping
                                $badgeClass = match($mType) {
                                    'Stock In', 'Return' => 'badge-success',
                                    'Online Sale', 'Offline Sale' => 'badge-primary',
                                    'Damaged', 'Expired', 'Stock Out' => 'badge-danger',
                                    'Stock Transfer' => 'badge-warning',
                                    'Cancelled Order' => 'badge-accent',
                                    default => 'badge-neutral',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-primary">{{ $mId }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        <a href="{{ route('admin.inventory.show', $m->product_id ?? 1) }}" class="hover:underline hover:text-primary">
                                            {{ $mName }}
                                        </a>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $mSku }} • {{ $mVariant }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="badge {{ $badgeClass }} text-[11px] font-black py-0.5 px-2">{{ $mType }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-700 dark:text-slate-300">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() == 'ar' ? 'من:' : 'From:' }}</span>
                                        <span class="font-bold">{{ $mFrom }}</span>
                                        <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px] text-slate-400"></i>
                                        <span class="font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() == 'ar' ? 'إلى:' : 'To:' }}</span>
                                        <span class="font-bold">{{ $mTo }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-black text-sm {{ $mQty < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $mQty > 0 ? '+' : '' }}{{ $mQty }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                    <span>{{ $mPrev }}</span>
                                    <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px] text-slate-400 mx-1"></i>
                                    <strong class="text-slate-900 dark:text-white">{{ $mNew }}</strong>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-user-shield text-[11px] text-slate-400"></i>
                                        <span>{{ $mUser }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400">
                                    <div class="font-mono text-[11px]">{{ $mDate }} {{ $mTime }}</div>
                                    @if($mReason)
                                        <div class="text-slate-600 dark:text-slate-300 italic text-[11px] mt-0.5">"{{ $mReason }}"</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-clock-rotate-left fa-2x mb-2 block opacity-40"></i>
                                    {{ app()->getLocale() == 'ar' ? 'لم يتم العثور على أي حركات مخزون مطابقة لشروط التصفية.' : 'No inventory movements found matching the filter criteria.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$movements->total()" />
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
