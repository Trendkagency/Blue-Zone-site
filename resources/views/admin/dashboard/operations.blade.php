<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مركز قيادة العمليات والمخزون والتوزيع' : 'Operations, Warehouse & Logistics Command'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة تدفق الطلبات، مستويات المخزون الحرج، وحركات المستودعات المباشرة' : 'Real-time order fulfillment pipelines, critical stock alerts, and warehouse movements.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        (app()->getLocale() === 'ar' ? 'العمليات والمخزون' : 'Operations & Logistics') => route('admin.dashboard', ['view' => 'operations'])
    ]"
>
    <!-- TOP VIEW SWITCHER (For Admins & Managers) -->
    @include('admin.dashboard.partials.view_switcher', ['currentView' => 'operations'])

    <!-- 1. HERO OPERATIONS COMMAND BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#062B49] via-[#0A4F78] to-[#115e59] text-white p-6 sm:p-7 mb-6 shadow-md border border-[#15456E]">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Info -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl font-black text-emerald-300 shadow-inner">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>

                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white mb-0">
                            {{ auth()->user()->name }}
                        </h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-black bg-emerald-400/20 text-emerald-200 border border-emerald-300/30">
                            <i class="fa-solid fa-gear text-[11px]"></i>
                            {{ auth()->user()->role?->name ?? (app()->getLocale() === 'ar' ? 'إدارة العمليات والمستودعات' : 'Operations Management') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-white border border-white/15">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ app()->getLocale() === 'ar' ? 'المستودعات متزامنة' : 'Warehouses Synced' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-cyan-100/90 mt-1.5 mb-0 flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-warehouse text-emerald-300"></i>
                            {{ app()->getLocale() === 'ar' ? 'المستودع الرئيسي — الرياض' : 'Main Hub — Riyadh' }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-white/80">
                            <i class="fa-regular fa-calendar text-cyan-300"></i>
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </p>

                    <!-- Shift Attendance Chip -->
                    <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-black/20 backdrop-blur-sm border border-white/10 text-xs">
                        <i class="fa-solid fa-clock text-emerald-300"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'حالة الدوام اليوم:' : 'Attendance Today:' }}</span>
                        @if($todayAttendance && $todayAttendance->check_in)
                            <span class="font-bold text-emerald-300 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                {{ app()->getLocale() === 'ar' ? 'تم الحضور' : 'Clocked In' }} ({{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }})
                            </span>
                            @if(!$todayAttendance->check_out)
                                <form method="POST" action="{{ route('admin.attendance.self-checkout') }}" class="inline m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-xs bg-red-500/80 hover:bg-red-600 text-white rounded-lg text-[10px] px-2 py-0.5 ml-1 mr-1 border-0">
                                        <i class="fa-solid fa-right-from-bracket mr-1 ml-1"></i>
                                        {{ app()->getLocale() === 'ar' ? 'تسجيل انصراف' : 'Punch Out' }}
                                    </button>
                                </form>
                            @else
                                <span class="font-bold text-cyan-200">
                                    — {{ app()->getLocale() === 'ar' ? 'انصرف' : 'Out' }} ({{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }})
                                </span>
                            @endif
                        @else
                            <form method="POST" action="{{ route('admin.attendance.self-checkin') }}" class="inline m-0">
                                @csrf
                                <button type="submit" class="btn btn-xs bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-[10px] px-2.5 py-0.5 border-0 font-bold shadow-xs">
                                    <i class="fa-solid fa-fingerprint mr-1 ml-1"></i>
                                    {{ app()->getLocale() === 'ar' ? 'تسجيل الحضور الآن' : 'Clock In Now' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Fast Operations CTAs -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.orders.index') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white border-0 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'معالجة وتجهيز الطلبات' : 'Fulfill Orders' }}</span>
                </a>

                <a href="{{ route('admin.inventory.index') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-white/15 hover:bg-white/25 text-white border border-white/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-dolly text-emerald-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'سجل المخزون والأرصدة' : 'Stock Inventory' }}</span>
                </a>

                <a href="{{ route('admin.products.index') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-barcode text-cyan-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'كتالوج المنتجات' : 'Products' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. TOP 4 VIBRANT OPERATIONS KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Pending Fulfillment Orders -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-amber-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'طلبات بانتظار التجهيز' : 'Pending Fulfillment' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-boxes-packing"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $pendingOrdersCount }}</span>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                    {{ app()->getLocale() === 'ar' ? 'بحاجة لتجهيز فوري' : 'require dispatch' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'إجمالي طلبات النظام:' : 'Total Orders:' }} <strong>{{ $totalOrdersCount }}</strong></span>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'بدء التجهيز ↗' : 'Process ↗' }}
                </a>
            </div>
        </div>

        <!-- 2. Critical Low Stock Alerts -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-red-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'تنبيهات نقص المخزون' : 'Low Stock Alerts' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-red-600 dark:text-red-400">{{ $lowStockCount }}</span>
                <span class="text-xs font-bold text-red-500">
                    {{ app()->getLocale() === 'ar' ? 'أصناف تحت حد الأمان' : 'SKUs below safety' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'أصناف نافدة تماماً:' : 'Out of stock:' }} <strong class="text-red-500">{{ $outOfStockCount }}</strong></span>
                <a href="{{ route('admin.inventory.index', ['filter' => 'low_stock']) }}" class="text-red-500 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'إعادة طلب ↗' : 'Restock ↗' }}
                </a>
            </div>
        </div>

        <!-- 3. Available Warehouse Inventory Units -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-emerald-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'إجمالي وحدات المخزون' : 'Available Stock Units' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-warehouse"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($totalStockUnits) }}</span>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                    {{ app()->getLocale() === 'ar' ? 'قطعة جاهزة للشحن' : 'units ready' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'إجمالي الأصناف (SKU):' : 'Active SKUs:' }} <strong>{{ $totalSkuCount }}</strong></span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ app()->getLocale() === 'ar' ? 'مستقر ✓' : 'Healthy ✓' }}</span>
            </div>
        </div>

        <!-- 4. Warehouse Movements Recorded -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-purple-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'حركات المستودع المسجلة' : 'Stock Movements' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $movementsCount }}</span>
                <span class="text-xs font-bold text-purple-600 dark:text-purple-400">
                    {{ app()->getLocale() === 'ar' ? 'حركات وارد / صادر' : 'in/out logged' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'المستودعات النشطة:' : 'Active Hubs:' }} <strong>{{ $activeHubsCount }}</strong></span>
                <a href="{{ route('admin.inventory.index') }}" class="text-purple-600 dark:text-purple-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'السجل ↗' : 'Log ↗' }}
                </a>
            </div>
        </div>
    </div>

    <!-- 3. MAIN SECTION: CRITICAL STOCK ALERTS & PENDING FULFILLMENT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Critical Replenishment Table (7 Cols) -->
        <div class="lg:col-span-7 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'أصناف قاربت على النفاد (تتطلب توريد فوري)' : 'Critical Replenishment Alert SKUs' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'المنتجات التي وصل رصيدها المتوفر إلى حد الأمان أو أقل' : 'Products where current available stock is below minimum safety threshold' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'عرض الكل' : 'View All' }}
                </a>
            </div>

            <div class="table-responsive flex-1">
                <table class="table mb-0 w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#031827] text-slate-500">
                            <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'المنتج / الصنف' : 'Product / SKU' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الرصيد المتاح' : 'Available Stock' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'حد الأمان' : 'Safety Min' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="p-3 font-bold text-end">{{ app()->getLocale() === 'ar' ? 'إجراء' : 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]">
                        @forelse($criticalStockItems as $item)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#0A4F78]/15 transition-colors">
                                <td class="p-3">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $item->product?->name ?? 'Medical Product' }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono">
                                        SKU: {{ $item->sku ?: ('BZ-SKU-' . $item->id) }}
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="font-extrabold text-sm {{ $item->available_stock <= 0 ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}">
                                        {{ $item->available_stock }}
                                    </span>
                                </td>
                                <td class="p-3 text-center text-slate-500">
                                    {{ $item->low_stock_threshold ?? 10 }}
                                </td>
                                <td class="p-3 text-center">
                                    @if($item->available_stock <= 0)
                                        <span class="badge badge-danger text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'نافد تماماً' : 'Out of Stock' }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'حرج جداً' : 'Low Stock' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-end">
                                    <a href="{{ route('admin.inventory.show', $item->id) }}" class="btn btn-primary btn-xs text-[11px] font-bold px-2.5 py-1">
                                        <i class="fa-solid fa-plus mr-1 ml-1"></i>
                                        {{ app()->getLocale() === 'ar' ? 'توريد' : 'Restock' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-2"></i>
                                    <div>{{ app()->getLocale() === 'ar' ? 'لا توجد أصناف تحت حد الأمان حالياً، المخزون متوازن!' : 'No critical low-stock items detected. Inventory healthy!' }}</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders Requiring Fulfillment (5 Cols) -->
        <div class="lg:col-span-5 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-cyan-100 dark:bg-cyan-950/50 text-[#0A4F78] dark:text-cyan-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'أوامر التجهيز والشحن' : 'Fulfillment Queue' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'الطلبات الجاهزة للتعبئة والشحن' : 'Orders queued for dispatch' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                </a>
            </div>

            <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-[380px]">
                @forelse($fulfillmentOrders as $order)
                    <div class="p-3.5 rounded-xl border border-slate-200/70 dark:border-[#15456E] bg-slate-50/50 dark:bg-[#031827]/40 flex items-center justify-between gap-3 transition-all hover:border-[#0A4F78]">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white dark:bg-[#062B49] border border-slate-200 dark:border-[#15456E] flex items-center justify-center text-xs font-black text-[#0A4F78] dark:text-cyan-400 shadow-xs">
                                <i class="fa-solid fa-box"></i>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-xs text-slate-900 dark:text-white">
                                        {{ $order->order_number }}
                                    </span>
                                    <span class="badge {{ $order->status === 'processing' ? 'badge-primary' : 'badge-warning' }} text-[10px] px-1.5 py-0.5 font-bold">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $order->created_at ? $order->created_at->diffForHumans() : 'Recently' }} • @currency($order->total)
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.orders.show', $order->order_number) }}" class="btn btn-outline btn-xs font-bold text-[11px] px-2.5 py-1">
                            {{ app()->getLocale() === 'ar' ? 'تجهيز' : 'Fulfill' }}
                            <i class="fa-solid fa-arrow-right mr-1 ml-1 text-[10px]"></i>
                        </a>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400">
                        <i class="fa-solid fa-box-open text-2xl mb-2 text-slate-300"></i>
                        <div>{{ app()->getLocale() === 'ar' ? 'لا توجد طلبات معلقة حالياً' : 'No pending orders in fulfillment queue' }}</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 4. RECENT STOCK MOVEMENTS AUDIT LOG -->
    <div class="card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden mb-6">
        <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </span>
                <div>
                    <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                        {{ app()->getLocale() === 'ar' ? 'سجل حركات المستودعات المباشرة (Logistics Audit Stream)' : 'Real-Time Warehouse Movements & Audit' }}
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                        {{ app()->getLocale() === 'ar' ? 'آخر عمليات الاستلام، الصرف، والتسويات الجردية' : 'Latest inbound receipts, order dispatches, and stock adjustments' }}
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                {{ app()->getLocale() === 'ar' ? 'عرض سجل الحركات' : 'Movements Log' }}
            </a>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#031827] text-slate-500">
                        <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'الصنف / المنتج' : 'Item / Product' }}</th>
                        <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'نوع الحركة' : 'Movement Type' }}</th>
                        <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Quantity' }}</th>
                        <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'المرجع / الملاحظة' : 'Reference / Notes' }}</th>
                        <th class="p-3 font-bold text-end">{{ app()->getLocale() === 'ar' ? 'التوقيت' : 'Timestamp' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]">
                    @forelse($recentMovements as $mov)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-[#0A4F78]/15 transition-colors">
                            <td class="p-3 font-bold text-slate-900 dark:text-white">
                                {{ $mov->product?->name ?? ($mov->product_name_ar ?? ($mov->product_name_en ?? ($mov['product_name'] ?? 'Product SKU'))) }}
                            </td>
                            <td class="p-3 text-center">
                                @php
                                    $type = strtolower($mov->movement_type ?? $mov['type'] ?? 'inbound');
                                @endphp
                                @if(str_contains($type, 'in') || str_contains($type, 'receive'))
                                    <span class="badge badge-success text-[10px] px-2 py-0.5 font-bold">
                                        <i class="fa-solid fa-arrow-down mr-1 ml-1"></i>
                                        {{ app()->getLocale() === 'ar' ? 'استلام وارد' : 'Inbound' }}
                                    </span>
                                @elseif(str_contains($type, 'out') || str_contains($type, 'dispatch'))
                                    <span class="badge badge-primary text-[10px] px-2 py-0.5 font-bold">
                                        <i class="fa-solid fa-arrow-up mr-1 ml-1"></i>
                                        {{ app()->getLocale() === 'ar' ? 'صرف وتوزيع' : 'Outbound' }}
                                    </span>
                                @else
                                    <span class="badge badge-warning text-[10px] px-2 py-0.5 font-bold">
                                        <i class="fa-solid fa-sliders mr-1 ml-1"></i>
                                        {{ app()->getLocale() === 'ar' ? 'تسوية جردية' : 'Adjustment' }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 text-center font-extrabold text-sm text-slate-900 dark:text-white">
                                {{ $mov->quantity ?? $mov['quantity'] ?? '0' }}
                            </td>
                            <td class="p-3 text-slate-500">
                                {{ $mov->note ?? ($mov->notes ?? ($mov['reference'] ?? 'Standard Warehouse Transaction')) }}
                            </td>
                            <td class="p-3 text-end text-slate-400">
                                {{ isset($mov->created_at) ? $mov->created_at->diffForHumans() : 'Today' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد حركات مسجلة مؤخراً' : 'No recent movements found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
