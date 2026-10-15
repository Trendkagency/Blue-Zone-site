<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إنشاء عرض سعر جديد' : 'Create New Quotation'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إعداد عرض تسعير مخصص للعملاء أو العيادات مع احتساب ضريبة القيمة المضافة والشروط التعاقدية.' : 'Prepare customized longevity formulation & protocol pricing with real-time VAT and terms.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المبيعات' : 'Sales') => route('admin.sales.orders.index'),
        (app()->getLocale() === 'ar' ? 'عروض الأسعار' : 'Quotations') => route('admin.sales.quotations.index'),
        (app()->getLocale() === 'ar' ? 'إنشاء عرض جديد' : 'New Quote') => route('admin.sales.quotations.create')
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.sales.quotations.index') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'الرجوع للقائمة' : 'Back to Quotations' }}
        </a>
    </x-slot>

    <div class="space-y-6">

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm space-y-1">
                @foreach($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.sales.quotations.store') }}" method="POST" id="quotationForm" class="space-y-6">
            @csrf

            <!-- 1. Header Information & Client Details Card -->
            <div class="card p-6 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-file-invoice text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'معلومات العرض والعميل' : 'Quotation & Customer Details' }}
                            </h3>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                {{ app()->getLocale() === 'ar' ? 'رقم العرض التلقائي:' : 'Auto Reference:' }} <strong>{{ $nextQuoteNumber }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="badge badge-neutral text-xs font-bold font-mono">
                            {{ app()->getLocale() === 'ar' ? 'الحالة: مسودة مبدئية' : 'Status: Draft' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Customer Picker -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'اختيار عميل مسجل' : 'Existing Customer' }}
                        </label>
                        <select name="customer_id" id="clientSelect" class="form-select text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="autofillCustomerData(this)">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- عميل جديد / إدخال يدوي --' : '-- Guest / Manual Entry --' }}</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-email="{{ $c->email }}" data-phone="{{ $c->phone }}">
                                    {{ $c->name }} ({{ $c->phone ?: $c->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'اسم العميل / المستلم' : 'Client Full Name' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="customer_name" id="clientName" value="{{ old('customer_name') }}" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" required>
                    </div>

                    <!-- Client Phone -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'رقم الجوال' : 'Phone Number' }}
                        </label>
                        <input type="text" name="customer_phone" id="clientPhone" value="{{ old('customer_phone') }}" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl font-mono">
                    </div>

                    <!-- Client Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                        </label>
                        <input type="email" name="customer_email" id="clientEmail" value="{{ old('customer_email') }}" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الشركة / المركز الطبي' : 'Company / Clinic Account' }}
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: عيادات أدفانسد كير' : 'e.g. Advanced Longevity Clinic' }}" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                    </div>

                    <!-- Issue Date -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'تاريخ الإصدار' : 'Issue Date' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl font-mono" required>
                    </div>

                    <!-- Valid Until -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'صالح حتى تاريخ' : 'Valid Until' }}
                        </label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', now()->addDays(30)->toDateString()) }}" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl font-mono">
                    </div>
                </div>
            </div>

            <!-- 2. Dynamic Line Items Builder Card -->
            <div class="card p-6 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-indigo-500"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'بنود المنتجات والخدمات المسعرة' : 'Quotation Line Items' }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'اختر التركيبات الصيدلانية أو الخدمات السريرية مع الكميات والخصومات.' : 'Select products or services to automatically calculate line pricing.' }}
                        </p>
                    </div>

                    <button type="button" onclick="addNewLineItem()" class="btn btn-secondary btn-sm text-xs font-bold text-primary">
                        <i class="fa-solid fa-plus mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'إضافة بند آخر' : 'Add Item' }}
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-start" id="quoteItemsTable">
                        <thead class="bg-slate-50 dark:bg-[#041E34] border-b border-slate-200/80 dark:border-[#0E4A7A] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-3 text-start min-w-[240px]">{{ app()->getLocale() === 'ar' ? 'المنتج أو الخدمة' : 'Product / Service' }}</th>
                                <th class="py-3 px-3 text-start w-[120px]">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                                <th class="py-3 px-3 text-center w-[100px]">{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Qty' }}</th>
                                <th class="py-3 px-3 text-end w-[130px]">{{ app()->getLocale() === 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</th>
                                <th class="py-3 px-3 text-end w-[120px]">{{ app()->getLocale() === 'ar' ? 'الخصم (ر.س)' : 'Discount' }}</th>
                                <th class="py-3 px-3 text-end w-[140px]">{{ app()->getLocale() === 'ar' ? 'المجموع' : 'Line Total' }}</th>
                                <th class="py-3 px-3 text-center w-[50px]"></th>
                            </tr>
                        </thead>
                        <tbody id="lineItemsContainer" class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                            <!-- Template Row 0 -->
                            <tr class="line-item-row" data-index="0">
                                <td class="py-2.5 px-3">
                                    <select name="items[0][product_id]" class="form-select text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg product-dropdown" onchange="onProductSelect(this)">
                                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر منتجاً أو خدمة --' : '-- Choose Product/Service --' }}</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" data-name="{{ $prod->name_en }}" data-price="{{ $prod->price }}" data-type="{{ $prod->type ?? 'product' }}" data-sku="{{ $prod->sku }}">
                                                {{ app()->getLocale() === 'ar' ? ($prod->name_ar ?? $prod->name_en) : $prod->name_en }} ({{ $prod->sku }}) - @currency($prod->price)
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="items[0][item_name]" class="form-control text-xs w-full mt-1.5 bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg item-name-input" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم البند في العرض...' : 'Item description in quotation...' }}" required>
                                </td>
                                <td class="py-2.5 px-3">
                                    <select name="items[0][item_type]" class="form-select text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg item-type-select">
                                        <option value="product">{{ app()->getLocale() === 'ar' ? 'منتج' : 'Product' }}</option>
                                        <option value="service">{{ app()->getLocale() === 'ar' ? 'خدمة' : 'Service' }}</option>
                                    </select>
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <input type="number" name="items[0][quantity]" min="1" value="1" class="form-control text-xs text-center w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg font-mono font-bold item-qty-input" oninput="recalcRow(this)" required>
                                </td>
                                <td class="py-2.5 px-3 text-end">
                                    <input type="number" step="0.01" name="items[0][unit_price]" min="0" value="0.00" class="form-control text-xs text-end w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg font-mono font-bold item-price-input" oninput="recalcRow(this)" required>
                                </td>
                                <td class="py-2.5 px-3 text-end">
                                    <input type="number" step="0.01" name="items[0][discount]" min="0" value="0.00" class="form-control text-xs text-end w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg font-mono item-discount-input" oninput="recalcRow(this)">
                                </td>
                                <td class="py-2.5 px-3 text-end font-mono font-black text-sm text-slate-900 dark:text-white line-total-display">
                                    0.00 SAR
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" onclick="removeLineItem(this)" class="text-rose-500 hover:text-rose-700 p-1 text-sm" title="Remove">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Grand Summary Box -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <button type="button" onclick="addNewLineItem()" class="btn btn-secondary btn-sm text-xs font-bold text-primary">
                        <i class="fa-solid fa-plus mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'إضافة بند جديد' : 'Add Another Item' }}
                    </button>

                    <div class="w-full sm:w-72 space-y-2 bg-slate-50 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-200 dark:border-slate-800 text-xs">
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>{{ app()->getLocale() === 'ar' ? 'المجموع الفرعي:' : 'Subtotal:' }}</span>
                            <span class="font-bold font-mono text-slate-900 dark:text-white" id="summarySubtotal">0.00 SAR</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>{{ app()->getLocale() === 'ar' ? 'إجمالي الخصم:' : 'Discount Total:' }}</span>
                            <span class="font-bold font-mono text-rose-600 dark:text-rose-400" id="summaryDiscount">-0.00 SAR</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>{{ app()->getLocale() === 'ar' ? 'ضريبة القيمة المضافة (15%):' : 'VAT (15%):' }}</span>
                            <span class="font-bold font-mono text-slate-900 dark:text-white" id="summaryTax">0.00 SAR</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-black border-t border-slate-200 dark:border-slate-700 pt-2 text-slate-900 dark:text-white">
                            <span>{{ app()->getLocale() === 'ar' ? 'الإجمالي النهائي:' : 'Grand Total:' }}</span>
                            <span class="text-base text-primary font-mono" id="summaryGrandTotal">0.00 SAR</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Terms & Clinical Conditions Card -->
            <div class="card p-6 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-signature text-amber-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'الشروط التعاقدية والملاحظات' : 'Terms, Conditions & Notes' }}</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'شروط وأحكام العرض (تظهر في العرض المطبوع)' : 'Terms & Conditions (Visible to Client)' }}
                        </label>
                        <textarea name="terms_conditions" rows="3" class="form-control text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">{{ old('terms_conditions', '1. الأسعار سارية لمدة 30 يوماً من تاريخ الإصدار.\n2. تشمل الأسعار ضريبة القيمة المضافة 15%.\n3. التسليم مجاني للمراكز والعيادات في مدينة الرياض.') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'ملاحظات داخلية لفريق المبيعات' : 'Internal Sales Notes' }}
                        </label>
                        <textarea name="notes" rows="3" placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظات خاصة بمتابعة الصفقة أو مواعيد الزيارات...' : 'Internal negotiation context...' }}" class="form-control text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.sales.quotations.index') }}" class="btn btn-neutral text-xs sm:text-sm font-semibold">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-xs sm:text-sm font-bold shadow-md">
                    <i class="fa-solid fa-file-circle-check mr-1 ml-1"></i>
                    {{ app()->getLocale() === 'ar' ? 'حفظ وإصدار عرض السعر' : 'Save & Issue Quotation' }}
                </button>
            </div>
        </form>

    </div>

    <!-- Interactive Line Items Calculation Script -->
    <script>
        let rowIndex = 1;

        function autofillCustomerData(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) return;
            document.getElementById('clientName').value = opt.getAttribute('data-name') || '';
            document.getElementById('clientEmail').value = opt.getAttribute('data-email') || '';
            document.getElementById('clientPhone').value = opt.getAttribute('data-phone') || '';
        }

        function onProductSelect(select) {
            const opt = select.options[select.selectedIndex];
            const row = select.closest('.line-item-row');
            if (!opt || !opt.value) return;

            const nameInput = row.querySelector('.item-name-input');
            const priceInput = row.querySelector('.item-price-input');
            const typeSelect = row.querySelector('.item-type-select');

            nameInput.value = opt.getAttribute('data-name') || '';
            priceInput.value = parseFloat(opt.getAttribute('data-price') || 0).toFixed(2);
            typeSelect.value = opt.getAttribute('data-type') || 'product';

            recalcRow(priceInput);
        }

        function recalcRow(el) {
            const row = el.closest('.line-item-row');
            const qty = parseFloat(row.querySelector('.item-qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.item-price-input').value) || 0;
            const discount = parseFloat(row.querySelector('.item-discount-input').value) || 0;

            const lineTotal = Math.max(0, (qty * price) - discount);
            row.querySelector('.line-total-display').innerText = lineTotal.toFixed(2) + ' SAR';

            recalcGrandTotals();
        }

        function recalcGrandTotals() {
            let subtotal = 0;
            let discountTotal = 0;

            document.querySelectorAll('.line-item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.item-price-input').value) || 0;
                const discount = parseFloat(row.querySelector('.item-discount-input').value) || 0;

                subtotal += (qty * price);
                discountTotal += discount;
            });

            const taxable = Math.max(0, subtotal - discountTotal);
            const tax = taxable * 0.15;
            const grandTotal = taxable + tax;

            document.getElementById('summarySubtotal').innerText = subtotal.toFixed(2) + ' SAR';
            document.getElementById('summaryDiscount').innerText = '-' + discountTotal.toFixed(2) + ' SAR';
            document.getElementById('summaryTax').innerText = tax.toFixed(2) + ' SAR';
            document.getElementById('summaryGrandTotal').innerText = grandTotal.toFixed(2) + ' SAR';
        }

        function addNewLineItem() {
            const container = document.getElementById('lineItemsContainer');
            const newRow = document.createElement('tr');
            newRow.className = 'line-item-row';
            newRow.setAttribute('data-index', rowIndex);

            newRow.innerHTML = `
                <td class="py-2.5 px-3">
                    <select name="items[${rowIndex}][product_id]" class="form-select text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg product-dropdown" onchange="onProductSelect(this)">
                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر منتجاً أو خدمة --' : '-- Choose Product/Service --' }}</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" data-name="{{ $prod->name_en }}" data-price="{{ $prod->price }}" data-type="{{ $prod->type ?? 'product' }}" data-sku="{{ $prod->sku }}">
                                {{ app()->getLocale() === 'ar' ? ($prod->name_ar ?? $prod->name_en) : $prod->name_en }} ({{ $prod->sku }}) - @currency($prod->price)
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="items[${rowIndex}][item_name]" class="form-control text-xs w-full mt-1.5 bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg item-name-input" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم البند في العرض...' : 'Item description in quotation...' }}" required>
                </td>
                <td class="py-2.5 px-3">
                    <select name="items[${rowIndex}][item_type]" class="form-select text-xs w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg item-type-select">
                        <option value="product">{{ app()->getLocale() === 'ar' ? 'منتج' : 'Product' }}</option>
                        <option value="service">{{ app()->getLocale() === 'ar' ? 'خدمة' : 'Service' }}</option>
                    </select>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <input type="number" name="items[${rowIndex}][quantity]" min="1" value="1" class="form-control text-xs text-center w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg font-mono font-bold item-qty-input" oninput="recalcRow(this)" required>
                </td>
                <td class="py-2.5 px-3 text-end">
                    <input type="number" step="0.01" name="items[${rowIndex}][unit_price]" min="0" value="0.00" class="form-control text-xs text-end w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg font-mono font-bold item-price-input" oninput="recalcRow(this)" required>
                </td>
                <td class="py-2.5 px-3 text-end">
                    <input type="number" step="0.01" name="items[${rowIndex}][discount]" min="0" value="0.00" class="form-control text-xs text-end w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-lg font-mono item-discount-input" oninput="recalcRow(this)">
                </td>
                <td class="py-2.5 px-3 text-end font-mono font-black text-sm text-slate-900 dark:text-white line-total-display">
                    0.00 SAR
                </td>
                <td class="py-2.5 px-3 text-center">
                    <button type="button" onclick="removeLineItem(this)" class="text-rose-500 hover:text-rose-700 p-1 text-sm" title="Remove">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            container.appendChild(newRow);
            rowIndex++;
        }

        function removeLineItem(btn) {
            const rows = document.querySelectorAll('.line-item-row');
            if (rows.length <= 1) {
                alert('{{ app()->getLocale() === 'ar' ? 'يجب الإبقاء على بند واحد على الأقل في عرض السعر.' : 'At least one line item is required.' }}');
                return;
            }
            btn.closest('.line-item-row').remove();
            recalcGrandTotals();
        }
    </script>
</x-layouts.admin>
