<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مركز مراقبة وتنبيهات انخفاض المخزون' : 'Low Stock Alerts & Procurement Intelligence'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'رصد فوري للمنتجات المنتهية أو القريبة من النفاد مع توصيات إعادة الطلب والتحويل الفوري من المستودع المركزي.' : 'Real-time telemetry for stock depletion, safety buffer breaches, and automated procurement reorder recommendations.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.dashboard'),
        (app()->getLocale() === 'ar' ? 'تنبيهات المخزون' : 'Low Stock Alerts') => route('admin.inventory.alerts')
    ]"
>
    <x-slot name="actions">
        <!-- Quick Link to Stock Transfer -->
        <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-arrow-right-arrow-left text-teal-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'تحويل من المستودع المركزي' : 'Transfer from Central WH' }}
        </a>

        <!-- Fast Adjustments -->
        <a href="{{ route('admin.inventory.adjustments') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-plus-minus text-indigo-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'تسويات الجرد' : 'Stock Adjustments' }}
        </a>

        <!-- Supply Chain Purchase Orders -->
        @if(Route::has('admin.purchase-orders.create'))
            <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm">
                <i class="fa-solid fa-cart-flatbed mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'إنشاء أمر شراء للمورد' : 'Issue Purchase Order' }}
            </a>
        @endif
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Notices -->
        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- 1. Alerts Telemetry KPI Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Total Active Alerts -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-amber-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'إجمالي التنبيهات النشطة' : 'Active Depletion Alerts' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight font-mono">
                    {{ $totalAlerts }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'منتج' : 'items' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'تتطلب توريداً أو تحويلاً بين المواقع' : 'Below safety replenishment buffer' }}
                </div>
            </div>

            <!-- Out of Stock (0) -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-rose-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'نفد تماماً (صفر وحدة)' : 'Depleted (Zero Units)' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-ban text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 tracking-tight font-mono">
                    {{ $outOfStockCount }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'صنف منتهي' : 'depleted' }}</span>
                </div>
                <div class="text-xs text-rose-600 dark:text-rose-400 mt-1 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'أولوية قصوى للتوريد الفوري' : 'Critical - High Priority' }}</span>
                </div>
            </div>

            <!-- Critical Low (<25% of threshold) -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-orange-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'مستوى حرج جداً' : 'Critically Low Buffer' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-battery-quarter text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-orange-600 dark:text-orange-400 tracking-tight font-mono">
                    {{ $criticalCount }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'منتج' : 'items' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'أقل من ربع حد الأمان الموصى به' : 'Under 25% of safety threshold' }}
                </div>
            </div>

            <!-- Replenishment Investment Required -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-emerald-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'تكلفة التوريد المقترحة' : 'Est. Reorder Capital' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight font-mono">
                    @currency($totalReplenishmentEstCost)
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'لاستعادة رصيد الأمان القياسي لكافة الأصناف' : 'Capital required to reach optimal safety stock' }}
                </div>
            </div>

        </div>

        <!-- 2. Search & Severity Filter Bar -->
        <div class="card p-4 sm:p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
            <form method="GET" action="{{ route('admin.inventory.alerts') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 flex-wrap">
                <!-- Search -->
                <div class="flex-1 min-w-[220px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 start-3.5 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ $search }}" class="form-control ps-9 text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برمز SKU، اسم المنتج، الباركود...' : 'Filter by SKU, product formulation, barcode...' }}">
                    </div>
                </div>

                <!-- Severity Filter -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="severity" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع مستويات الخطورة' : 'All Severity Levels' }}</option>
                        <option value="out_of_stock" {{ $selectedSeverity === 'out_of_stock' ? 'selected' : '' }}>🔴 {{ app()->getLocale() === 'ar' ? 'نفد من المخزون (0)' : 'Out of Stock (0)' }}</option>
                        <option value="critical" {{ $selectedSeverity === 'critical' ? 'selected' : '' }}>🟠 {{ app()->getLocale() === 'ar' ? 'حرج جداً (<25%)' : 'Critically Low (<25%)' }}</option>
                        <option value="low_stock" {{ $selectedSeverity === 'low_stock' ? 'selected' : '' }}>🟡 {{ app()->getLocale() === 'ar' ? 'منخفض (تحت حد الأمان)' : 'Low Stock (Under Threshold)' }}</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="category_id" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع التصنيفات' : 'All Categories' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? ($cat->name_ar ?? $cat->name_en) : $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="location_id" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع المستودعات' : 'All Hubs' }}</option>
                        <option value="online" {{ $selectedLocation === 'online' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'المتجر الإلكتروني (Online)' : 'Online E-Commerce' }}</option>
                        <option value="offline" {{ $selectedLocation === 'offline' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'المعرض ونقاط البيع (POS)' : 'POS Retail Warehouse' }}</option>
                        <option value="central_wh" {{ $selectedLocation === 'central_wh' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'المستودع المركزي' : 'Central Warehouse' }}</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold px-3">
                        <i class="fa-solid fa-filter mr-1 ml-1 text-slate-400"></i>
                        {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if($search || $selectedSeverity !== 'all' || $selectedCategory !== 'all' || $selectedLocation !== 'all')
                        <a href="{{ route('admin.inventory.alerts') }}" class="btn btn-neutral btn-sm text-xs" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Reset' }}
                        </a>
                    @endif
                    <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm text-xs" title="Print Sheet">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Low Stock Alerts Data Table -->
        <div class="card bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs overflow-hidden">
            <div class="table-responsive">
                <table class="table w-full text-start" id="lowStockAlertsTable">
                    <thead class="bg-slate-50 dark:bg-[#041E34] border-b border-slate-200/80 dark:border-[#0E4A7A] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'المنتج والمواصفة' : 'Product Formulation' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'أونلاين (Online)' : 'Online Hub' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'المعرض (POS)' : 'POS Hub' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'المستودع المركزي' : 'Central Depot' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'الإجمالي الفعلي / حد الأمان' : 'Current / Safety Limit' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'مستوى الخطورة' : 'Severity Level' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'إعادة الطلب الموصى بها' : 'Suggested Reorder' }}</th>
                            <th class="py-3.5 px-4 text-end">{{ app()->getLocale() === 'ar' ? 'إجراءات سريعة' : 'Quick Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @forelse($alerts as $item)
                            @php
                                $total = $item['total_stock'];
                                $thresh = $item['threshold'];
                                $sev = $item['severity'];
                                
                                $sevBadge = match($sev) {
                                    'out_of_stock' => 'badge-danger',
                                    'critical' => 'badge-warning',
                                    default => 'badge-neutral',
                                };
                                $sevText = match($sev) {
                                    'out_of_stock' => (app()->getLocale() === 'ar' ? 'منتهي (0)' : 'Out of Stock (0)'),
                                    'critical' => (app()->getLocale() === 'ar' ? 'حرج جداً' : 'Critical Low'),
                                    default => (app()->getLocale() === 'ar' ? 'تحت حد الأمان' : 'Low Stock'),
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- Product Details -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name_en'] }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0 bg-slate-100 dark:bg-slate-800" onerror="this.src='/images/placeholder-product.svg'">
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.products.edit', $item['product_id']) }}" class="font-bold text-slate-900 dark:text-white hover:text-primary transition-colors block truncate">
                                                {{ app()->getLocale() === 'ar' ? $item['name_ar'] : $item['name_en'] }}
                                            </a>
                                            <div class="text-[11px] font-mono text-slate-400 mt-0.5 flex items-center gap-2">
                                                <span>{{ $item['sku'] }}</span>
                                                <span>•</span>
                                                <span>{{ app()->getLocale() === 'ar' ? $item['category_ar'] : $item['category'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Online Stock -->
                                <td class="py-3.5 px-4 text-center font-mono font-bold">
                                    <span class="{{ $item['stock_online'] <= 2 ? 'text-rose-600 dark:text-rose-400 font-black' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $item['stock_online'] }}
                                    </span>
                                </td>

                                <!-- POS Stock -->
                                <td class="py-3.5 px-4 text-center font-mono font-bold">
                                    <span class="{{ $item['stock_offline'] <= 2 ? 'text-rose-600 dark:text-rose-400 font-black' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $item['stock_offline'] }}
                                    </span>
                                </td>

                                <!-- Central Stock -->
                                <td class="py-3.5 px-4 text-center font-mono">
                                    @if($item['stock_central'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20">
                                            <i class="fa-solid fa-boxes-packing text-[9px]"></i>
                                            {{ $item['stock_central'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-bold">0</span>
                                    @endif
                                </td>

                                <!-- Total vs Threshold -->
                                <td class="py-3.5 px-4 text-center font-mono">
                                    <div class="text-sm font-black {{ $total <= 0 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }}">
                                        {{ $total }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'الحد:' : 'Limit:' }} {{ $thresh }}
                                    </div>
                                </td>

                                <!-- Severity -->
                                <td class="py-3.5 px-4">
                                    <span class="badge {{ $sevBadge }} text-[11px] font-black py-0.5 px-2">
                                        {{ $sevText }}
                                    </span>
                                </td>

                                <!-- Suggested Reorder -->
                                <td class="py-3.5 px-4 text-center font-mono">
                                    <div class="text-xs font-black text-emerald-600 dark:text-emerald-400">
                                        +{{ $item['suggested_reorder'] }} {{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        @currency($item['est_cost'])
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-end">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($item['can_transfer_from_central'])
                                            <!-- One-Click Rebalance Transfer from Central WH -->
                                            <a href="{{ route('admin.inventory.transfers', ['product_id' => $item['product_id'], 'from_location' => 'central_wh']) }}" class="btn btn-secondary btn-sm text-[11px] font-bold py-1 px-2.5 text-teal-600 dark:text-teal-400" title="{{ app()->getLocale() === 'ar' ? 'تحويل رصيد من المستودع المركزي' : 'Transfer stock from Central WH' }}">
                                                <i class="fa-solid fa-arrow-right-arrow-left mr-1 ml-1"></i>
                                                {{ app()->getLocale() === 'ar' ? 'تحويل' : 'Transfer' }}
                                            </a>
                                        @endif

                                        <!-- Quick Adjustment / Stock In Modal Trigger -->
                                        <button type="button" onclick="openQuickRestockModal('{{ $item['product_id'] }}', '{{ addslashes($item['name_en']) }}', '{{ $item['suggested_reorder'] }}')" class="btn btn-primary btn-sm text-[11px] font-bold py-1 px-2.5 shadow-xs" title="{{ app()->getLocale() === 'ar' ? 'إدخال مخزون فوري' : 'Quick Stock In' }}">
                                            <i class="fa-solid fa-plus mr-1 ml-1"></i>
                                            {{ app()->getLocale() === 'ar' ? 'توريد' : 'Restock' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center text-xl mb-3">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد تنبيهات انخفاض مخزون نشطة!' : 'No low stock alerts found!' }}
                                    </h4>
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ app()->getLocale() === 'ar' ? 'كافة المنتجات في جميع المستودعات أعلى من حدود الأمان القياسية.' : 'All formulations across all storage facilities are above their safety threshold.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Meta -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 dark:text-slate-400 gap-2">
                <div>
                    {{ app()->getLocale() === 'ar' ? 'إجمالي الأصناف المعروضة بالتنبيهات:' : 'Total flagged formulations:' }}
                    <strong class="text-slate-900 dark:text-white font-mono">{{ count($alerts) }}</strong>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        {{ app()->getLocale() === 'ar' ? 'منتهي (0)' : 'Depleted (0)' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        {{ app()->getLocale() === 'ar' ? 'أقل من حد الأمان' : 'Under Threshold' }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Restock Modal -->
    <div id="quickRestockModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden transition-opacity">
        <div class="bg-white dark:bg-[#062B49] border border-slate-200 dark:border-[#0E4A7A] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'إدخال وتوريد مخزون فوري' : 'Quick Stock Replenishment' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400" id="restockProductTitle">Product</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickRestockModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.inventory.adjustments.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="product_id" id="restockProductId">
                <input type="hidden" name="movement_type" value="Stock In">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'إيداع بالمستودع / الموقع' : 'Destination Warehouse / Hub' }} <span class="text-rose-500">*</span>
                    </label>
                    <select name="location_id" class="form-select text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" required>
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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الكمية الموردة (وحدة)' : 'Restock Quantity (Units)' }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="quantity" id="restockQuantity" min="1" value="20" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl font-mono font-bold" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'بيان التوريد / الملاحظات' : 'Replenishment Note' }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="reason" value="Quick low stock replenishment" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" required>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeQuickRestockModal()" class="btn btn-neutral text-xs sm:text-sm font-semibold">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary text-xs sm:text-sm font-bold shadow-md">
                        <i class="fa-solid fa-check mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'إتمام الإيداع' : 'Confirm Restock' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openQuickRestockModal(productId, productName, suggestedQty) {
            const modal = document.getElementById('quickRestockModal');
            if (!modal) return;
            document.getElementById('restockProductId').value = productId;
            document.getElementById('restockProductTitle').innerText = productName;
            document.getElementById('restockQuantity').value = suggestedQty || 20;
            modal.classList.remove('hidden');
        }

        function closeQuickRestockModal() {
            const modal = document.getElementById('quickRestockModal');
            if (modal) modal.classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeQuickRestockModal();
        });
    </script>
</x-layouts.admin>
