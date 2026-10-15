<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'لوحة تحكم وإدارة المخزون المركزي' : 'Executive Inventory Dashboard'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'نظرة شاملة ومباشرة على كميات المخزون، تقييم الأصول، توزيع المستودعات ومؤشرات دوران البضائع.' : 'Real-time fleet inventory intelligence, asset valuations, multi-hub stock distribution, and live movement telemetry.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.dashboard'),
        (app()->getLocale() === 'ar' ? 'لوحة القيادة' : 'Dashboard') => route('admin.inventory.dashboard')
    ]"
>
    <x-slot name="actions">
        <!-- Quick Stock Control Hub -->
        <a href="{{ route('admin.inventory.control') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-sliders text-emerald-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'مركز التحكم السريع' : 'Control Hub' }}
        </a>

        <!-- Stock Allocator (Live) -->
        <a href="{{ route('admin.inventory.allocator') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-network-wired text-sky-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'موزع المخزون' : 'Visual Allocator' }}
        </a>

        <!-- Stock Transfer -->
        <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-arrow-right-arrow-left text-teal-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'تحويل مخزون' : 'Stock Transfer' }}
        </a>

        <!-- Stock Adjustments -->
        <a href="{{ route('admin.inventory.adjustments') }}" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm">
            <i class="fa-solid fa-plus-minus mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'تسوية جردية' : 'New Adjustment' }}
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

        <!-- 1. Executive Master KPIs Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Total System Inventory -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs transition-all hover:shadow-md border-s-4 border-s-indigo-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'إجمالي المخزون بالنظام' : 'Total System Fleet Units' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-boxes-stacked text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ number_format($totalUnits) }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1 font-medium">
                    <i class="fa-solid fa-network-wired text-indigo-500 text-[10px]"></i>
                    <span>{{ count($locationStockMap) }} {{ app()->getLocale() === 'ar' ? 'مستودعات ومواقع نشطة' : 'Active Storage Hubs' }}</span>
                </div>
            </div>

            <!-- Stock Valuation -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs transition-all hover:shadow-md border-s-4 border-s-emerald-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'القيمة التقديرية للمخزون' : 'Est. Inventory Valuation' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-sack-dollar text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight font-mono">
                    @currency($totalValuation)
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'محسوبة بسعر تكلفة الشراء والتصنيع' : 'Based on unit cost valuation' }}
                </div>
            </div>

            <!-- Health Score -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs transition-all hover:shadow-md border-s-4 border-s-sky-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'مؤشر سلامة التخزين' : 'Stock Health Rate' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-shield-heart text-sm"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 tracking-tight">{{ $healthRate }}%</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">({{ $healthyCount }}/{{ $totalProducts }})</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="bg-sky-500 h-2 rounded-full transition-all duration-500" style="width: {{ $healthRate }}%"></div>
                </div>
            </div>

            <!-- Low Stock Warnings -->
            <a href="{{ route('admin.inventory.alerts') }}" class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs transition-all hover:shadow-md border-s-4 border-s-amber-500 group block">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">{{ app()->getLocale() === 'ar' ? 'تنبيهات انخفاض المخزون' : 'Low Stock Alerts' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight">
                    {{ $lowStockCount }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'منتج' : 'items' }}</span>
                </div>
                <div class="text-xs text-amber-600 dark:text-amber-400 mt-1 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px]"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'استعراض وإعادة التوريد' : 'Review & Reorder' }}</span>
                </div>
            </a>

            <!-- Out of Stock -->
            <a href="{{ route('admin.inventory.alerts', ['severity' => 'out_of_stock']) }}" class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs transition-all hover:shadow-md border-s-4 border-s-rose-500 group block">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">{{ app()->getLocale() === 'ar' ? 'نفد من المخزون (صفر)' : 'Depleted (Out of Stock)' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-ban text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 tracking-tight">
                    {{ $outOfStockCount }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'صنف منتهي' : 'depleted' }}</span>
                </div>
                <div class="text-xs text-rose-600 dark:text-rose-400 mt-1 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-bolt text-[10px]"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'يتطلب إجراءً فورياً' : 'Immediate Action' }}</span>
                </div>
            </a>

        </div>

        <!-- 2. Multi-Warehouse Stock Breakdown Row -->
        <div class="card p-6 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-warehouse text-indigo-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'توزيع المخزون عبر شبكة المستودعات والمنافذ' : 'Fleet Warehouses & Storage Facilities Distribution' }}</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ app()->getLocale() === 'ar' ? 'أرصدة الوحدات الحية، قيم الأصول، ونسبة التوزيع عبر كل موقع تخزين نشط.' : 'Live physical unit balances, valuation equity, and distribution proportion per facility.' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary btn-sm text-xs font-bold">
                        <i class="fa-solid fa-building-columns mr-1 ml-1 text-indigo-400"></i>
                        {{ app()->getLocale() === 'ar' ? 'إدارة المستودعات' : 'Manage Warehouses' }}
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($locationStockMap as $locId => $locData)
                    @php
                        $pct = $totalUnits > 0 ? round(($locData['units'] / $totalUnits) * 100, 1) : 0;
                        $badgeColor = match($locId) {
                            'online' => 'bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-500/20',
                            'offline' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                            'central_wh' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-500/20',
                            default => 'bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-500/20',
                        };
                        $barColor = match($locId) {
                            'online' => 'bg-sky-500',
                            'offline' => 'bg-emerald-500',
                            'central_wh' => 'bg-purple-500',
                            default => 'bg-indigo-500',
                        };
                        $iconClass = match($locId) {
                            'online' => 'fa-solid fa-globe',
                            'offline' => 'fa-solid fa-store',
                            'central_wh' => 'fa-solid fa-boxes-packing',
                            default => 'fa-solid fa-warehouse',
                        };
                    @endphp
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg {{ $badgeColor }} border flex items-center justify-center text-sm">
                                    <i class="{{ $iconClass }}"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ app()->getLocale() === 'ar' ? $locData['name_ar'] : $locData['name_en'] }}
                                    </h4>
                                    <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">{{ $locId }}</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $badgeColor }} border">
                                {{ $pct }}%
                            </span>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between items-baseline text-xs">
                                <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'الرصيد الفعلي:' : 'Physical Units:' }}</span>
                                <span class="text-base font-black text-slate-900 dark:text-white">{{ number_format($locData['units']) }} <span class="text-xs font-normal text-slate-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span></span>
                            </div>
                            <div class="flex justify-between items-baseline text-xs">
                                <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'القيمة المالية:' : 'Valuation:' }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300 font-mono">@currency($locData['valuation'])</span>
                            </div>
                            <div class="flex justify-between items-baseline text-xs">
                                <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'الأصناف المتوفرة:' : 'Stocked SKUs:' }}</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $locData['skus'] }} {{ app()->getLocale() === 'ar' ? 'صنف' : 'SKUs' }}</span>
                            </div>

                            <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden mt-3">
                                <div class="{{ $barColor }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Two-Column Intelligence Section: Critical Watchlist & Live Movement Feed -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Critical Watchlist -->
            <div class="card p-6 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'قائمة المراقبة والتحذيرات الحرجة' : 'Critical Stock Watchlist' }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ app()->getLocale() === 'ar' ? 'المنتجات المنتهية أو القريبة من النفاد التي تحتاج تحويلاً أو توريداً عاجلاً.' : 'Depleted or safety-critical formulations needing immediate replenishment.' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.inventory.alerts') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                            <span>{{ app()->getLocale() === 'ar' ? 'عرض الكل' : 'View All' }}</span>
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px]"></i>
                        </a>
                    </div>

                    @if(count($criticalWatchlist) > 0)
                        <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @foreach($criticalWatchlist as $item)
                                <div class="py-3 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name_en'] }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0 bg-slate-100 dark:bg-slate-800" onerror="this.src='/images/placeholder-product.svg'">
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.products.edit', $item['id']) }}" class="text-sm font-bold text-slate-900 dark:text-white hover:text-primary transition-colors truncate block">
                                                {{ app()->getLocale() === 'ar' ? $item['name_ar'] : $item['name_en'] }}
                                            </a>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono flex items-center gap-2">
                                                <span>{{ $item['sku'] }}</span>
                                                <span>•</span>
                                                <span>{{ $item['category'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 flex-shrink-0">
                                        <div class="text-end">
                                            @if($item['status'] === 'out_of_stock')
                                                <span class="badge badge-danger text-xs font-black">{{ app()->getLocale() === 'ar' ? 'نفد (0)' : 'Out of Stock (0)' }}</span>
                                            @else
                                                <span class="badge badge-warning text-xs font-black">{{ $item['total_stock'] }} {{ app()->getLocale() === 'ar' ? 'متبقي' : 'left' }}</span>
                                            @endif
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                {{ app()->getLocale() === 'ar' ? 'حد الأمان:' : 'Safety limit:' }} {{ $item['threshold'] }}
                                            </div>
                                        </div>

                                        @if($item['central_stock'] > 0)
                                            <a href="{{ route('admin.inventory.transfers', ['product_id' => $item['id'], 'from_location' => 'central_wh']) }}" class="btn btn-secondary btn-sm text-[11px] font-bold py-1 px-2 text-teal-600 dark:text-teal-400" title="{{ app()->getLocale() === 'ar' ? 'يتوفر رصيد بالمستودع المركزي - تحويل فوري' : 'Central WH has stock - Quick Transfer' }}">
                                                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.inventory.adjustments', ['product_id' => $item['id']]) }}" class="btn btn-secondary btn-sm text-[11px] font-bold py-1 px-2 text-indigo-600 dark:text-indigo-400" title="{{ app()->getLocale() === 'ar' ? 'تسوية / إدخال مخزون جديد' : 'Quick Adjustment' }}">
                                                <i class="fa-solid fa-plus"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center text-xl mb-2">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'جميع مستويات المخزون بحالة ممتازة!' : 'All stock levels are optimal!' }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ app()->getLocale() === 'ar' ? 'لا توجد منتجات منتهية أو أقل من حدود الأمان حالياً.' : 'No items currently below safety replenishment buffer.' }}</p>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs">
                    <span class="text-slate-500 dark:text-slate-400">
                        {{ app()->getLocale() === 'ar' ? 'إجمالي الأصناف التي تحتاج متابعة:' : 'Items flagged for review:' }}
                        <strong class="text-slate-900 dark:text-white font-mono">{{ $lowStockCount + $outOfStockCount }}</strong>
                    </span>
                    <a href="{{ route('admin.inventory.alerts') }}" class="font-bold text-amber-600 dark:text-amber-400 hover:underline">
                        {{ app()->getLocale() === 'ar' ? 'فتح مركز التنبيهات بالكامل ↗' : 'Open Alerts Command Hub ↗' }}
                    </a>
                </div>
            </div>

            <!-- Live Stock Movements Feed -->
            <div class="card p-6 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'شريط الحركات اللوجستية المباشرة' : 'Live Movement Telemetry Feed' }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ app()->getLocale() === 'ar' ? 'سجل التدقيق الحي لآخر عمليات الإدخال، الإخراج، والتحويلات المسجلة بالنظام.' : 'Real-time auditable stream of inbound, outbound, POS, and transfers.' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.inventory.movements') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                            <span>{{ app()->getLocale() === 'ar' ? 'عرض السجل الكامل' : 'Full Ledger' }}</span>
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px]"></i>
                        </a>
                    </div>

                    @if(count($recentMovements) > 0)
                        <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @foreach($recentMovements as $m)
                                @php
                                    $qty = (int) $m->quantity;
                                    $mType = $m->movement_type ?? 'Transfer';
                                    $typeBadge = match($mType) {
                                        'Stock In', 'Return' => 'badge-success',
                                        'Online Sale', 'Offline Sale' => 'badge-primary',
                                        'Stock Transfer' => 'badge-warning',
                                        'Damaged', 'Expired', 'Stock Out' => 'badge-danger',
                                        default => 'badge-neutral',
                                    };
                                @endphp
                                <div class="py-3 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="badge {{ $typeBadge }} text-[10px] font-black py-0.5 px-2">
                                                {{ $mType }}
                                            </span>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                                {{ app()->getLocale() === 'ar' ? ($m->product_name_ar ?? $m->product_name_en) : $m->product_name_en }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                                            <span class="font-mono text-slate-400">{{ $m->sku }}</span>
                                            <span>•</span>
                                            <span>{{ $m->from_location }} &rarr; {{ $m->to_location }}</span>
                                        </div>
                                    </div>

                                    <div class="text-end flex-shrink-0">
                                        <div class="text-sm font-black font-mono {{ $qty > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            {{ $qty > 0 ? "+{$qty}" : $qty }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            {{ $m->date?->format('M d') ?? '' }} {{ $m->time }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-slate-400">
                            <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 opacity-40"></i>
                            <p class="text-xs">{{ app()->getLocale() === 'ar' ? 'لا توجد حركات مسجلة مؤخراً.' : 'No recent inventory movements recorded.' }}</p>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs">
                    <span class="text-slate-500 dark:text-slate-400">
                        {{ app()->getLocale() === 'ar' ? 'محدث بصورة آلية وفورية' : 'Live Syncing Central Ledger' }}
                    </span>
                    <a href="{{ route('admin.inventory.movements') }}" class="font-bold text-primary hover:underline">
                        {{ app()->getLocale() === 'ar' ? 'سجل الحركات المفصل ↗' : 'Detailed Movements Ledger ↗' }}
                    </a>
                </div>
            </div>

        </div>

    </div>
</x-layouts.admin>
