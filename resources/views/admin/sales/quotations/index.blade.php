<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة عروض الأسعار والصفقات' : 'Commercial Quotations & Proposals'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إنشاء ومتابعة عروض الأسعار للعملاء والشركات والعيادات، والتحويل الفوري بنقرة واحدة إلى أوامر بيع رسمية.' : 'Manage corporate & patient price quotations with one-click conversion into confirmed sales orders.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المبيعات' : 'Sales') => route('admin.sales.orders.index'),
        (app()->getLocale() === 'ar' ? 'عروض الأسعار' : 'Quotations') => route('admin.sales.quotations.index')
    ]"
>
    <x-slot name="actions">
        <!-- Create Quotation Button -->
        <a href="{{ route('admin.sales.quotations.create') }}" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'إنشاء عرض سعر جديد' : 'New Quotation' }}
        </a>

        <!-- Products & Services Link -->
        <a href="{{ route('admin.sales.products-services') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-boxes-packing text-emerald-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'دليل الأسعار والخدمات' : 'Price Book' }}
        </a>

        <!-- Sales Orders Link -->
        <a href="{{ route('admin.sales.orders.index') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-receipt text-amber-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'أوامر البيع' : 'Sales Orders' }}
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

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- 1. Quotations Intelligence KPI Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Total Quotes -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-indigo-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'إجمالي عروض الأسعار' : 'Total Quotations' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $totalQuotes }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'عرض' : 'quotes' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'صادرة لعملاء التجزئة والشركات' : 'Generated for clinics & consumers' }}
                </div>
            </div>

            <!-- Pipeline Value -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-sky-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'قيمة العروض النشطة' : 'Active Pipeline Value' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-trend-up text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 tracking-tight font-mono">
                    @currency($pipelineValue)
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'عروض قيد المتابعة والاعتماد' : 'In negotiation & client review' }}
                </div>
            </div>

            <!-- Approved / Converted -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-emerald-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'العروض المحولة لأوامر بيع' : 'Converted to Orders' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight font-mono">
                    {{ $convertedCount }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">({{ $approvedCount }} {{ app()->getLocale() === 'ar' ? 'معتمد' : 'approved' }})</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'تم تحويلها بنجاح لأوامر بيع وفواتير' : 'Converted to production sales orders' }}
                </div>
            </div>

            <!-- Conversion Rate -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-purple-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'معدل تحويل الصفقات' : 'Quote Conversion Rate' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-trophy text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-purple-600 dark:text-purple-400 tracking-tight font-mono">
                    {{ $conversionRate }}%
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'نسبة نجاح إتمام الصفقات' : 'Quotation deal closing efficiency' }}
                </div>
            </div>

        </div>

        <!-- 2. Search & Filter Bar -->
        <div class="card p-4 sm:p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
            <form method="GET" action="{{ route('admin.sales.quotations.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 flex-wrap">
                <!-- Search -->
                <div class="flex-1 min-w-[220px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 start-3.5 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ $search }}" class="form-control ps-9 text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم العرض، اسم العميل، الشركة، البريد الإلكتروني...' : 'Filter by Quote #, customer name, company...' }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="status" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع حالات العروض' : 'All Quotation Statuses' }}</option>
                        <option value="Draft" {{ $selectedStatus === 'Draft' ? 'selected' : '' }}>📝 {{ app()->getLocale() === 'ar' ? 'مسودة (Draft)' : 'Draft' }}</option>
                        <option value="Sent" {{ $selectedStatus === 'Sent' ? 'selected' : '' }}>📨 {{ app()->getLocale() === 'ar' ? 'مرسل للعميل (Sent)' : 'Sent to Client' }}</option>
                        <option value="Approved" {{ $selectedStatus === 'Approved' ? 'selected' : '' }}>✅ {{ app()->getLocale() === 'ar' ? 'معتمد من العميل (Approved)' : 'Approved' }}</option>
                        <option value="Converted" {{ $selectedStatus === 'Converted' ? 'selected' : '' }}>🔄 {{ app()->getLocale() === 'ar' ? 'تم التحويل لأمر بيع (Converted)' : 'Converted to Order' }}</option>
                        <option value="Expired" {{ $selectedStatus === 'Expired' ? 'selected' : '' }}>⌛ {{ app()->getLocale() === 'ar' ? 'منتهي الصلاحية (Expired)' : 'Expired' }}</option>
                        <option value="Rejected" {{ $selectedStatus === 'Rejected' ? 'selected' : '' }}>❌ {{ app()->getLocale() === 'ar' ? 'ملغي / مرفوض (Rejected)' : 'Rejected' }}</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold px-3">
                        <i class="fa-solid fa-filter mr-1 ml-1 text-slate-400"></i>
                        {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if($search || $selectedStatus !== 'all')
                        <a href="{{ route('admin.sales.quotations.index') }}" class="btn btn-neutral btn-sm text-xs" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Reset' }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 3. Quotations Data Ledger Table -->
        <div class="card bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs overflow-hidden">
            <div class="table-responsive">
                <table class="table w-full text-start" id="quotationsTable">
                    <thead class="bg-slate-50 dark:bg-[#041E34] border-b border-slate-200/80 dark:border-[#0E4A7A] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'رقم العرض' : 'Quotation #' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'العميل / المنشأة' : 'Customer / Account' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'تاريخ الإصدار' : 'Issue Date' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'صلاحية العرض' : 'Valid Until' }}</th>
                            <th class="py-3.5 px-4 text-end">{{ app()->getLocale() === 'ar' ? 'القيمة الإجمالية' : 'Total Amount' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="py-3.5 px-4 text-end">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @forelse($quotations as $quote)
                            @php
                                $badgeClass = match($quote->status) {
                                    'Approved' => 'badge-success',
                                    'Converted' => 'badge-primary',
                                    'Sent' => 'badge-warning',
                                    'Draft' => 'badge-neutral',
                                    'Rejected', 'Expired' => 'badge-danger',
                                    default => 'badge-neutral',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- Quote Number -->
                                <td class="py-3.5 px-4 font-mono font-bold text-primary">
                                    <a href="{{ route('admin.sales.quotations.show', $quote->id) }}" class="hover:underline">
                                        {{ $quote->quotation_number }}
                                    </a>
                                </td>

                                <!-- Customer -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $quote->customer_name }}
                                    </div>
                                    @if($quote->company_name)
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-building text-[10px]"></i>
                                            <span>{{ $quote->company_name }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Issue Date -->
                                <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300">
                                    {{ $quote->date?->format('Y-m-d') }}
                                </td>

                                <!-- Valid Until -->
                                <td class="py-3.5 px-4 font-mono text-slate-500 dark:text-slate-400">
                                    @if($quote->valid_until)
                                        <span class="{{ $quote->valid_until->isPast() && $quote->status !== 'Converted' ? 'text-rose-500 font-bold' : '' }}">
                                            {{ $quote->valid_until->format('Y-m-d') }}
                                        </span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>

                                <!-- Total -->
                                <td class="py-3.5 px-4 text-end font-mono font-black text-sm text-slate-900 dark:text-white">
                                    @currency($quote->total)
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4">
                                    <span class="badge {{ $badgeClass }} text-[11px] font-black py-0.5 px-2">
                                        {{ $quote->status }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-end">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- View -->
                                        <a href="{{ route('admin.sales.quotations.show', $quote->id) }}" class="btn btn-secondary btn-sm text-[11px] font-bold py-1 px-2.5" title="{{ app()->getLocale() === 'ar' ? 'استعراض التفاصيل' : 'View Details' }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <!-- Print -->
                                        <a href="{{ route('admin.sales.quotations.print', $quote->id) }}" target="_blank" class="btn btn-secondary btn-sm text-[11px] font-bold py-1 px-2.5" title="{{ app()->getLocale() === 'ar' ? 'طباعة العرض' : 'Print Proposal' }}">
                                            <i class="fa-solid fa-print"></i>
                                        </a>

                                        <!-- Convert to Order if not converted yet -->
                                        @if($quote->status !== 'Converted')
                                            <form action="{{ route('admin.sales.quotations.convert', $quote->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل تريد تأكيد تحويل عرض السعر هذا إلى أمر بيع رسمي؟' : 'Convert this quotation into an official sales order?' }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm text-[11px] font-bold py-1 px-2 text-emerald-300" title="{{ app()->getLocale() === 'ar' ? 'تحويل فوري لأمر بيع' : 'Convert to Sales Order' }}">
                                                    <i class="fa-solid fa-arrow-right-to-bracket mr-1 ml-1"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'تحويل لأمر' : 'Order' }}
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.orders.show', $quote->converted_order_id ?? 1) }}" class="btn btn-neutral btn-sm text-[11px] font-bold py-1 px-2 text-primary" title="{{ app()->getLocale() === 'ar' ? 'فتح أمر البيع المرتبط' : 'View Order' }}">
                                                <i class="fa-solid fa-receipt mr-1 ml-1"></i>
                                                {{ app()->getLocale() === 'ar' ? 'الأمر' : 'Order' }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 mx-auto flex items-center justify-center text-xl mb-3 opacity-60">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد عروض أسعار مسجلة حالياً' : 'No quotations registered yet' }}
                                    </h4>
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ app()->getLocale() === 'ar' ? 'ابدأ بإنشاء أول عرض سعر عبر زر "إنشاء عرض سعر جديد" بالأعلى.' : 'Create your first commercial quotation using the button above.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($quotations->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$quotations->total()" />
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
