<x-layouts.admin 
    :pageTitle="(app()->getLocale() == 'ar' ? 'عرض السعر: ' : 'Quotation #') . $quotation->quotation_number" 
    :pageSubtitle="app()->getLocale() == 'ar' ? 'تفاصيل العرض التجاري، البنود، واعتماد أمر البيع' : 'Commercial quotation specifications, line items & conversion'"
    :breadcrumbs="[
        (app()->getLocale() == 'ar' ? 'المبيعات' : 'Sales') => route('admin.sales.orders.index'),
        (app()->getLocale() == 'ar' ? 'عروض الأسعار' : 'Quotations') => route('admin.sales.quotations.index'),
        $quotation->quotation_number => route('admin.sales.quotations.show', $quotation->id)
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            @php
                $statusColor = match($quotation->status) {
                    'Approved' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                    'Converted' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                    'Sent' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                    'Rejected' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                    'Expired' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                    default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                };
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold border {{ $statusColor }}">
                <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 ml-1.5"></span>
                {{ $quotation->status }}
            </span>

            <a href="{{ route('admin.sales.quotations.print', $quotation->id) }}" target="_blank" class="btn btn-outline font-bold shadow-sm">
                <i class="fa-solid fa-print mr-1.5 ml-1.5 text-sky-500"></i> {{ app()->getLocale() == 'ar' ? 'طباعة العرض' : 'Print Quote' }}
            </a>

            @if($quotation->status !== 'Converted')
                <form action="{{ route('admin.sales.quotations.convert', $quotation->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من تحويل هذا العرض إلى أمر بيع رسمي؟' : 'Convert this quotation into an official Sales Order?' }}')">
                    @csrf
                    <button type="submit" class="btn btn-primary font-bold shadow-sm bg-emerald-600 hover:bg-emerald-500 text-white border-0">
                        <i class="fa-solid fa-cart-arrow-down mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'تحويل لأمر بيع' : 'Convert to Order' }}
                    </button>
                </form>
            @else
                <a href="{{ route('admin.orders.show', $quotation->converted_order_id) }}" class="btn btn-primary font-bold shadow-sm bg-indigo-600 hover:bg-indigo-500 text-white border-0">
                    <i class="fa-solid fa-receipt mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'عرض أمر البيع المحوّل' : 'View Converted Order' }}
                </a>
            @endif

            <a href="{{ route('admin.sales.quotations.index') }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'قائمة العروض' : 'All Quotations' }}
            </a>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger shadow-sm mb-6">
            <ul class="m-0 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Highlight Banner -->
    <div class="card mb-6 p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-sky-950 text-white border-0 shadow-lg relative overflow-hidden rounded-2xl">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-sky-500/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-sky-500/20 border border-sky-400/30 text-sky-400 flex items-center justify-center flex-shrink-0 shadow-inner">
                    <i class="fa-solid fa-file-invoice text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="text-xs font-black uppercase tracking-wider text-sky-400 bg-sky-950/80 px-2.5 py-0.5 rounded-full border border-sky-500/30">
                            {{ app()->getLocale() == 'ar' ? 'عرض سعر تجاري معتمد' : 'Commercial Clinical Quotation' }}
                        </span>
                        <span class="text-xs text-slate-300 font-mono">{{ $quotation->quotation_number }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-white mt-1 mb-0.5">
                        {{ $quotation->customer_name }}
                        @if($quotation->company_name)
                            <span class="text-sm font-normal text-slate-300">({{ $quotation->company_name }})</span>
                        @endif
                    </h3>
                    <p class="text-xs text-slate-300/80 m-0">
                        {{ app()->getLocale() == 'ar' ? 'تاريخ الإصدار:' : 'Issued Date:' }} {{ $quotation->date?->format('Y-m-d') ?? $quotation->created_at->format('Y-m-d') }}
                        • {{ app()->getLocale() == 'ar' ? 'صالح حتى:' : 'Valid Until:' }} <span class="text-amber-300 font-semibold">{{ $quotation->valid_until?->format('Y-m-d') ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>

            <div class="text-right rtl:text-left">
                <span class="text-xs uppercase font-bold text-slate-400 tracking-wider block">
                    {{ app()->getLocale() == 'ar' ? 'إجمالي العرض (شامل الضريبة)' : 'Total Amount (Inc. VAT)' }}
                </span>
                <span class="text-2xl sm:text-3xl font-black text-emerald-400 font-mono">
                    {{ number_format($quotation->total, 2) }} <span class="text-sm font-sans text-emerald-200">SAR</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Line Items & Totals & Terms -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Items Table Card -->
            <div class="card overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                <div class="card-header p-5 sm:p-6 bg-slate-50/70 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2.5 m-0">
                            <i class="fa-solid fa-boxes-stacked text-sky-600 dark:text-sky-400"></i>
                            {{ app()->getLocale() == 'ar' ? 'بنود المنتجات والخدمات' : 'Products & Services Line Items' }} 
                            <span class="text-xs bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300 px-2.5 py-0.5 rounded-full font-bold">
                                {{ $quotation->items->count() }}
                            </span>
                        </h3>
                    </div>
                    <span class="badge badge-secondary text-xs font-semibold py-1 px-3">
                        <i class="fa-solid fa-shield-halved text-emerald-500 mr-1 ml-1"></i>
                        {{ app()->getLocale() == 'ar' ? 'أسعار موحدة ومعتمدة' : 'Standard Rate Schedule' }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/80 text-xs uppercase text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                                <th class="p-4 text-center w-12">#</th>
                                <th class="p-4">{{ app()->getLocale() == 'ar' ? 'البند / التركيبة' : 'Item / Formulation' }}</th>
                                <th class="p-4 text-center">{{ app()->getLocale() == 'ar' ? 'النوع' : 'Type' }}</th>
                                <th class="p-4 text-center">{{ app()->getLocale() == 'ar' ? 'الكمية' : 'Qty' }}</th>
                                <th class="p-4 text-right rtl:text-left">{{ app()->getLocale() == 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</th>
                                <th class="p-4 text-right rtl:text-left">{{ app()->getLocale() == 'ar' ? 'الخصم' : 'Discount' }}</th>
                                <th class="p-4 text-right rtl:text-left">{{ app()->getLocale() == 'ar' ? 'الإجمالي' : 'Total' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-sm">
                            @foreach($quotation->items as $idx => $item)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="p-4 text-center text-slate-400 font-mono text-xs">{{ $idx + 1 }}</td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $item->item_name }}
                                        </div>
                                        <div class="text-xs text-slate-400 font-mono mt-0.5">
                                            SKU: {{ $item->sku ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $item->item_type === 'service' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' }}">
                                            {{ ucfirst($item->item_type ?? 'product') }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="p-4 text-right rtl:text-left font-mono text-slate-700 dark:text-slate-300">
                                        {{ number_format($item->unit_price, 2) }} SAR
                                    </td>
                                    <td class="p-4 text-right rtl:text-left font-mono text-rose-500">
                                        {{ $item->discount > 0 ? '-' . number_format($item->discount, 2) . ' SAR' : '—' }}
                                    </td>
                                    <td class="p-4 text-right rtl:text-left font-mono font-bold text-slate-900 dark:text-white">
                                        {{ number_format($item->total, 2) }} SAR
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Financial Calculation Breakdown -->
                <div class="p-6 bg-slate-50/50 dark:bg-slate-900/30 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                    <div class="w-full sm:w-80 space-y-2.5 text-sm">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>{{ app()->getLocale() == 'ar' ? 'المجموع الفرعي:' : 'Subtotal:' }}</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ number_format($quotation->subtotal, 2) }} SAR</span>
                        </div>
                        @if($quotation->discount > 0)
                            <div class="flex justify-between text-rose-600 dark:text-rose-400">
                                <span>{{ app()->getLocale() == 'ar' ? 'إجمالي الخصم التجاري:' : 'Commercial Discount:' }}</span>
                                <span class="font-mono font-semibold">-{{ number_format($quotation->discount, 2) }} SAR</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>{{ app()->getLocale() == 'ar' ? 'ضريبة القيمة المضافة (15%):' : 'VAT (15%):' }}</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">{{ number_format($quotation->tax, 2) }} SAR</span>
                        </div>
                        <div class="border-t border-slate-200 dark:border-slate-800 pt-3 flex justify-between items-center">
                            <span class="font-bold text-base text-slate-900 dark:text-white">{{ app()->getLocale() == 'ar' ? 'المجموع النهائي:' : 'Net Quotation Total:' }}</span>
                            <span class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($quotation->total, 2) }} SAR</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions & Notes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-scroll text-sky-500"></i>
                        {{ app()->getLocale() == 'ar' ? 'الشروط والأحكام' : 'Terms & Conditions' }}
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line m-0">
                        {{ $quotation->terms_conditions ?? (app()->getLocale() == 'ar' ? 'يسري هذا العرض لمدة 30 يوماً من تاريخ إصداره. الأسعار تشمل ضريبة القيمة المضافة 15%. يتم تأكيد الطلب فور سداد الدفعة أو إصدار أمر شراء رسمي.' : 'This quotation is valid for 30 days from the date of issue. Prices include 15% VAT. Order confirmed upon issuance of official PO or initial payment.') }}
                    </p>
                </div>

                <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-note-sticky text-amber-500"></i>
                        {{ app()->getLocale() == 'ar' ? 'ملاحظات داخلية' : 'Internal Sales Notes' }}
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line m-0">
                        {{ $quotation->notes ?? (app()->getLocale() == 'ar' ? 'لا توجد ملاحظات إضافية مسجلة.' : 'No additional internal notes recorded.') }}
                    </p>
                </div>
            </div>

        </div>

        <!-- Right Col: Customer Dossier & Workflow Actions -->
        <div class="space-y-6">
            
            <!-- Customer Dossier Card -->
            <div class="card p-5 sm:p-6 border border-slate-200 dark:border-slate-800 rounded-2xl">
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user-tie text-sky-500"></i>
                    {{ app()->getLocale() == 'ar' ? 'بيانات العميل / المنشأة' : 'Client Profile' }}
                </h3>

                <div class="flex items-center gap-3.5 mb-5 pb-5 border-b border-slate-100 dark:border-slate-800/80">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-400 flex items-center justify-center font-bold text-lg">
                        {{ mb_substr($quotation->customer_name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base m-0">{{ $quotation->customer_name }}</h4>
                        @if($quotation->company_name)
                            <p class="text-xs text-sky-600 dark:text-sky-400 font-semibold m-0">{{ $quotation->company_name }}</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/50">
                        <span class="text-slate-400"><i class="fa-solid fa-envelope mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $quotation->customer_email ?? '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/50">
                        <span class="text-slate-400"><i class="fa-solid fa-phone mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone' }}</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-slate-200 dir-ltr">{{ $quotation->customer_phone ?? '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/50">
                        <span class="text-slate-400"><i class="fa-solid fa-building mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'الجهة التجارية' : 'Company' }}</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $quotation->company_name ?? 'Individual' }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-slate-400"><i class="fa-solid fa-user-shield mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'محرر العرض' : 'Prepared By' }}</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $quotation->creator?->name ?? 'Sales Officer' }}</span>
                    </div>
                </div>
            </div>

            <!-- Workflow Status Manager Card -->
            <div class="card p-5 sm:p-6 border border-slate-200 dark:border-slate-800 rounded-2xl">
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-indigo-500"></i>
                    {{ app()->getLocale() == 'ar' ? 'إدارة حالة العرض' : 'Workflow & Status' }}
                </h3>

                <form action="{{ route('admin.sales.quotations.update-status', $quotation->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">
                            {{ app()->getLocale() == 'ar' ? 'تحديث الحالة الحالية:' : 'Select New Status:' }}
                        </label>
                        <select name="status" class="form-select w-full rounded-xl text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-900">
                            @foreach(['Draft' => 'Draft / مسودة', 'Sent' => 'Sent / تم الإرسال', 'Approved' => 'Approved / معتمد من العميل', 'Rejected' => 'Rejected / مرفوض', 'Expired' => 'Expired / منتهي الصلاحية'] as $val => $label)
                                <option value="{{ $val }}" {{ $quotation->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-outline w-full text-xs font-bold py-2.5">
                        <i class="fa-solid fa-check mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'حفظ الحالة' : 'Update Status' }}
                    </button>
                </form>

                @if($quotation->status === 'Converted' && $quotation->convertedOrder)
                    <div class="mt-4 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60">
                        <div class="text-xs font-bold text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-circle-check text-indigo-600"></i>
                            {{ app()->getLocale() == 'ar' ? 'تم التحويل إلى أمر بيع:' : 'Converted into Sales Order:' }}
                        </div>
                        <a href="{{ route('admin.orders.show', $quotation->converted_order_id) }}" class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            #{{ $quotation->convertedOrder->order_number }} &rarr;
                        </a>
                    </div>
                @endif
            </div>

            <!-- Conversion Action Box -->
            @if($quotation->status !== 'Converted')
                <div class="card p-5 sm:p-6 bg-gradient-to-br from-emerald-500/10 to-teal-500/5 border border-emerald-500/30 rounded-2xl">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-wand-magic-sparkles text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 dark:text-white text-sm m-0">
                                {{ app()->getLocale() == 'ar' ? 'تحويل فوري إلى أمر بيع' : 'Convert to Sales Order' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 m-0">
                                {{ app()->getLocale() == 'ar' ? 'توليد فاتورة وخصم المخزون' : 'Auto-generates official order & invoice' }}
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('admin.sales.quotations.convert', $quotation->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من تحويل هذا العرض إلى أمر بيع رسمي؟' : 'Convert this quotation into an official Sales Order?' }}')">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 rounded-xl shadow-md border-0 text-xs">
                            <i class="fa-solid fa-cart-check mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'تأكيد وتحويل لأمر بيع الآن' : 'Execute One-Click Conversion' }}
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>
</x-layouts.admin>
