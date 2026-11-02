<x-layouts.admin 
    :pageTitle="'Purchase Invoice: ' . $invoice->invoice_number" 
    :pageSubtitle="__('finance.purchase_invoices_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.purchase_invoices') => route('admin.finance.purchase-invoices.index'),
        $invoice->invoice_number => route('admin.finance.purchase-invoices.show', $invoice->id)
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.finance.purchase-invoices.print', $invoice->id) }}" target="_blank" class="btn btn-secondary font-bold text-sm shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square text-sky-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'طباعة كاملة A4' : 'Full A4 Print View' }}</span>
            </a>

            <button type="button" onclick="printInvoiceSheet()" class="btn btn-secondary font-bold text-sm shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-print text-amber-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'طباعة سريعة' : 'Quick Print' }}</span>
            </button>

            @if($invoice->due_balance > 0)
                <button type="button" onclick="document.getElementById('modal-pay-supplier').showModal()" class="btn btn-primary font-bold text-sm shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-transfer text-emerald-300"></i>
                    <span>{{ __('finance.pay_supplier') }} ({{ number_format($invoice->due_balance, 2) }} AED/SAR)</span>
                </button>
            @endif

            <a href="{{ route('admin.finance.purchase-invoices.index') }}" class="btn btn-ghost font-bold text-sm text-slate-500 hover:text-slate-800 dark:hover:text-white">
                <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1 ml-1"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'العودة لقائمة الفواتير' : 'Back to Bills' }}</span>
            </a>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @php
        $tpl = \App\Services\InvoiceTemplateService::getPurchaseTemplate();
        
        $invNum = $invoice->invoice_number ?? 'PINV-000';
        $refNum = $invoice->reference_number ?? '—';
        $invDate = $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('Y-m-d') : substr((string)$invoice->invoice_date, 0, 10);
        $dueDate = $invoice->due_date ? ($invoice->due_date instanceof \Carbon\Carbon ? $invoice->due_date->format('Y-m-d') : substr((string)$invoice->due_date, 0, 10)) : \Carbon\Carbon::parse($invDate)->addDays(30)->format('Y-m-d');
        
        $supplierName = $invoice->supplier?->name ?? ($invoice->supplier?->name_ar ?? 'المورد المعتمد');
        $supplierCode = $invoice->supplier?->code ?? ('VND-' . $invoice->supplier_id);
        $supplierAddress = $invoice->supplier?->address ?? ($invoice->supplier?->country ?? 'المملكة العربية السعودية');
        $warehouseName = $invoice->purchaseOrder?->destinationLocation?->name_ar ?? ($invoice->purchaseOrder?->destinationLocation?->name_en ?? 'المستودع الرئيسي - رصيف أ');

        $subtotal = (float) $invoice->subtotal;
        $tax = (float) $invoice->tax_amount;
        $total = (float) $invoice->grand_total;
        $extraDiscPct = (float) ($invoice->discount_percent ?? 0);
        $extraDiscVal = (float) ($invoice->discount_amount ?? 0);

        $payMethod = strtolower($invoice->payment_method ?? 'bank_transfer');
        $payStatus = strtolower($invoice->payment_status ?? 'unpaid');
        $paidAmount = (float) $invoice->paid_amount;
        $dueBalance = (float) $invoice->due_balance;
        $bankRef = $invoice->payments->last()?->reference_number ?? ($invoice->reference_number ?? '—');

        $verificationUrl = url("/admin/finance/purchase-invoices/{$invoice->id}");
    @endphp

    <div class="space-y-6" x-data="{ sheetTheme: 'auto' }">
        <!-- Sheet Preview Controls & Badges -->
        <div class="flex items-center justify-between flex-wrap gap-4 card p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-white flex items-center gap-2">
                        <span>{{ $tpl['title_ar'] }} — {{ $invNum }}</span>
                        @if($payStatus === 'paid')
                            <span class="badge badge-emerald text-xs">مدفوعة بالكامل / Paid</span>
                        @elseif($payStatus === 'partially_paid' || $payStatus === 'partial')
                            <span class="badge badge-amber text-xs">دفع جزئي / Partial</span>
                        @else
                            <span class="badge badge-rose text-xs">مستحقة السداد / Unpaid</span>
                        @endif
                    </h3>
                    <p class="text-xs text-gray-500">
                        {{ app()->getLocale() === 'ar' ? 'تصميم الفاتورة المعتمد مطابق لنموذج الطباعة وجداول الإكسل الرسمية' : 'Official certified procurement layout aligned with spreadsheet and printing specifications' }}
                    </p>
                </div>
            </div>

            <!-- On-Screen Sheet Theme Toggle (Light / Dark) -->
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-slate-800 p-1 rounded-xl text-xs font-bold">
                <button type="button" @click="sheetTheme = 'light'" :class="sheetTheme === 'light' ? 'bg-white text-slate-900 shadow-sm' : 'text-gray-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-sun text-amber-500 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'ورق أبيض' : 'Paper Light' }}
                </button>
                <button type="button" @click="sheetTheme = 'dark'" :class="sheetTheme === 'dark' ? 'bg-slate-900 text-sky-400 shadow-sm' : 'text-gray-500 hover:text-white'" class="px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-moon text-sky-400 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'الوضع الليلي' : 'Dark Mode' }}
                </button>
                <button type="button" @click="sheetTheme = 'auto'" :class="sheetTheme === 'auto' ? 'bg-sky-600 text-white shadow-sm' : 'text-gray-500 hover:text-slate-800 dark:hover:text-white'" class="px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-circle-half-stroke mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تلقائي للنظام' : 'System Auto' }}
                </button>
            </div>
        </div>

        <!-- Official Purchase Invoice Screen Canvas (Matching the exact uploaded design) -->
        <div id="printable-purchase-invoice-container" class="w-full flex justify-center overflow-x-auto pb-4">
            <div 
                id="purchase-invoice-canvas"
                :class="{
                    'theme-light': sheetTheme === 'light',
                    'theme-dark': sheetTheme === 'dark',
                    'theme-auto': sheetTheme === 'auto'
                }"
                class="pi-sheet w-full max-w-[920px] transition-all duration-300"
            >
                <!-- 1. Header Grid -->
                <div class="pi-header-grid">
                    <!-- Right: Company Corporate & Contact Details -->
                    <div class="pi-company-box">
                        <div class="pi-meta-line">
                            <span class="pi-label">العنوان:</span>
                            <span class="pi-value">{{ $tpl['address_ar'] }}</span>
                        </div>
                        <div class="pi-meta-line">
                            <span class="pi-label">هاتف:</span>
                            <span class="pi-value font-mono"><bdi dir="ltr">{{ $tpl['phone'] }}</bdi></span>
                        </div>
                        <div class="pi-meta-line">
                            <span class="pi-label">البريد الإلكتروني:</span>
                            <span class="pi-value font-mono"><bdi dir="ltr">{{ $tpl['email'] }}</bdi></span>
                        </div>
                        <div class="pi-meta-line">
                            <span class="pi-label">الرقم الضريبي:</span>
                            <span class="pi-value font-mono font-bold"><bdi dir="ltr">{{ $tpl['vat_number'] }}</bdi></span>
                        </div>
                        <div class="pi-meta-line">
                            <span class="pi-label">السجل التجاري:</span>
                            <span class="pi-value font-mono font-bold"><bdi dir="ltr">{{ $tpl['cr_number'] }}</bdi></span>
                        </div>
                    </div>

                    <!-- Center: Brand Logo & Title -->
                    <div class="pi-center-box">
                        <h2 class="pi-company-title">{{ $tpl['company_name_en'] }}</h2>
                        <img src="{{ asset('assets/logo/logo-main.png') }}" 
                             alt="Blue Zone Logo" 
                             class="pi-logo-img" 
                             onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
                    </div>

                    <!-- Left: Supplier Info & QR Code -->
                    <div class="pi-supplier-qr-box">
                        <div class="pi-supplier-fields">
                            <div class="pi-meta-line">
                                <span class="pi-label">اسم المورد:</span>
                                <span class="pi-value font-bold">{{ $supplierName }}</span>
                            </div>
                            <div class="pi-meta-line">
                                <span class="pi-label">رقم المورد:</span>
                                <span class="pi-value font-mono">{{ $supplierCode }}</span>
                            </div>
                            <div class="pi-meta-line">
                                <span class="pi-label">العنوان:</span>
                                <span class="pi-value">{{ $supplierAddress }}</span>
                            </div>
                            <div class="pi-meta-line mt-1">
                                <span class="pi-label">تاريخ الفاتورة:</span>
                                <span class="pi-value font-mono">{{ $invDate }}</span>
                            </div>
                            <div class="pi-meta-line">
                                <span class="pi-label">رقم الفاتورة:</span>
                                <span class="pi-value font-mono font-bold">{{ $invNum }}</span>
                            </div>
                            <div class="pi-meta-line">
                                <span class="pi-label">المخزن:</span>
                                <span class="pi-value">{{ $warehouseName }}</span>
                            </div>
                        </div>

                        <div class="pi-qr-wrapper" title="Scan for Verification">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($verificationUrl) }}" alt="QR Code">
                        </div>
                    </div>
                </div>

                <!-- 2. Invoice Main Title Banner -->
                <div class="pi-main-title">
                    {{ $tpl['title_ar'] ?? 'فاتورة مشتريات' }}
                    <span>فاتورة مشتريات ضريبية معتمدة &bull; PURCHASE TAX INVOICE</span>
                </div>

                <!-- 3. Spreadsheet 10-Column Line Items Table -->
                <div class="overflow-x-auto">
                    <table class="pi-table">
                        <thead>
                            <tr>
                                <th style="width: 3.5%;">م<span class="pi-en-sub">N</span></th>
                                <th style="width: 11%;">كود الصنف<span class="pi-en-sub">Item Code</span></th>
                                <th style="width: 25%;">اسم الصنف<span class="pi-en-sub">Item Description</span></th>
                                <th style="width: 6%;">الكمية<span class="pi-en-sub">Quantity</span></th>
                                <th style="width: 14%;">تاريخ الصلاحية / التشغيله<span class="pi-en-sub">Expiry / Batch No.</span></th>
                                <th style="width: 8%;">سعر الوحدة<span class="pi-en-sub">Price</span></th>
                                <th style="width: 5.5%;">نسبة الخصم 1<span class="pi-en-sub">Disc. 1</span></th>
                                <th style="width: 11%;">إجمالي بدون الضريبة<span class="pi-en-sub">Total Exc. VAT</span></th>
                                <th style="width: 8%;">الضريبة<span class="pi-en-sub">VAT</span></th>
                                <th style="width: 12%;">إجمالي الصنف شامل الضريبة<span class="pi-en-sub">Total Inc. VAT</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rows = 0; @endphp
                            @forelse($invoice->items as $index => $item)
                                @php
                                    $rows++;
                                    $itCode = $item->rawMaterial?->material_code ?? ($item->product?->sku ?? ($item->code ?? 'RM-'.$item->id));
                                    $itQty = (float) $item->quantity;
                                    $itPrice = (float) $item->unit_price;
                                    $itDisc = (float) ($item->discount_percent ?? 0);
                                    $itSubtotal = (float) $item->subtotal;
                                    $itTax = (float) $item->tax_amount;
                                    $itTotal = (float) $item->total;
                                    $batchExp = $item->batch_number ?? ($item->expiry_date ? $item->expiry_date->format('Y-m-d') : 'LOT-2026 / 2028-12');
                                @endphp
                                <tr>
                                    <td class="text-center font-mono font-bold">{{ $index + 1 }}</td>
                                    <td class="text-center font-mono text-[11px]">{{ $itCode }}</td>
                                    <td class="text-right font-bold">{{ $item->item_name }}</td>
                                    <td class="text-center font-mono font-bold">{{ number_format($itQty, 2) }}</td>
                                    <td class="text-center font-mono text-[11px]">{{ $batchExp }}</td>
                                    <td class="text-center font-mono">{{ number_format($itPrice, 2) }}</td>
                                    <td class="text-center font-mono">{{ $itDisc > 0 ? $itDisc . '%' : '0%' }}</td>
                                    <td class="text-center font-mono font-bold">{{ number_format($itSubtotal, 2) }}</td>
                                    <td class="text-center font-mono">{{ number_format($itTax, 2) }}</td>
                                    <td class="text-center font-mono font-bold">{{ number_format($itTotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-gray-400">لا توجد بنود مسجلة للفاتورة</td>
                                </tr>
                            @endforelse

                            <!-- Empty spreadsheet rows for realistic grid feel -->
                            @for($i = $rows; $i < 5; $i++)
                                <tr class="pi-empty-row">
                                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <!-- 4. Bottom Section: Left (Settlement & Signatures) | Right (Totals Table) -->
                <div class="pi-bottom-grid">
                    <!-- Left Column: Settlement & Signatures -->
                    <div>
                        <!-- Settlement Card -->
                        <div class="pi-settlement-card">
                            <div class="pi-settlement-row">
                                <span class="pi-settlement-label">طريقة الدفع:</span>
                                <div class="pi-methods-flex">
                                    <span class="pi-check-item">
                                        <span class="pi-square">{{ $payMethod === 'cash' ? '✓' : '' }}</span> نقداً Cash
                                    </span>
                                    <span class="pi-check-item">
                                        <span class="pi-square">{{ $payMethod === 'bank_transfer' ? '✓' : '' }}</span> تحويل بنكي Bank Transfer
                                    </span>
                                    <span class="pi-check-item">
                                        <span class="pi-square">{{ $payMethod === 'cheque' ? '✓' : '' }}</span> شيك Cheque
                                    </span>
                                    <span class="pi-check-item">
                                        <span class="pi-square">{{ $payStatus === 'partially_paid' || $payStatus === 'partial' ? '✓' : '' }}</span> دفع جزئي Partial
                                    </span>
                                </div>
                            </div>
                            <div class="pi-settlement-row">
                                <span class="pi-settlement-label">المبلغ المدفوع:</span>
                                <span class="pi-settlement-val text-emerald-600 font-mono">{{ number_format($paidAmount, 2) }} AED/SAR</span>
                            </div>
                            <div class="pi-settlement-row">
                                <span class="pi-settlement-label">المبلغ المتبقي:</span>
                                <span class="pi-settlement-val {{ $dueBalance > 0 ? 'text-rose-600' : 'text-emerald-600' }} font-mono font-bold">{{ number_format($dueBalance, 2) }} AED/SAR</span>
                            </div>
                            <div class="pi-settlement-row">
                                <span class="pi-settlement-label">تاريخ الاستحقاق:</span>
                                <span class="pi-settlement-val font-mono">{{ $dueDate }}</span>
                            </div>
                            <div class="pi-settlement-row">
                                <span class="pi-settlement-label">مرجع التحويل البنكي:</span>
                                <span class="pi-settlement-val font-mono">{{ $bankRef }}</span>
                            </div>
                        </div>

                        <!-- Signatures Box -->
                        <div class="pi-signatures-grid">
                            <div class="pi-sig-col">
                                <span class="pi-sig-head">المستلم</span>
                                <div class="pi-sig-line"></div>
                            </div>
                            <div class="pi-sig-col">
                                <span class="pi-sig-head">التوقيع</span>
                                <div class="pi-sig-line"></div>
                            </div>
                            <div class="pi-sig-col">
                                <span class="pi-sig-head">الختم</span>
                                <div class="pi-stamp-ring">STAMP</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Financial Totals (Matching User's Spreadsheet) -->
                    <div>
                        <table class="pi-totals-table">
                            <tr>
                                <td class="pi-t-label">إجمالي الفاتورة بعد خصم الأصناف</td>
                                <td class="pi-t-val">{{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="pi-t-label">خصم إضافي (%)</td>
                                <td class="pi-t-val">{{ $extraDiscPct > 0 ? $extraDiscPct . '%' : '0.00%' }}</td>
                            </tr>
                            <tr>
                                <td class="pi-t-label">قيمة الخصم الإضافي</td>
                                <td class="pi-t-val">{{ number_format($extraDiscVal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="pi-t-label">صافي الفاتورة قبل الضريبة VAT</td>
                                <td class="pi-t-val">{{ number_format(max(0, $subtotal - $extraDiscVal), 2) }}</td>
                            </tr>
                            <tr>
                                <td class="pi-t-label">قيمة الضريبة VAT</td>
                                <td class="pi-t-val">{{ number_format($tax, 2) }}</td>
                            </tr>
                            <tr class="pi-grand-row">
                                <td class="pi-t-label font-black">إجمالي الفاتورة شامل الضريبة</td>
                                <td class="pi-t-val font-black text-sky-600 text-sm">{{ number_format($total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Footer Terms -->
                <div class="pi-footer-note">
                    {{ $tpl['footer']['terms_ar'] ?? 'تخضع المواد والمنتجات الواردة لشروط الفحص المخبري وسلسلة التبريد المعتمدة لدى بلو زون للأدوية.' }}
                </div>
            </div>
        </div>

        <!-- 5. Payment Transactions Log -->
        <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'سجل مدفوعات وسندات صرف المورد' : 'Supplier Disbursements & Payment History' }}</span>
                </span>
                <span class="badge badge-emerald text-xs font-mono">{{ $invoice->payments->count() }} Payments</span>
            </h3>

            <div class="table-responsive">
                <table class="table text-xs">
                    <thead>
                        <tr class="bg-gray-50/70 dark:bg-slate-800/50">
                            <th>TXN #</th>
                            <th>{{ __('finance.date') }}</th>
                            <th>{{ __('finance.paid_from') }}</th>
                            <th>{{ __('finance.payment_method') }}</th>
                            <th class="text-right">{{ __('finance.amount') }}</th>
                            <th class="text-center w-20">{{ __('finance.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payments as $pay)
                            <tr>
                                <td class="font-mono font-bold text-sky-600">{{ $pay->transaction_number }}</td>
                                <td class="font-mono text-gray-500">{{ $pay->transaction_date ? $pay->transaction_date->format('Y-m-d') : '—' }}</td>
                                <td class="font-bold text-slate-800 dark:text-white">{{ $pay->account?->localized_name ?? 'Cash Account' }}</td>
                                <td class="uppercase font-mono">{{ $pay->payment_method }}</td>
                                <td class="text-right font-mono font-bold text-emerald-600">{{ number_format($pay->amount, 2) }} AED/SAR</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.finance.supplier-payments.voucher', $pay->id) }}" target="_blank" class="btn btn-secondary btn-xs" title="Print Voucher">
                                        <i class="fa-solid fa-print text-amber-500"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-400 text-xs">
                                    {{ app()->getLocale() === 'ar' ? 'لم يتم تسجيل أي مدفوعات لهذه الفاتورة بعد.' : 'No disbursement transactions recorded yet for this purchase invoice.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Pay Supplier -->
    <dialog id="modal-pay-supplier" class="modal p-0 rounded-2xl shadow-2xl backdrop:bg-slate-900/40">
        <div class="card p-6 w-[480px] max-w-full space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h4 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-transfer text-emerald-600"></i>
                    {{ __('finance.pay_supplier') }}
                </h4>
                <button type="button" onclick="document.getElementById('modal-pay-supplier').close()" class="btn btn-ghost btn-xs text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.finance.purchase-invoices.pay', $invoice->id) }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="form-label font-bold text-[11px]">{{ __('finance.amount') }} (Max: {{ number_format($invoice->due_balance, 2) }}) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" max="{{ $invoice->due_balance }}" name="amount" value="{{ old('amount', $invoice->due_balance) }}" required class="form-control text-sm font-mono font-bold">
                </div>

                <div>
                    <label class="form-label font-bold text-[11px]">{{ __('finance.paid_from') }} <span class="text-rose-500">*</span></label>
                    <select name="financial_account_id" required class="form-select text-xs">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">
                                {{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} {{ $acc->currency }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label font-bold text-[11px]">{{ __('finance.payment_method') }} <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required class="form-select text-xs">
                            <option value="bank_transfer">Bank Transfer / تحويل</option>
                            <option value="cash">Cash / نقداً</option>
                            <option value="cheque">Cheque / شيك</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label font-bold text-[11px]">{{ __('finance.date') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="form-control text-xs">
                    </div>
                </div>

                <div>
                    <label class="form-label font-bold text-[11px]">{{ app()->getLocale() === 'ar' ? 'رقم الحوالة / المرجع البنكي' : 'Reference / SWIFT #' }}</label>
                    <input type="text" name="reference_number" class="form-control text-xs font-mono" placeholder="e.g. WIRE-SNB-99214">
                </div>

                <div>
                    <label class="form-label font-bold text-[11px]">{{ app()->getLocale() === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
                    <textarea name="notes" rows="2" class="form-control text-xs" placeholder="{{ app()->getLocale() === 'ar' ? 'أي تفاصيل عن السداد...' : 'Disbursement notes...' }}"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="document.getElementById('modal-pay-supplier').close()" class="btn btn-secondary btn-sm">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white">
                        <i class="fa-solid fa-check mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد السداد والخصم' : 'Confirm Disbursement' }}
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <style>
        /* Responsive High-Fidelity Purchase Invoice Styling with Dark & Light Mode */
        .pi-sheet {
            direction: rtl;
            text-align: right;
            padding: 1.25rem 1.35rem;
            border-radius: 6px;
            box-sizing: border-box;
            font-family: 'Tajawal', 'Outfit', sans-serif;
        }

        /* 1. Theme: Light Mode (Paper Clean) */
        .pi-sheet.theme-light {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1.5px solid #0f172a !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .pi-sheet.theme-light .pi-header-grid { border-bottom: 1.5px solid #0f172a; }
        .pi-sheet.theme-light .pi-table { border: 1.5px solid #0f172a; }
        .pi-sheet.theme-light .pi-table th { background: #d4dfe8; color: #0f172a; border: 1px solid #0f172a; }
        .pi-sheet.theme-light .pi-table td { border: 1px solid #0f172a; color: #0f172a; background: #ffffff; }
        .pi-sheet.theme-light .pi-empty-row td { background: #fafafa !important; border-color: #cbd5e1 !important; }
        .pi-sheet.theme-light .pi-settlement-card, 
        .pi-sheet.theme-light .pi-signatures-grid,
        .pi-sheet.theme-light .pi-totals-table,
        .pi-sheet.theme-light .pi-totals-table td { border-color: #0f172a; color: #0f172a; }
        .pi-sheet.theme-light .pi-grand-row td { background: #d4dfe8; color: #0284c7; }
        .pi-sheet.theme-light .pi-square { border-color: #0f172a; }

        /* 2. Theme: Dark Mode (Ultra-modern Dashboard Slate) */
        .pi-sheet.theme-dark {
            background: #062B49 !important;
            color: #f8fafc !important;
            border: 1.5px solid #38bdf8 !important;
            box-shadow: 0 4px 25px rgba(0,0,0,0.3);
        }
        .pi-sheet.theme-dark .pi-header-grid { border-bottom: 1.5px solid #334155; }
        .pi-sheet.theme-dark .pi-label { color: #94a3b8; }
        .pi-sheet.theme-dark .pi-value { color: #f1f5f9; border-bottom: 1px dotted #475569; }
        .pi-sheet.theme-dark .pi-company-title { color: #38bdf8; }
        .pi-sheet.theme-dark .pi-main-title { color: #ffffff; }
        .pi-sheet.theme-dark .pi-main-title span { color: #94a3b8; }
        .pi-sheet.theme-dark .pi-table { border: 1.5px solid #334155; }
        .pi-sheet.theme-dark .pi-table th { background: #0f2744; color: #38bdf8; border: 1px solid #334155; }
        .pi-sheet.theme-dark .pi-en-sub { color: #94a3b8; }
        .pi-sheet.theme-dark .pi-table td { border: 1px solid #334155; color: #f8fafc; background: #062B49; }
        .pi-sheet.theme-dark .pi-empty-row td { background: #092138 !important; border-color: #1e293b !important; }
        .pi-sheet.theme-dark .pi-settlement-card { background: #0a2742; border-color: #334155; color: #f8fafc; }
        .pi-sheet.theme-dark .pi-settlement-row { border-bottom-color: #334155; }
        .pi-sheet.theme-dark .pi-settlement-label { color: #94a3b8; }
        .pi-sheet.theme-dark .pi-check-item { color: #e2e8f0; }
        .pi-sheet.theme-dark .pi-square { border-color: #38bdf8; }
        .pi-sheet.theme-dark .pi-signatures-grid { border-color: #334155; background: #0a2742; }
        .pi-sheet.theme-dark .pi-sig-head { color: #94a3b8; }
        .pi-sheet.theme-dark .pi-totals-table { border-color: #334155; }
        .pi-sheet.theme-dark .pi-totals-table td { border-color: #334155; color: #f8fafc; }
        .pi-sheet.theme-dark .pi-t-label { background: #0f2744; color: #cbd5e1; }
        .pi-sheet.theme-dark .pi-grand-row td { background: #0c3358 !important; color: #38bdf8 !important; }

        /* 3. Theme: Auto (Inherits CSS variables from Admin Layout) */
        .pi-sheet.theme-auto {
            background: var(--bg-card, #ffffff);
            color: var(--text-main, #0f172a);
            border: 1.5px solid var(--border-color, #1e293b);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .pi-sheet.theme-auto .pi-header-grid { border-bottom: 1.5px solid var(--border-color, #1e293b); }
        .pi-sheet.theme-auto .pi-table { border: 1.5px solid var(--border-color, #1e293b); }
        .pi-sheet.theme-auto .pi-table th { background: rgba(56, 189, 248, 0.12); color: var(--text-main, #0f172a); border: 1px solid var(--border-color, #1e293b); }
        .pi-sheet.theme-auto .pi-table td { border: 1px solid var(--border-color, #1e293b); }
        .pi-sheet.theme-auto .pi-settlement-card,
        .pi-sheet.theme-auto .pi-signatures-grid,
        .pi-sheet.theme-auto .pi-totals-table,
        .pi-sheet.theme-auto .pi-totals-table td { border-color: var(--border-color, #1e293b); }
        .pi-sheet.theme-auto .pi-grand-row td { background: rgba(56, 189, 248, 0.15); }
        .pi-sheet.theme-auto .pi-square { border-color: currentColor; }

        /* Layout Elements */
        .pi-header-grid {
            display: grid;
            grid-template-columns: 36% 26% 38%;
            gap: 0.5rem;
            align-items: stretch;
            padding-bottom: 0.4rem;
            margin-bottom: 0.4rem;
        }
        .pi-company-box { font-size: 0.72rem; line-height: 1.4; }
        .pi-meta-line { display: flex; align-items: baseline; gap: 0.25rem; margin-bottom: 0.12rem; }
        .pi-label { font-weight: 800; white-space: nowrap; min-width: 65px; font-size: 0.71rem; }
        .pi-value { font-weight: 700; word-break: break-word; flex: 1; border-bottom: 1px dotted #94a3b8; padding-bottom: 1px; font-size: 0.71rem; }
        .pi-center-box { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .pi-company-title { font-size: 0.88rem; font-weight: 900; margin-bottom: 0.2rem; letter-spacing: -0.01em; line-height: 1.2; }
        .pi-logo-img { max-height: 44px; max-width: 125px; object-fit: contain; margin: 0 auto; }
        .pi-supplier-qr-box { display: flex; gap: 0.45rem; align-items: flex-start; justify-content: space-between; font-size: 0.72rem; line-height: 1.35; }
        .pi-supplier-fields { flex: 1 1 0%; min-width: 0; }
        .pi-qr-wrapper { flex-shrink: 0; width: 68px; height: 68px; border: 1px solid #cbd5e1; padding: 2px; background: #ffffff; border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .pi-qr-wrapper img { width: 100%; height: 100%; object-fit: contain; }
        .pi-main-title { text-align: center; font-size: 1.45rem; font-weight: 900; margin: 0.35rem 0 0.45rem 0; letter-spacing: -0.01em; line-height: 1.15; }
        .pi-main-title span { display: block; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.05em; margin-top: 0.1rem; }
        
        .pi-table { width: 100%; border-collapse: collapse; font-size: 0.71rem; margin-bottom: 0.4rem; }
        .pi-table th { padding: 5px 3px; font-weight: 800; text-align: center; line-height: 1.25; vertical-align: middle; }
        .pi-en-sub { display: block; font-size: 0.62rem; font-weight: 600; }
        .pi-table td { padding: 4px 4px; vertical-align: middle; height: 24px; }
        
        .pi-bottom-grid { display: grid; grid-template-columns: 55% 45%; gap: 0.75rem; margin-top: 0.35rem; align-items: start; }
        .pi-settlement-card { border: 1.5px solid; padding: 0.4rem 0.55rem; border-radius: 2px; font-size: 0.7rem; margin-bottom: 0.5rem; }
        .pi-settlement-row { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 0.2rem; padding-bottom: 0.15rem; border-bottom: 1px dotted #cbd5e1; }
        .pi-settlement-row:last-child { border-bottom: none; margin-bottom: 0; }
        .pi-settlement-label { font-weight: 800; min-width: 90px; }
        .pi-methods-flex { display: flex; gap: 0.45rem; flex-wrap: wrap; font-size: 0.67rem; font-weight: 800; }
        .pi-check-item { display: inline-flex; align-items: center; gap: 0.25rem; }
        .pi-square { display: inline-block; width: 11px; height: 11px; border: 1.5px solid; border-radius: 2px; line-height: 10px; text-align: center; font-size: 9px; font-weight: bold; }
        
        .pi-signatures-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; border: 1.5px solid; padding: 0.4rem; text-align: center; font-size: 0.68rem; }
        .pi-sig-col { display: flex; flex-direction: column; gap: 0.5rem; }
        .pi-sig-head { font-weight: 800; }
        .pi-sig-line { border-bottom: 1.5px dashed #94a3b8; margin-top: 0.8rem; width: 80%; margin-left: auto; margin-right: auto; }
        .pi-stamp-ring { width: 44px; height: 44px; border: 1.5px dashed #94a3b8; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 0.55rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; }

        .pi-totals-table { width: 100%; border-collapse: collapse; font-size: 0.72rem; border: 1.5px solid; }
        .pi-totals-table td { border: 1px solid; padding: 4px 6px; }
        .pi-t-label { font-weight: 800; }
        .pi-t-val { text-align: left; font-family: monospace; font-weight: 800; }

        .pi-footer-note { margin-top: 0.5rem; text-align: center; font-size: 0.64rem; border-top: 1px dotted #cbd5e1; padding-top: 0.3rem; opacity: 0.8; }

        @media (max-width: 768px) {
            .pi-header-grid { grid-template-columns: 1fr; gap: 0.75rem; }
            .pi-bottom-grid { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        function printInvoiceSheet() {
            const printUrl = "{{ route('admin.finance.purchase-invoices.print', $invoice->id) }}";
            const printWin = window.open(printUrl, '_blank', 'width=950,height=800,scrollbars=yes');
            if (printWin) {
                printWin.focus();
            } else {
                window.print();
            }
        }
    </script>
</x-layouts.admin>
