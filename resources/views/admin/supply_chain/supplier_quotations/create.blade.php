<x-layouts.admin 
    :pageTitle="__('supplier_quotations.create_quote')" 
    :pageSubtitle="__('supplier_quotations.subtitle')"
    :breadcrumbs="[__('supplier_quotations.title') => route('admin.supplier-quotations.index'), __('supplier_quotations.create_quote') => '#']"
>
    <div class="max-w-6xl mx-auto pb-12">
        <form method="POST" action="{{ route('admin.supplier-quotations.store') }}" id="quotationForm" class="space-y-6">
            @csrf

            <!-- Header Panel: Vendor & Commercial Terms -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-file-contract text-sky-600"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'البيانات التجارية والشريك المورد' : 'Vendor & Commercial Parameters' }}</span>
                    </h3>
                    <span class="text-xs font-mono font-bold text-sky-600 dark:text-sky-400">
                        RFQ Specification
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Quotation Number -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.quote_number') }} <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="quote_number" 
                               value="{{ old('quote_number', $defaultQuoteNumber) }}" 
                               required 
                               class="form-control text-sm font-mono tracking-wider @error('quote_number') border-rose-500 @enderror">
                        @error('quote_number') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Vendor / Supplier Selection -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.supplier') }} <span class="text-rose-500">*</span></label>
                        <select name="supplier_id" id="supplierSelect" required class="form-select text-sm @error('supplier_id') border-rose-500 @enderror" onchange="onSupplierChanged(this)">
                            <option value="">-- {{ __('supplier_quotations.select_supplier') }} --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" 
                                        data-currency="{{ $sup->currency ?? 'SAR' }}"
                                        data-terms="{{ $sup->payment_terms ?? 'Net 30' }}"
                                        data-lead="{{ $sup->lead_time_days ?? 14 }}"
                                        {{ (old('supplier_id', $selectedSupplierId) == $sup->id) ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' ? ($sup->name_ar ?? $sup->name_en) : $sup->name_en }} ({{ $sup->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Internal RFQ Reference -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.rfq_reference') }}</label>
                        <input type="text" name="rfq_reference" value="{{ old('rfq_reference') }}" class="form-control text-sm" placeholder="e.g. RFQ-RAW-2026-Q1">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.status') }} <span class="text-rose-500">*</span></label>
                        <select name="status" required class="form-select text-sm font-bold">
                            @foreach(['draft', 'pending', 'accepted', 'rejected'] as $st)
                                <option value="{{ $st }}" {{ old('status', 'pending') === $st ? 'selected' : '' }}>
                                    {{ __('supplier_quotations.statuses.' . $st) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quotation Date -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.quote_date') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="quote_date" value="{{ old('quote_date', date('Y-m-d')) }}" required class="form-control text-sm">
                    </div>

                    <!-- Valid Until -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.valid_until') }}</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" class="form-control text-sm">
                    </div>

                    <!-- Currency -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.currency') }} <span class="text-rose-500">*</span></label>
                        <select name="currency" id="currencySelect" required class="form-select text-sm font-bold" onchange="updateCurrencySymbols()">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr }}" {{ old('currency', 'SAR') === $curr ? 'selected' : '' }}>
                                    {{ $curr }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Exchange Rate -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.exchange_rate') }} <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.0001" min="0.0001" name="exchange_rate" value="{{ old('exchange_rate', '1.0000') }}" required class="form-control text-sm font-mono">
                    </div>

                    <!-- Payment Terms -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.payment_terms') }}</label>
                        <input type="text" name="payment_terms" id="paymentTermsInput" value="{{ old('payment_terms', 'Net 30') }}" class="form-control text-sm" placeholder="Net 30, LC, Advance">
                    </div>

                    <!-- Lead Time Days -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.lead_time_days') }} <span class="text-rose-500">*</span></label>
                        <input type="number" name="lead_time_days" id="leadTimeInput" value="{{ old('lead_time_days', 14) }}" min="1" max="365" required class="form-control text-sm">
                    </div>
                </div>
            </div>

            <!-- Card 2: Itemized Line Items Table -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-purple-600"></i>
                        <span>{{ __('supplier_quotations.line_items') }}</span>
                    </h3>
                    <button type="button" onclick="addNewItemRow()" class="btn btn-sm btn-secondary text-xs font-bold flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-plus text-sky-500"></i>
                        <span>{{ __('supplier_quotations.add_item') }}</span>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table-modern text-xs" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 25%;">{{ __('supplier_quotations.item_type') }} &amp; Material</th>
                                <th style="width: 20%;">{{ app()->getLocale() === 'ar' ? 'الوصف المخصص' : 'Description / Item Name' }}</th>
                                <th style="width: 12%;">{{ __('supplier_quotations.quantity') }}</th>
                                <th style="width: 14%;">{{ __('supplier_quotations.unit_price') }}</th>
                                <th style="width: 10%;">{{ __('supplier_quotations.tax_rate') }}</th>
                                <th style="width: 14%;" class="text-end">{{ __('supplier_quotations.total_price') }}</th>
                                <th style="width: 5%;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody" class="divide-y divide-gray-100 dark:divide-gray-800">
                            <!-- Dynamic Rows will be rendered by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Financial Totals Summary Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('supplier_quotations.notes') }}</label>
                        <textarea name="notes" rows="3" class="form-control text-xs" placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظات التوريد، شروط الشحن Incoterms (CIF, FOB, DDP)...' : 'Incoterms (CIF, FOB, DDP), quality requirements, shipping instructions...' }}">{{ old('notes') }}</textarea>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/60 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 space-y-2.5 text-xs">
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>{{ __('supplier_quotations.subtotal') }}:</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white" id="summarySubtotal">0.00</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>{{ __('supplier_quotations.tax_amount') }}:</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white" id="summaryTax">0.00</span>
                        </div>
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-slate-600 dark:text-slate-400">{{ __('supplier_quotations.freight_estimate') }}:</span>
                            <input type="number" step="0.01" min="0" name="estimated_freight" id="inputFreight" value="{{ old('estimated_freight', 0) }}" class="form-control text-xs font-mono w-28 text-end" oninput="calculateTotals()">
                        </div>
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-slate-600 dark:text-slate-400">{{ __('supplier_quotations.customs_estimate') }}:</span>
                            <input type="number" step="0.01" min="0" name="estimated_customs" id="inputCustoms" value="{{ old('estimated_customs', 0) }}" class="form-control text-xs font-mono w-28 text-end" oninput="calculateTotals()">
                        </div>
                        <div class="flex justify-between items-center pt-2.5 border-t border-gray-200 dark:border-gray-700 font-extrabold text-sm text-slate-900 dark:text-white">
                            <span>{{ __('supplier_quotations.grand_total') }}:</span>
                            <span class="text-base text-primary font-mono" id="summaryGrandTotal">0.00 SAR</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.supplier-quotations.index') }}" class="btn btn-secondary text-sm">
                    <i class="fa-solid fa-arrow-left mr-1.5 ml-1.5 {{ app()->getLocale() === 'ar' ? 'fa-flip-horizontal' : '' }}"></i>
                    {{ app()->getLocale() === 'ar' ? 'إلغاء والعودة' : 'Cancel & Return' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-md hover:shadow-lg transition-all px-6 py-2.5">
                    <i class="fa-solid fa-save mr-1.5 ml-1.5"></i>
                    {{ app()->getLocale() === 'ar' ? 'حفظ وتسجيل عرض السعر' : 'Save Supplier Quotation' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Data Payloads for JS Dynamic Rows -->
    <script>
        const RAW_MATERIALS = @json($rawMaterials);
        const PRODUCTS = @json($products);
        let rowCounter = 0;

        function onSupplierChanged(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) return;

            const curr = opt.getAttribute('data-currency');
            const terms = opt.getAttribute('data-terms');
            const lead = opt.getAttribute('data-lead');

            if (curr) {
                const currSelect = document.getElementById('currencySelect');
                if (currSelect) {
                    currSelect.value = curr;
                    updateCurrencySymbols();
                }
            }
            if (terms) {
                const termsInput = document.getElementById('paymentTermsInput');
                if (termsInput) termsInput.value = terms;
            }
            if (lead) {
                const leadInput = document.getElementById('leadTimeInput');
                if (leadInput) leadInput.value = lead;
            }
        }

        function updateCurrencySymbols() {
            const curr = document.getElementById('currencySelect')?.value || 'SAR';
            calculateTotals();
        }

        function addNewItemRow(presetData = null) {
            rowCounter++;
            const tbody = document.getElementById('itemsTableBody');
            const tr = document.createElement('tr');
            tr.id = 'row-' + rowCounter;

            const idx = rowCounter;

            let matOptions = '<option value="">-- ' + '{{ __("supplier_quotations.select_item") }}' + ' --</option>';
            RAW_MATERIALS.forEach(m => {
                matOptions += `<option value="${m.id}" data-type="raw_material" data-cost="${m.standard_cost || 0}" data-name="${m.name_en}">${m.name_en} (${m.material_code || 'RAW'})</option>`;
            });
            PRODUCTS.forEach(p => {
                matOptions += `<option value="${p.id}" data-type="product" data-cost="${p.cost_price || 0}" data-name="${p.name_en}">[Product] ${p.name_en} (${p.sku || 'SKU'})</option>`;
            });

            tr.innerHTML = `
                <td>
                    <select name="items[${idx}][catalog_item]" class="form-select text-xs item-catalog-select" onchange="onCatalogItemSelected(${idx}, this)" required>
                        ${matOptions}
                    </select>
                    <input type="hidden" name="items[${idx}][item_type]" id="item-type-${idx}" value="raw_material">
                    <input type="hidden" name="items[${idx}][raw_material_id]" id="item-raw-id-${idx}" value="">
                    <input type="hidden" name="items[${idx}][product_id]" id="item-prod-id-${idx}" value="">
                </td>
                <td>
                    <input type="text" name="items[${idx}][item_name]" id="item-name-${idx}" required class="form-control text-xs" placeholder="Material or grade specification">
                </td>
                <td>
                    <input type="number" step="0.0001" min="0.0001" name="items[${idx}][quantity]" id="item-qty-${idx}" value="100.00" required class="form-control text-xs font-mono text-end" oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="number" step="0.0001" min="0" name="items[${idx}][unit_price]" id="item-price-${idx}" value="0.00" required class="form-control text-xs font-mono text-end" oninput="calculateRow(${idx})">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" max="100" name="items[${idx}][tax_rate]" id="item-taxrate-${idx}" value="15.00" required class="form-control text-xs font-mono text-end" oninput="calculateRow(${idx})">
                </td>
                <td class="text-end font-bold font-mono text-slate-800 dark:text-white" id="item-total-${idx}">
                    0.00
                </td>
                <td class="text-center">
                    <button type="button" onclick="removeItemRow(${idx})" class="text-rose-500 hover:text-rose-700 p-1" title="Remove Row">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            calculateRow(idx);
        }

        function onCatalogItemSelected(idx, select) {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) return;

            const type = opt.getAttribute('data-type');
            const cost = parseFloat(opt.getAttribute('data-cost')) || 0;
            const name = opt.getAttribute('data-name') || '';

            const typeInput = document.getElementById(`item-type-${idx}`);
            const rawIdInput = document.getElementById(`item-raw-id-${idx}`);
            const prodIdInput = document.getElementById(`item-prod-id-${idx}`);
            const nameInput = document.getElementById(`item-name-${idx}`);
            const priceInput = document.getElementById(`item-price-${idx}`);

            if (typeInput) typeInput.value = type;
            if (type === 'raw_material') {
                if (rawIdInput) rawIdInput.value = opt.value;
                if (prodIdInput) prodIdInput.value = '';
            } else {
                if (prodIdInput) prodIdInput.value = opt.value;
                if (rawIdInput) rawIdInput.value = '';
            }

            if (nameInput && !nameInput.value) nameInput.value = name;
            if (priceInput && (!priceInput.value || parseFloat(priceInput.value) === 0)) priceInput.value = cost.toFixed(4);

            calculateRow(idx);
        }

        function calculateRow(idx) {
            const qty = parseFloat(document.getElementById(`item-qty-${idx}`)?.value) || 0;
            const price = parseFloat(document.getElementById(`item-price-${idx}`)?.value) || 0;
            const taxRate = parseFloat(document.getElementById(`item-taxrate-${idx}`)?.value) || 0;

            const sub = qty * price;
            const tax = sub * (taxRate / 100);
            const total = sub + tax;

            const totalEl = document.getElementById(`item-total-${idx}`);
            if (totalEl) totalEl.textContent = total.toFixed(2);

            calculateTotals();
        }

        function removeItemRow(idx) {
            const tr = document.getElementById('row-' + idx);
            if (tr) tr.remove();
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            let taxTotal = 0;

            document.querySelectorAll('#itemsTableBody tr').forEach(tr => {
                const id = tr.id.replace('row-', '');
                const qty = parseFloat(document.getElementById(`item-qty-${id}`)?.value) || 0;
                const price = parseFloat(document.getElementById(`item-price-${id}`)?.value) || 0;
                const taxRate = parseFloat(document.getElementById(`item-taxrate-${id}`)?.value) || 0;

                const sub = qty * price;
                const tax = sub * (taxRate / 100);

                subtotal += sub;
                taxTotal += tax;
            });

            const freight = parseFloat(document.getElementById('inputFreight')?.value) || 0;
            const customs = parseFloat(document.getElementById('inputCustoms')?.value) || 0;
            const grandTotal = subtotal + taxTotal + freight + customs;
            const curr = document.getElementById('currencySelect')?.value || 'SAR';

            const subEl = document.getElementById('summarySubtotal');
            const taxEl = document.getElementById('summaryTax');
            const grandEl = document.getElementById('summaryGrandTotal');

            if (subEl) subEl.textContent = subtotal.toFixed(2) + ' ' + curr;
            if (taxEl) taxEl.textContent = taxTotal.toFixed(2) + ' ' + curr;
            if (grandEl) grandEl.textContent = grandTotal.toFixed(2) + ' ' + curr;
        }

        document.addEventListener('DOMContentLoaded', () => {
            addNewItemRow();
        });
    </script>
</x-layouts.admin>
