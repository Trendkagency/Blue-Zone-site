<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'لوحة القيادة التنفيذية الشاملة' : 'Executive Enterprise Command Dashboard'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة شاملة لكافة قطاعات المؤسسة: المبيعات، العمليات، المناديب الميدانيين، والموارد البشرية' : 'Comprehensive oversight across all organizational units: sales, logistics, field medical reps, and human resources.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        (app()->getLocale() === 'ar' ? 'النظرة التنفيذية' : 'Executive Overview') => route('admin.dashboard', ['view' => 'executive'])
    ]"
>
    <!-- TOP VIEW SWITCHER (For Admins & Managers) -->
    @include('admin.dashboard.partials.view_switcher', ['currentView' => 'executive'])

    <!-- 1. HERO EXECUTIVE COMMAND BANNER -->
    <div class="bz-hero-banner">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Info -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl font-black text-amber-300 shadow-inner">
                    <i class="fa-solid fa-crown"></i>
                </div>

                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white mb-0">
                            {{ auth()->user()->name }}
                        </h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-black bg-amber-400/20 text-amber-200 border border-amber-300/30">
                            <i class="fa-solid fa-shield-halved text-[11px]"></i>
                            {{ auth()->user()->role?->name ?? 'Super Admin' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-white border border-white/15">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ app()->getLocale() === 'ar' ? 'كافة القطاعات تعمل بكفاءة' : 'All Systems Operational' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-cyan-100/90 mt-1.5 mb-0 flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-network-wired text-cyan-300"></i>
                            {{ app()->getLocale() === 'ar' ? 'الإدارة المركزية العامة — الرياض' : 'Central Enterprise Headquarters — Riyadh' }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-white/80">
                            <i class="fa-regular fa-calendar text-cyan-300"></i>
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </p>

                    <!-- Shift Attendance Chip -->
                    <div class="mt-3 bz-hero-chip">
                        <i class="fa-solid fa-clock text-amber-300"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'حالة دوامك اليوم:' : 'Attendance Today:' }}</span>
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
                                <button type="submit" class="btn btn-xs bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg text-[10px] px-2.5 py-0.5 border-0 font-bold shadow-xs">
                                    <i class="fa-solid fa-fingerprint mr-1 ml-1"></i>
                                    {{ app()->getLocale() === 'ar' ? 'تسجيل الحضور الآن' : 'Clock In Now' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Fast Department Direct Links -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.mr.live-map') }}" class="bz-hero-btn-accent">
                    <i class="fa-solid fa-earth-americas text-amber-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'خريطة العمليات الميدانية' : 'Live Ops Map' }}</span>
                </a>

                <a href="{{ route('admin.dashboard', ['view' => 'operations']) }}" class="bz-hero-btn-secondary">
                    <i class="fa-solid fa-boxes-stacked text-emerald-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'لوحة العمليات' : 'Operations' }}</span>
                </a>

                <a href="{{ route('admin.dashboard', ['view' => 'hr']) }}" class="bz-hero-btn-secondary">
                    <i class="fa-solid fa-users-gear text-purple-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'الموارد البشرية' : 'HR' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. TOP 4 VIBRANT EXECUTIVE ENTERPRISE KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Total Enterprise Revenue -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-amber-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'إجمالي المبيعات والإيرادات' : 'Total Revenue' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">@currency($kpi['total_revenue'])</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'أونلاين:' : 'Online:' }} <strong>@currency($kpi['online_revenue'])</strong></span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">+18.4% ↑</span>
            </div>
        </div>

        <!-- 2. Field MR Execution Today -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-cyan-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'نشاط المناديب الميدانيين اليوم' : 'Field MR Ops Today' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 text-[#0A4F78] dark:text-cyan-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-user-doctor"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $mrVisitsDoneToday }} / {{ $mrVisitsPlannedToday }}</span>
                <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400">
                    {{ app()->getLocale() === 'ar' ? 'زيارات عيادات' : 'visits' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'مناديب في الميدان:' : 'Active reps:' }} <strong>{{ $activeRepsCount }}</strong></span>
                <a href="{{ route('admin.dashboard', ['view' => 'mr_manager']) }}" class="text-cyan-600 dark:text-cyan-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'الميدان ↗' : 'Field ↗' }}
                </a>
            </div>
        </div>

        <!-- 3. Operations & Warehouse Logistics -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-emerald-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'العمليات وطلبات الشحن' : 'Operations & Logistics' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-boxes-packing"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $kpi['total_orders'] }}</span>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                    ({{ $kpi['pending_orders'] }} {{ app()->getLocale() === 'ar' ? 'بانتظار الشحن' : 'pending' }})
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'أصناف تحت حد الأمان:' : 'Low stock SKUs:' }} <strong class="text-red-500">{{ $kpi['low_stock_count'] }}</strong></span>
                <a href="{{ route('admin.dashboard', ['view' => 'operations']) }}" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'المخزون ↗' : 'Hub ↗' }}
                </a>
            </div>
        </div>

        <!-- 4. Staff Attendance Rate Today -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-purple-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'نسبة انضباط دوام الموظفين' : 'Workforce Attendance' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-user-check"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-purple-600 dark:text-purple-400">{{ $attendanceRate }}%</span>
                <span class="text-xs font-bold text-slate-500">
                    ({{ $presentStaffCount }}/{{ $totalStaffCount }} {{ app()->getLocale() === 'ar' ? 'حاضر' : 'in' }})
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'تأخير مسجل اليوم:' : 'Late today:' }} <strong>{{ $lateStaffCount }}</strong></span>
                <a href="{{ route('admin.dashboard', ['view' => 'hr']) }}" class="text-purple-600 dark:text-purple-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'الموارد ↗' : 'HR ↗' }}
                </a>
            </div>
        </div>
    </div>

    <!-- 3. SECOND ROW: RECENT ORDERS & LIVE OPS MAP SUMMARY -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Recent Orders (7 Cols) -->
        <div class="lg:col-span-7 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-receipt"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'أحدث الطلبيات والمبيعات' : 'Recent Orders & Invoices' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'تدفق الطلبات عبر قنوات البيع الإلكترونية والمباشرة' : 'Multi-channel order transactions' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'عرض الكل' : 'View All' }}
                </a>
            </div>

            <div class="table-responsive flex-1">
                <table class="table mb-0 w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#031827] text-slate-500">
                            <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'رقم الطلب' : 'Order #' }}</th>
                            <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'العميل' : 'Customer' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'القناة' : 'Channel' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="p-3 font-bold text-end">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#0A4F78]/15 transition-colors">
                                <td class="p-3 font-bold text-primary">
                                    <a href="{{ route('admin.orders.show', $order['order_number'] ?? $order->order_number) }}">
                                        {{ $order['order_number'] ?? $order->order_number }}
                                    </a>
                                </td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $order->customer?->name ?? ($order->customer_name ?? ($order['customer_name'] ?? 'Corporate Customer')) }}
                                    </div>
                                    @php
                                        $custEmail = $order->customer?->email ?? ($order->customer_email ?? ($order['customer_email'] ?? ''));
                                    @endphp
                                    @if($custEmail)
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            {{ $custEmail }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <span class="badge {{ ($order['channel'] ?? $order->channel) === 'online' ? 'badge-primary' : 'badge-secondary' }} text-[10px] px-2 py-0.5">
                                        {{ ucfirst($order['channel'] ?? $order->channel) }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="badge {{ in_array(($order['status'] ?? $order->status), ['Delivered', 'delivered']) ? 'badge-success' : 'badge-warning' }} text-[10px] px-2 py-0.5 font-bold">
                                        {{ ucfirst($order['status'] ?? $order->status) }}
                                    </span>
                                </td>
                                <td class="p-3 text-end font-extrabold text-slate-900 dark:text-white">
                                    @currency($order['amount'] ?? $order->total)
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Field Operations Quick Telemetry (5 Cols) -->
        <div class="lg:col-span-5 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-cyan-100 dark:bg-cyan-950/50 text-[#0A4F78] dark:text-cyan-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'النشاط الميداني المباشر' : 'Live Field Telemetry' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'تغطية عيادات الأطباء ونشاط المناديب' : 'Doctor visits stream' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.mr.live-map') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'الخريطة ↗' : 'Map ↗' }}
                </a>
            </div>

            <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-[380px]">
                @forelse($recentMrVisits as $visit)
                    <div class="p-3.5 rounded-xl border border-slate-200/70 dark:border-[#15456E] bg-slate-50/50 dark:bg-[#031827]/40 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-xs text-slate-900 dark:text-white">
                                {{ $visit->contact?->name ?? 'Dr. Clinic' }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-doctor text-cyan-500 text-[10px]"></i>
                                {{ $visit->representative?->name ?? 'MR' }}
                                <span>•</span>
                                <span>{{ $visit->contact?->city?->name ?? 'Riyadh' }}</span>
                            </div>
                        </div>

                        <div class="text-end">
                            @if($visit->gps_verified)
                                <span class="badge badge-success text-[10px] px-2 py-0.5 font-bold">
                                    GPS ✓
                                </span>
                            @else
                                <span class="badge badge-warning text-[10px] px-2 py-0.5 font-bold">
                                    Manual
                                </span>
                            @endif
                            <div class="text-[10px] text-slate-400 mt-1 font-mono">
                                {{ $visit->checkin_at ? \Carbon\Carbon::parse($visit->checkin_at)->format('h:i A') : 'Today' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مسجلة اليوم' : 'No visits recorded yet.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
