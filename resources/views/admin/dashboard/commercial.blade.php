<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'لوحة قيادة المبيعات التجارية والشركات (B2B CRM)' : 'Commercial B2B Sales & Corporate CRM'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة صفقات التوزيع، طلبيات سلاسل الصيدليات، وأرصدة المنتجات الطبية' : 'Wholesale client accounts, pharmacy distribution orders, and corporate sales pipeline.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        (app()->getLocale() === 'ar' ? 'المبيعات التجارية' : 'Commercial Sales') => route('admin.dashboard', ['view' => 'commercial'])
    ]"
>
    <!-- TOP VIEW SWITCHER (For Admins & Managers) -->
    @include('admin.dashboard.partials.view_switcher', ['currentView' => 'commercial'])

    <!-- 1. HERO COMMERCIAL COMMAND BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#062B49] via-[#0A4F78] to-[#1e3a8a] text-white p-6 sm:p-7 mb-6 shadow-md border border-[#15456E]">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Info -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl font-black text-blue-300 shadow-inner">
                    <i class="fa-solid fa-briefcase"></i>
                </div>

                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white mb-0">
                            {{ auth()->user()->name }}
                        </h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-black bg-blue-400/20 text-blue-200 border border-blue-300/30">
                            <i class="fa-solid fa-handshake text-[11px]"></i>
                            {{ auth()->user()->role?->name ?? (app()->getLocale() === 'ar' ? 'مندوب مبيعات الشركات (B2B)' : 'Commercial Accounts Rep') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-white border border-white/15">
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                            {{ app()->getLocale() === 'ar' ? 'مبيعات الجملة والصيدليات' : 'Pharmacy Chains & B2B' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-cyan-100/90 mt-1.5 mb-0 flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-building text-blue-300"></i>
                            {{ app()->getLocale() === 'ar' ? 'قطاع المبيعات المؤسسية والتعاقدات' : 'Corporate Accounts Division' }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-white/80">
                            <i class="fa-regular fa-calendar text-cyan-300"></i>
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </p>

                    <!-- Shift Attendance Chip -->
                    <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-black/20 backdrop-blur-sm border border-white/10 text-xs">
                        <i class="fa-solid fa-clock text-blue-300"></i>
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
                                <button type="submit" class="btn btn-xs bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-[10px] px-2.5 py-0.5 border-0 font-bold shadow-xs">
                                    <i class="fa-solid fa-fingerprint mr-1 ml-1"></i>
                                    {{ app()->getLocale() === 'ar' ? 'تسجيل الحضور الآن' : 'Clock In Now' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Fast Commercial CTAs -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.orders.index') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white border-0 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'طلبيات الجملة والتعاقدات' : 'Wholesale Orders' }}</span>
                </a>

                <a href="{{ route('admin.products.index') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-white/15 hover:bg-white/25 text-white border border-white/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-blue-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'كتالوج المنتجات والأسعار' : 'Price Catalog' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. TOP 4 VIBRANT COMMERCIAL KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Total Commercial Revenue -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-blue-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'إجمالي المبيعات التجارية' : 'Commercial Revenue' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">@currency($commercialRevenue)</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'نمو شهري مستمر:' : 'Monthly growth:' }}</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">+24.5% ↑</span>
            </div>
        </div>

        <!-- 2. Active Corporate Clients -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-emerald-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'عملاء وسلاسل الصيدليات' : 'Corporate Clients' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-hospital-user"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $corporateClientsCount }}</span>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                    {{ app()->getLocale() === 'ar' ? 'حساب تجاري نشط' : 'active accounts' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'سلاسل صيدليات ومستشفيات' : 'Pharma & Hospital chains' }}</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ app()->getLocale() === 'ar' ? 'متصل' : 'Active' }}</span>
            </div>
        </div>

        <!-- 3. Wholesale Orders Processed -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-amber-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'طلبيات التوزيع المنفذة' : 'Wholesale Orders' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-boxes-packing"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $wholesaleOrdersCount }}</span>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                    {{ app()->getLocale() === 'ar' ? 'طلب جملة' : 'B2B orders' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'متوسط قيمة الطلب:' : 'Avg Order Value:' }}</span>
                <span class="text-amber-600 dark:text-amber-400 font-bold">@currency(1240)</span>
            </div>
        </div>

        <!-- 4. Commercial Products Stock -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-purple-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'المنتجات الطبية الجاهزة' : 'Catalog Products' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-pills"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $commercialProductsCount }}</span>
                <span class="text-xs font-bold text-purple-600 dark:text-purple-400">
                    {{ app()->getLocale() === 'ar' ? 'صنف دوائي معتمد' : 'commercial SKUs' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'توفر المخزون بالمستودع:' : 'Stock status:' }}</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ app()->getLocale() === 'ar' ? 'جاهز للشحن' : 'In Stock' }}</span>
            </div>
        </div>
    </div>

    <!-- 3. MAIN SECTION: RECENT B2B ORDERS & PRODUCT CATALOG STATUS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Recent B2B Wholesale Orders (7 Cols) -->
        <div class="lg:col-span-7 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-file-invoice"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'طلبيات التوزيع وسلاسل الصيدليات الحديثة' : 'Recent Wholesale B2B Orders' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'أوامر التوريد الصادرة لعملاء قطاع الأعمال' : 'Latest pharmacy chain and corporate sales invoices' }}
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
                            <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'العميل' : 'Client' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'القيمة' : 'Amount' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="p-3 font-bold text-end">{{ app()->getLocale() === 'ar' ? 'إجراء' : 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]">
                        @foreach($recentCommercialOrders as $order)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#0A4F78]/15 transition-colors">
                                <td class="p-3 font-bold text-primary">
                                    {{ $order->order_number }}
                                </td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $order->customer?->name ?? ('Corporate Account #' . $order->customer_id) }}
                                </td>
                                <td class="p-3 text-center font-extrabold text-slate-900 dark:text-white">
                                    @currency($order->total)
                                </td>
                                <td class="p-3 text-center">
                                    @if($order->status === 'delivered')
                                        <span class="badge badge-success text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'مكتمل' : 'Delivered' }}
                                        </span>
                                    @elseif($order->status === 'shipped')
                                        <span class="badge badge-primary text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'في الطريق' : 'Shipped' }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning text-[10px] px-2 py-0.5 font-bold">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-end">
                                    <a href="{{ route('admin.orders.show', $order->order_number) }}" class="btn btn-outline btn-xs font-bold text-[11px] px-2.5 py-0.5">
                                        {{ app()->getLocale() === 'ar' ? 'تفاصيل' : 'Details' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Commercial Products & Availability (5 Cols) -->
        <div class="lg:col-span-5 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'كتالوج المنتجات وأسعار التوزيع' : 'Medical Product Catalog' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'الأسعار وتوفر الأرصدة في المستودع' : 'Wholesale pricing and stock' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'الكتالوج' : 'Catalog' }}
                </a>
            </div>

            <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-[380px]">
                @foreach($products as $product)
                    <div class="p-3 rounded-xl border border-slate-200/70 dark:border-[#15456E] bg-slate-50/50 dark:bg-[#031827]/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300 flex items-center justify-center text-xs font-black">
                                <i class="fa-solid fa-capsules"></i>
                            </span>
                            <div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">
                                    {{ $product->name }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono">
                                    {{ $product->category?->name ?? 'Medical Care' }}
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="font-black text-xs text-slate-900 dark:text-white">
                                @currency($product->price)
                            </div>
                            <span class="badge badge-success text-[10px] px-1.5 py-0.2 font-bold">
                                {{ app()->getLocale() === 'ar' ? 'متوفر' : 'In Stock' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
