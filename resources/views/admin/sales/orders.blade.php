<x-layouts.admin 
    :pageTitle="app()->getLocale() == 'ar' ? 'أوامر المبيعات' : 'Sales Orders'" 
    :pageSubtitle="app()->getLocale() == 'ar' ? 'إدارة مركزية لجميع أوامر المبيعات (المتجر الإلكتروني وعروض الأسعار المعمدة)' : 'Unified omnichannel sales orders hub (E-Commerce and Converted Quotations)'"
    :breadcrumbs="[
        (app()->getLocale() == 'ar' ? 'المبيعات' : 'Sales') => route('admin.sales.orders.index'),
        (app()->getLocale() == 'ar' ? 'أوامر المبيعات' : 'Sales Orders') => route('admin.sales.orders.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.sales.quotations.create') }}" class="btn btn-outline font-bold shadow-sm">
                <i class="fa-solid fa-file-circle-plus mr-1.5 ml-1.5 text-sky-500"></i>
                {{ app()->getLocale() == 'ar' ? 'إنشاء عرض سعر' : 'New Quotation' }}
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-file-invoice-dollar mr-1.5 ml-1.5 text-amber-500"></i>
                {{ app()->getLocale() == 'ar' ? 'سجل الفواتير الضريبية' : 'Tax Invoices' }}
            </a>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Sales Executive KPI Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- Total Revenue -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() == 'ar' ? 'إجمالي إيرادات المبيعات' : 'Total Sales Revenue' }}
                </span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-sack-dollar text-base"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                {{ number_format($totalRevenue, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                <span class="text-emerald-500 font-bold"><i class="fa-solid fa-shield-check"></i> 100%</span>
                <span>{{ app()->getLocale() == 'ar' ? 'شامل ضريبة القيمة المضافة 15%' : 'Includes 15% VAT' }}</span>
            </div>
        </div>

        <!-- Orders Volume & Omnichannel breakdown -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() == 'ar' ? 'إجمالي أوامر البيع' : 'Total Sales Orders' }}
                </span>
                <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <i class="fa-solid fa-cart-shopping text-base"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                {{ number_format($totalOrdersCount) }}
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-2">
                <span class="text-sky-500 font-semibold">Web: {{ $onlineCount }}</span> •
                <span class="text-indigo-500 font-semibold">B2B: {{ $quotationCount }}</span>
                @if($offlineCount > 0)
                    • <span class="text-slate-400 font-medium">{{ app()->getLocale() == 'ar' ? 'سجل مؤرشف' : 'Archived' }}: {{ $offlineCount }}</span>
                @endif
            </div>
        </div>

        <!-- Pending Fulfillment -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() == 'ar' ? 'قيد التجهيز والمعالجة' : 'Processing Queue' }}
                </span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i class="fa-solid fa-boxes-packing text-base"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-500 font-mono">
                {{ number_format($pendingProcessingCount) }}
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                {{ app()->getLocale() == 'ar' ? 'تتطلب الشحن أو التسليم الفعلي' : 'Orders pending fulfillment' }}
            </div>
        </div>

        <!-- Average Order Value (AOV) -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() == 'ar' ? 'متوسط قيمة الأمر (AOV)' : 'Average Order Value' }}
                </span>
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-base"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                {{ number_format($avgOrderValue, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                {{ app()->getLocale() == 'ar' ? 'معدل سلة الشراء لكل عميل' : 'Blended basket size per order' }}
            </div>
        </div>

    </div>

    <!-- Filter & Channel Navigation Bar -->
    <div class="card p-4 sm:p-5 border border-slate-200 dark:border-slate-800 rounded-2xl mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.sales.orders.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            <!-- Channel Switcher Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                <a href="{{ route('admin.sales.orders.index', array_merge(request()->query(), ['channel' => 'all'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($selectedChannel === 'all' || !$selectedChannel) ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-globe mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'كافة القنوات' : 'All Channels' }}
                </a>

                <a href="{{ route('admin.sales.orders.index', array_merge(request()->query(), ['channel' => 'online'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedChannel === 'online' ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-cart-shopping mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'المتجر الإلكتروني' : 'E-Commerce' }} ({{ $onlineCount }})
                </a>

                @if($offlineCount > 0)
                <a href="{{ route('admin.sales.orders.index', array_merge(request()->query(), ['channel' => 'offline'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedChannel === 'offline' ? 'bg-slate-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-box-archive mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'الأرشيف التاريخي' : 'Historical Archive' }} ({{ $offlineCount }})
                </a>
                @endif

                <a href="{{ route('admin.sales.orders.index', array_merge(request()->query(), ['channel' => 'quotation'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedChannel === 'quotation' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-file-invoice mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'عروض الأسعار المعمدة' : 'B2B Quotes' }} ({{ $quotationCount }})
                </a>
            </div>

            <!-- Search & Dropdown Filters -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <input type="hidden" name="channel" value="{{ $selectedChannel }}">
                
                <div class="relative flex-1 sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute top-3 rtl:right-3 ltr:left-3 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="{{ app()->getLocale() == 'ar' ? 'بحث برقم الطلب، الفاتورة، أو العميل...' : 'Search order, invoice, customer...' }}" 
                           class="form-control text-xs rtl:pr-8 ltr:pl-8 py-2 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 w-full">
                </div>

                <select name="status" onchange="this.form.submit()" class="form-select text-xs py-2 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900">
                    <option value="all">{{ app()->getLocale() == 'ar' ? 'كل حالات الطلب' : 'All Order Statuses' }}</option>
                    @foreach(['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'] as $st)
                        <option value="{{ $st }}" {{ $selectedStatus === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>

                <select name="payment_status" onchange="this.form.submit()" class="form-select text-xs py-2 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900">
                    <option value="all">{{ app()->getLocale() == 'ar' ? 'كل حالات الدفع' : 'All Payment Statuses' }}</option>
                    @foreach(['Paid', 'Pending', 'Partially Paid', 'Refunded', 'Failed'] as $pst)
                        <option value="{{ $pst }}" {{ $selectedPayment === $pst ? 'selected' : '' }}>{{ $pst }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold rounded-xl py-2 px-3">
                    <i class="fa-solid fa-filter mr-1 ml-1 text-slate-500"></i> {{ app()->getLocale() == 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>

        </form>
    </div>

    <!-- Sales Orders Table Card -->
    <div class="card overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49]">
        
        <div class="card-header p-5 bg-slate-50/70 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                <i class="fa-solid fa-file-invoice-dollar text-sky-600 dark:text-sky-400"></i>
                {{ app()->getLocale() == 'ar' ? 'سجل أوامر البيع والفواتير المعتمدة' : 'Commercial Sales Orders Ledger' }}
                <span class="text-xs bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300 px-2.5 py-0.5 rounded-full font-bold">
                    {{ $orders->total() }}
                </span>
            </h3>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-sky-600 dark:text-sky-400 font-bold hover:underline">
                    {{ app()->getLocale() == 'ar' ? 'العرض التفصيلي المتقدم' : 'Legacy Detailed Grid' }} &rarr;
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/80 text-xs uppercase text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4">{{ app()->getLocale() == 'ar' ? 'رقم الطلب والفاتورة' : 'Order & Invoice #' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th class="p-4 text-center">{{ app()->getLocale() == 'ar' ? 'قناة البيع' : 'Channel' }}</th>
                        <th class="p-4 text-center">{{ app()->getLocale() == 'ar' ? 'حالة الدفع' : 'Payment' }}</th>
                        <th class="p-4 text-center">{{ app()->getLocale() == 'ar' ? 'حالة الطلب' : 'Status' }}</th>
                        <th class="p-4 text-right rtl:text-left">{{ app()->getLocale() == 'ar' ? 'الإجمالي' : 'Total' }}</th>
                        <th class="p-4 text-center">{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th class="p-4 text-center w-28">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-sm">
                    @forelse($orders as $ord)
                        @php
                            $ordNum = $ord->order_number ?? 'BZ-000';
                            $invNum = $ord->invoice_number ?? ('INV-' . str_replace(['BZ-', 'ORD-', 'POS-'], '', $ordNum));
                            $channel = $ord->channel ?? 'online';
                            
                            $channelBadge = match($channel) {
                                'offline' => ['bg' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20', 'icon' => 'fa-box-archive', 'label' => app()->getLocale() == 'ar' ? 'سجل مؤرشف' : 'Archived'],
                                'quotation' => ['bg' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20', 'icon' => 'fa-file-invoice', 'label' => 'B2B Quote'],
                                default => ['bg' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20', 'icon' => 'fa-globe', 'label' => 'Online Hub'],
                            };

                            $payBadge = match(strtolower($ord->payment_status ?? 'pending')) {
                                'paid' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                'partially_paid' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                'refunded' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                            };

                            $statusBadge = match(strtolower($ord->status ?? 'pending')) {
                                'delivered', 'completed' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                'shipped' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                                'processing', 'confirmed' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                                'cancelled' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                default => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <!-- Order / Invoice # -->
                            <td class="p-4">
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="font-bold text-sky-600 dark:text-sky-400 hover:underline block font-mono">
                                    {{ $ordNum }}
                                </a>
                                <span class="text-xs text-slate-400 font-mono">
                                    {{ $invNum }}
                                </span>
                            </td>

                            <!-- Customer -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $ord->customer_name ?? 'Client' }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ $ord->customer_phone ?? $ord->customer_email ?? '—' }}
                                </div>
                            </td>

                            <!-- Channel -->
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border {{ $channelBadge['bg'] }}">
                                    <i class="fa-solid {{ $channelBadge['icon'] }}"></i>
                                    {{ $channelBadge['label'] }}
                                </span>
                            </td>

                            <!-- Payment -->
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-bold border {{ $payBadge }}">
                                    {{ ucfirst($ord->payment_status ?? 'Pending') }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ str_ireplace(['POS Terminal', 'POS'], ['Payment Terminal', 'Archived Terminal'], $ord->payment_method ?? 'MADA / Card') }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-bold border {{ $statusBadge }}">
                                    {{ ucfirst($ord->status ?? 'Pending') }}
                                </span>
                            </td>

                            <!-- Total -->
                            <td class="p-4 text-right rtl:text-left font-mono font-bold text-slate-900 dark:text-white">
                                {{ number_format($ord->total, 2) }} SAR
                            </td>

                            <!-- Date -->
                            <td class="p-4 text-center text-xs text-slate-500 dark:text-slate-400 font-mono">
                                {{ $ord->date?->format('Y-m-d') ?? $ord->created_at?->format('Y-m-d') ?? '—' }}
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-secondary btn-sm p-1.5 rounded-lg" title="{{ app()->getLocale() == 'ar' ? 'معاينة تفاصيل الطلب' : 'View Order Dossier' }}">
                                        <i class="fa-solid fa-eye text-xs text-sky-500"></i>
                                    </a>
                                    <a href="{{ route('admin.invoices.show', $ord->id) }}" class="btn btn-secondary btn-sm p-1.5 rounded-lg" title="{{ app()->getLocale() == 'ar' ? 'عرض الفاتورة الضريبية' : 'Tax Invoice' }}">
                                        <i class="fa-solid fa-file-invoice text-xs text-indigo-500"></i>
                                    </a>
                                    <a href="{{ route('admin.invoices.print', $ord->id) }}" target="_blank" class="btn btn-secondary btn-sm p-1.5 rounded-lg" title="{{ app()->getLocale() == 'ar' ? 'طباعة رسمية' : 'Print Invoice' }}">
                                        <i class="fa-solid fa-print text-xs text-emerald-500"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">
                                <i class="fa-solid fa-cart-arrow-down text-4xl mb-3 opacity-40"></i>
                                <p class="text-sm m-0">{{ app()->getLocale() == 'ar' ? 'لا توجد أوامر بيع مطابقة للمحددات الحالية.' : 'No sales orders found matching current criteria.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 flex items-center justify-between">
                <div class="text-xs text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() == 'ar' ? 'عرض الصفحة' : 'Showing Page' }} <span class="font-bold text-slate-800 dark:text-slate-200">{{ $orders->currentPage() }}</span> {{ app()->getLocale() == 'ar' ? 'من' : 'of' }} <span class="font-bold text-slate-800 dark:text-slate-200">{{ $orders->lastPage() }}</span>
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        @endif

    </div>

</x-layouts.admin>
