<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ app()->getLocale() === 'ar' ? 'سند صرف رسمي' : 'Payment Voucher' }} — {{ $entry->payment_number }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .voucher-container { border: 2px solid #000 !important; box-shadow: none !important; margin: 0 auto !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 text-slate-800" style="font-family: 'Outfit', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <!-- Top Action Toolbar (Hidden in Print) -->
    <div class="max-w-4xl mx-auto mb-5 flex items-center justify-between no-print px-4">
        <a href="{{ route('admin.finance.payments.index') }}" class="inline-flex items-center gap-2 text-xs font-bold px-3 py-2 rounded-xl bg-white border border-slate-300 shadow-sm text-slate-700 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'العودة لدفتر سندات الصرف' : 'Back to Payment Ledger' }}</span>
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-black px-4 py-2 rounded-xl bg-[#0A4F78] hover:bg-[#063450] text-white shadow-md transition cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'طباعة سند الصرف' : 'Print Payment Voucher' }}</span>
            </button>
        </div>
    </div>

    <!-- Official Corporate Payment Voucher Document -->
    <div class="voucher-container max-w-4xl mx-auto bg-white border-2 border-slate-300 rounded-2xl shadow-xl overflow-hidden p-8 sm:p-12 relative">
        
        <!-- Top Branded Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b-2 border-slate-800 gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="Blue Zone" class="h-14 w-auto object-contain"
                     onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                <div>
                    <h1 class="text-xl font-black text-slate-950 uppercase tracking-tight m-0">BLUE ZONE PHARMACEUTICALS</h1>
                    <p class="text-xs font-bold text-slate-600 m-0">شركة المنطقة الزرقاء للأدوية والمستلزمات الطبية</p>
                    <p class="text-[11px] font-mono text-slate-500 m-0">VAT Reg: 310000000000003 &bull; CR: 1010892019</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="inline-block px-3 py-1 bg-rose-100 text-rose-900 font-black text-xs uppercase tracking-wider rounded-md border border-rose-300 mb-1">
                    {{ app()->getLocale() === 'ar' ? 'سند صرف رسمي نقد / شيك' : 'Official Payment Voucher' }}
                </span>
                <div class="text-sm font-mono font-black text-slate-950 tracking-wider">
                    NO: <span class="text-rose-700">{{ $entry->payment_number }}</span>
                </div>
                <div class="text-xs font-bold text-slate-600">
                    {{ app()->getLocale() === 'ar' ? 'التاريخ:' : 'Date:' }}
                    <span class="font-mono text-slate-900">{{ $entry->entry_date ? $entry->entry_date->format('Y-m-d') : date('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <!-- Voucher Body Details Table -->
        <div class="mt-6 space-y-4 text-xs sm:text-sm">
            
            <!-- Payee Row -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 border-b border-slate-200 pb-3 items-center">
                <span class="font-black text-slate-600 uppercase">{{ app()->getLocale() === 'ar' ? 'اصرفوا إلى السيد / السادة:' : 'Paid To / Payee:' }}</span>
                <div class="sm:col-span-3 font-black text-slate-950 text-base sm:text-lg flex items-center justify-between flex-wrap gap-2">
                    <span>{{ $entry->payee_name }}</span>
                    @if($entry->supplier_code)
                        <span class="text-xs font-mono font-bold bg-slate-100 px-2 py-0.5 rounded border border-slate-300 text-slate-700">
                            CODE: {{ $entry->supplier_code }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Amount Numbers & Tafqeet Row -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 border-b border-slate-200 pb-3 items-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                <span class="font-black text-slate-600 uppercase">{{ app()->getLocale() === 'ar' ? 'المبلغ الإجمالي:' : 'Gross Amount:' }}</span>
                <div class="sm:col-span-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-xl sm:text-2xl font-black font-mono text-rose-700">
                        {{ number_format($entry->gross_amount, 2) }} <span class="text-xs font-sans text-slate-600">SAR / ريال سعودي</span>
                    </span>
                    <span class="text-xs font-bold text-slate-600 italic">
                        {{ app()->getLocale() === 'ar' ? 'فقط وقدره ' . number_format($entry->gross_amount, 2) . ' ريال سعودي لا غير' : 'Total of ' . number_format($entry->gross_amount, 2) . ' Saudi Riyals only' }}
                    </span>
                </div>
            </div>

            <!-- Category & Purpose -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 border-b border-slate-200 pb-3">
                <span class="font-black text-slate-600 uppercase">{{ app()->getLocale() === 'ar' ? 'التصنيف والبيان:' : 'Category & Purpose:' }}</span>
                <div class="sm:col-span-3 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold px-2.5 py-0.5 rounded text-xs" style="background-color: {{ $entry->category_meta['bg_color'] }}; color: {{ $entry->category_meta['badge_color'] }}; border: 1px solid {{ $entry->category_meta['badge_color'] }}40;">
                            {{ app()->getLocale() === 'ar' ? $entry->category_meta['name_ar'] : $entry->category_meta['name_en'] }}
                        </span>
                        @if($entry->purchaseInvoice)
                            <span class="text-xs font-mono font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                Bill Ref: #{{ $entry->purchaseInvoice->invoice_number }}
                            </span>
                        @endif
                    </div>
                    <p class="font-medium text-slate-800 m-0 text-sm">
                        {{ $entry->description ?: ($entry->category_meta['name_ar'] ?? 'صرف مالي رسمي') }}
                    </p>
                </div>
            </div>

            <!-- Tax Breakdown Grid -->
            <div class="grid grid-cols-3 gap-3 p-3.5 bg-slate-100 rounded-xl border border-slate-200 text-center font-mono">
                <div>
                    <span class="block text-[11px] font-sans font-bold text-slate-500 uppercase">{{ app()->getLocale() === 'ar' ? 'بدون ضريبة' : 'Amount Excl. VAT' }}</span>
                    <span class="text-sm sm:text-base font-black text-slate-900">{{ number_format($entry->amount_excl_vat, 2) }} SAR</span>
                </div>
                <div class="border-x border-slate-300">
                    <span class="block text-[11px] font-sans font-bold text-slate-500 uppercase">{{ app()->getLocale() === 'ar' ? 'ضريبة القيمة المضافة (' . $entry->vat_rate . '%)' : 'VAT (' . $entry->vat_rate . '%)' }}</span>
                    <span class="text-sm sm:text-base font-black text-amber-700">{{ number_format($entry->vat_amount, 2) }} SAR</span>
                </div>
                <div>
                    <span class="block text-[11px] font-sans font-bold text-slate-500 uppercase">{{ app()->getLocale() === 'ar' ? 'الإجمالي النهائي' : 'Gross Total' }}</span>
                    <span class="text-sm sm:text-base font-black text-rose-700">{{ number_format($entry->gross_amount, 2) }} SAR</span>
                </div>
            </div>

            <!-- Payment Method & Bank/Cheque Details -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 border-b border-slate-200 pb-3 items-center">
                <span class="font-black text-slate-600 uppercase">{{ app()->getLocale() === 'ar' ? 'طريقة الصرف والحساب:' : 'Disbursement Method:' }}</span>
                <div class="sm:col-span-3 flex items-center justify-between flex-wrap gap-2 text-xs font-bold text-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded bg-slate-200 font-extrabold capitalize text-slate-900">
                            {{ str_replace('_', ' ', $entry->payment_method) }}
                        </span>
                        @if($entry->financialAccount)
                            <span class="text-slate-700">
                                {{ $entry->financialAccount->name_en }} / {{ $entry->financialAccount->name_ar }}
                                @if($entry->financialAccount->iban)
                                    <span class="font-mono text-slate-500">({{ $entry->financialAccount->iban }})</span>
                                @endif
                            </span>
                        @endif
                    </div>

                    @if($entry->payment_method === 'cheque' && $entry->cheque_number)
                        <div class="font-mono bg-purple-50 text-purple-900 px-2.5 py-1 rounded border border-purple-200 text-xs font-bold">
                            CHQ #: {{ $entry->cheque_number }} &bull; Bank: {{ $entry->cheque_bank ?: 'Bank' }} &bull; Due: {{ $entry->cheque_due_date ? $entry->cheque_due_date->format('Y-m-d') : '-' }}
                        </div>
                    @endif
                </div>
            </div>

            @if($entry->notes)
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-600">
                    <span class="font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'ملاحظات إضافية:' : 'Additional Notes:' }}</span>
                    {{ $entry->notes }}
                </div>
            @endif

        </div>

        <!-- Official Signatures Box (Saudi Standard Corporate) -->
        <div class="mt-12 pt-6 border-t-2 border-slate-800 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-xs">
            <div class="space-y-10">
                <span class="block font-black text-slate-700 uppercase">{{ app()->getLocale() === 'ar' ? 'المحاسب / أمين الصندوق' : 'Accountant / Cashier' }}</span>
                <div class="border-b border-dashed border-slate-400 mx-4"></div>
                <span class="block text-[11px] font-bold text-slate-500">{{ $entry->creator?->name ?: 'System Accountant' }}</span>
            </div>

            <div class="space-y-10">
                <span class="block font-black text-slate-700 uppercase">{{ app()->getLocale() === 'ar' ? 'المراجع الداخلي' : 'Internal Auditor' }}</span>
                <div class="border-b border-dashed border-slate-400 mx-4"></div>
                <span class="block text-[11px] font-bold text-slate-500">{{ app()->getLocale() === 'ar' ? 'التوقيع والختم' : 'Sign & Stamp' }}</span>
            </div>

            <div class="space-y-10">
                <span class="block font-black text-slate-700 uppercase">{{ app()->getLocale() === 'ar' ? 'المدير المالي / المفوض' : 'Finance Director' }}</span>
                <div class="border-b border-dashed border-slate-400 mx-4"></div>
                <span class="block text-[11px] font-bold text-slate-500">{{ app()->getLocale() === 'ar' ? 'الاعتماد والموافقة' : 'Approved' }}</span>
            </div>

            <div class="space-y-10">
                <span class="block font-black text-slate-700 uppercase">{{ app()->getLocale() === 'ar' ? 'توقيع المستلم' : 'Received By' }}</span>
                <div class="border-b border-dashed border-slate-400 mx-4"></div>
                <span class="block text-[11px] font-bold text-slate-500">{{ app()->getLocale() === 'ar' ? 'الاسم والصفة' : 'Name & ID' }}</span>
            </div>
        </div>

        <!-- Footer Security Hash & Watermark -->
        <div class="mt-8 pt-4 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-400 font-mono">
            <span>AUDIT ID: BZ-PV-{{ $entry->id }}-{{ strtoupper(substr(md5($entry->payment_number . $entry->created_at), 0, 8)) }}</span>
            <span>Blue Zone Pharmaceuticals ERP &bull; Official Accounting Document</span>
            <span>Printed: {{ now()->format('Y-m-d H:i') }}</span>
        </div>

    </div>

</body>
</html>
