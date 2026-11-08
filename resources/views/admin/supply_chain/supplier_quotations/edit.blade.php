<x-layouts.admin 
    :pageTitle="__('supplier_quotations.edit_quote') . ': ' . $supplierQuotation->quote_number" 
    :pageSubtitle="__('supplier_quotations.subtitle')"
    :breadcrumbs="[__('supplier_quotations.title') => route('admin.supplier-quotations.index'), $supplierQuotation->quote_number => route('admin.supplier-quotations.show', $supplierQuotation), __('supplier_quotations.edit_quote') => '#']"
>
    <div class="max-w-7xl mx-auto pb-12">
        <form method="POST" action="{{ route('admin.supplier-quotations.update', $supplierQuotation) }}" id="quotationForm" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Card 1: Vendor & Commercial Terms -->
            <div class="card p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 rounded-2xl bg-white dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5 flex-wrap gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-base shadow-sm">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-white">
                                {{ __('supplier_quotations.vendor_parameters') }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ __('supplier_quotations.select_vendor_hint') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-sky-50 text-sky-700 dark:bg-sky-950/70 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                            <i class="fa-solid fa-receipt text-[10px]"></i>
                            {{ $supplierQuotation->quote_number }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Quotation Number -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('supplier_quotations.quote_number') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-hashtag"></i>
                            </span>
                            <input type="text" 
                                   name="quote_number" 
                                   value="{{ old('quote_number', $supplierQuotation->quote_number) }}" 
                                   required 
                                   class="form-control text-sm font-mono font-bold tracking-wider pl-8 @error('quote_number') border-rose-500 @enderror">
                        </div>
                        @error('quote_number') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Vendor / Supplier Selection -->
                    <div class="lg:col-span-2">
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('supplier_quotations.supplier') }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="supplier_id" id="supplierSelect" required class="form-select text-sm font-semibold @error('supplier_id') border-rose-500 @enderror" onchange="onSupplierChanged(this)">
                            <option value="">-- {{ __('supplier_quotations.select_supplier') }} --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" 
                                        data-currency="{{ $sup->currency ?: $baseCurrency }}"
                                        data-terms="{{ $sup->payment_terms ?: 'Net 30' }}"
                                        data-lead="{{ $sup->lead_time_days ?: 14 }}"
                                        data-name="{{ $sup->name }}"
                                        data-code="{{ $sup->code }}"
                                        data-phone="{{ $sup->phone ?: '' }}"
                                        data-email="{{ $sup->email ?: '' }}"
                                        data-rating="{{ $sup->rating ?: '5.0' }}"
                                        data-gmp="{{ $sup->gmp_certified ? '1' : '0' }}"
                                        data-country="{{ $sup->country?->name_en ?: '' }}"
                                        {{ (old('supplier_id', $supplierQuotation->supplier_id) == $sup->id) ? 'selected' : '' }}>
                                    {{ $sup->name }} — [{{ $sup->code }}@if($sup->phone) | Tel: {{ $sup->phone }}@endif @if($sup->currency) | {{ $sup->currency }}@endif]
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('supplier_quotations.status') }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" required class="form-select text-sm font-bold">
                            @foreach(['draft', 'pending', 'accepted', 'rejected'] as $st)
                                <option value="{{ $st }}" {{ old('status', $supplierQuotation->status) === $st ? 'selected' : '' }}>
                                    {{ __('supplier_quotations.statuses.' . $st) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Internal RFQ Reference -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('supplier_quotations.rfq_reference') }}
                        </label>
                        <input type="text" name="rfq_reference" value="{{ old('rfq_reference', $supplierQuotation->rfq_reference) }}" class="form-control text-sm" placeholder="e.g. RFQ-RAW-2026-Q1">
                    </div>

                    <!-- Quotation Date -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('supplier_quotations.quote_date') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="quote_date" value="{{ old('quote_date', $supplierQuotation->quote_date?->format('Y-m-d')) }}" required class="form-control text-sm font-mono">
                    </div>

                    <!-- Valid Until -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('supplier_quotations.valid_until') }}
                        </label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', $supplierQuotation->valid_until?->format('Y-m-d')) }}" class="form-control text-sm font-mono">
                    </div>

                    <!-- Currency & Exchange Rate (Automatic based on Settings & Supplier) -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>{{ __('supplier_quotations.currency') }} <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] text-sky-600 dark:text-sky-400 font-mono font-normal" id="baseCurrencyHint">
                                (Base: {{ $baseCurrency }})
                            </span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="currency" id="currencySelect" required class="form-select text-sm font-bold" onchange="onCurrencyChanged()">
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr }}" {{ old('currency', $supplierQuotation->currency) === $curr ? 'selected' : '' }}>
                                        {{ $curr }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="number" 
                                   step="0.0001" 
                                   min="0.0001" 
                                   name="exchange_rate" 
                                   id="exchangeRateInput"
                                   value="{{ old('exchange_rate', $supplierQuotation->exchange_rate ?: 1.0000) }}" 
                                   required 
                                   class="form-control text-sm font-mono font-bold text-end"
                                   title="{{ __('supplier_quotations.exchange_rate') }}"
                                   oninput="calculateTotals()">
                        </div>
                    </div>

                    <!-- Payment Terms -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('supplier_quotations.payment_terms') }}
                        </label>
                        <input type="text" name="payment_terms" id="paymentTermsInput" value="{{ old('payment_terms', $supplierQuotation->payment_terms) }}" class="form-control text-sm" placeholder="e.g. Net 30, LC, Advance">
                    </div>

                    <!-- Lead Time Days -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('supplier_quotations.lead_time_days') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="lead_time_days" id="leadTimeInput" value="{{ old('lead_time_days', $supplierQuotation->lead_time_days ?: 14) }}" min="1" max="365" required class="form-control text-sm font-mono">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                {{ __('supplier_quotations.days_unit') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Vendor Intelligence Card (Populated when vendor selected) -->
                <div id="vendorIntelCard" class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 transition-all">
                    <div class="flex items-center justify-between flex-wrap gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">
                                <i class="fa-solid fa-building-circle-check"></i>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span id="intelVendorName">{{ $supplierQuotation->supplier?->name ?? '--' }}</span>
                                    <span id="intelVendorCode" class="font-mono text-[10px] text-slate-500 bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded">{{ $supplierQuotation->supplier?->code ?? '--' }}</span>
                                    <span id="intelGmpBadge" class="{{ $supplierQuotation->supplier?->gmp_certified ? '' : 'hidden' }} badge badge-emerald text-[10px]">cGMP Qualified</span>
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-3 mt-0.5">
                                    <span id="intelCountry"><i class="fa-solid fa-earth-americas mr-1"></i><span>{{ $supplierQuotation->supplier?->country?->name_en ?? 'International' }}</span></span>
                                    <span id="intelPhone"><i class="fa-solid fa-phone mr-1"></i><span>{{ $supplierQuotation->supplier?->phone ?? 'No phone' }}</span></span>
                                    <span id="intelRating"><i class="fa-solid fa-star text-amber-500 mr-1"></i><span>{{ $supplierQuotation->supplier?->rating ?? '5.0' }}/100</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 text-end">
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">{{ __('supplier_quotations.currency') }}</span>
                                <span id="intelCurrency" class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $supplierQuotation->currency }}</span>
                            </div>
                            <div class="border-l border-slate-200 dark:border-slate-700 pl-4">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">{{ __('supplier_quotations.terms') }}</span>
                                <span id="intelTerms" class="font-bold text-slate-800 dark:text-slate-200">{{ $supplierQuotation->payment_terms ?? 'Net 30' }}</span>
                            </div>
                            <div class="border-l border-slate-200 dark:border-slate-700 pl-4">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">{{ __('supplier_quotations.lead_time') }}</span>
                                <span id="intelLead" class="font-bold text-slate-800 dark:text-slate-200">{{ $supplierQuotation->lead_time_days }} Days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Itemized Line Items Table -->
            <div class="card p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 rounded-2xl bg-white dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 flex-wrap gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-base shadow-sm">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-white">
                                {{ __('supplier_quotations.line_items') }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Itemized material procurement, unit pricing, and VAT calculations.
                            </p>
                        </div>
                    </div>

                    <!-- Quick Action Add Buttons -->
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="addNewItemRow({ item_type: 'raw_material' })" class="btn btn-sm btn-outline-success text-xs font-bold flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-flask text-emerald-500"></i>
                            <span>{{ __('supplier_quotations.add_raw_material') }}</span>
                        </button>
                        <button type="button" onclick="addNewItemRow({ item_type: 'product' })" class="btn btn-sm btn-outline-primary text-xs font-bold flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-box text-sky-500"></i>
                            <span>{{ __('supplier_quotations.add_packaging_product') }}</span>
                        </button>
                        <button type="button" onclick="addNewItemRow()" class="btn btn-sm btn-secondary text-xs font-bold flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus text-slate-600 dark:text-slate-300"></i>
                            <span>{{ __('supplier_quotations.add_item') }}</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full" id="itemsTable">
                        <thead>
                            <tr class="text-xs text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                <th style="width: 4%;" class="text-center">#</th>
                                <th style="width: 26%;">{{ __('supplier_quotations.item_type') }} &amp; Catalog Item</th>
                                <th style="width: 20%;">{{ app()->getLocale() === 'ar' ? 'الوصف المخصص للمادة' : 'Description / Item Name' }}</th>
                                <th style="width: 13%;">{{ __('supplier_quotations.quantity') }} &amp; {{ __('supplier_quotations.uom') }}</th>
                                <th style="width: 14%;">
                                    {{ __('supplier_quotations.unit_price') }} 
                                    <span class="currency-tag text-[10px] font-mono text-sky-600 dark:text-sky-400 font-bold">({{ $supplierQuotation->currency }})</span>
                                </th>
                                <th style="width: 9%;">{{ __('supplier_quotations.tax_rate') }}</th>
                                <th style="width: 14%;" class="text-end">
                                    {{ __('supplier_quotations.total_price') }}
                                    <span class="currency-tag text-[10px] font-mono text-sky-600 dark:text-sky-400 font-bold">({{ $supplierQuotation->currency }})</span>
                                </th>
                                <th style="width: 4%;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            <!-- Dynamic Rows rendered via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State Placeholder (when 0 rows) -->
                <div id="emptyTableState" class="hidden text-center py-8 px-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3 text-lg">
                        <i class="fa-solid fa-dolly"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                        {{ __('supplier_quotations.no_items_placeholder') }}
                    </p>
                    <div class="mt-3 flex justify-center gap-2">
                        <button type="button" onclick="addNewItemRow({ item_type: 'raw_material' })" class="btn btn-sm btn-primary text-xs">
                            <i class="fa-solid fa-plus mr-1"></i> {{ __('supplier_quotations.add_raw_material') }}
                        </button>
                    </div>
                </div>

                <!-- Financial Totals Summary Grid (Enterprise Accounting Breakdown) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-5 border-t border-slate-100 dark:border-slate-800">
                    <!-- Left: Incoterms & Procurement Notes -->
                    <div class="lg:col-span-7 space-y-3">
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-clipboard-check text-sky-600"></i>
                            <span>{{ __('supplier_quotations.notes') }}</span>
                        </label>
                        <textarea name="notes" rows="4" class="form-control text-xs leading-relaxed" placeholder="{{ __('supplier_quotations.notes_placeholder') }}">{{ old('notes', $supplierQuotation->notes) }}</textarea>
                    </div>

                    <!-- Right: Accounting Financial Ledger Summary -->
                    <div class="lg:col-span-5 bg-slate-50/70 dark:bg-slate-900/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3 text-xs shadow-sm">
                        <div class="border-b border-slate-200 dark:border-slate-800 pb-2.5 flex items-center justify-between">
                            <span class="font-extrabold text-sm text-slate-800 dark:text-white flex items-center gap-1.5">
                                <i class="fa-solid fa-calculator text-primary"></i>
                                <span>{{ __('supplier_quotations.commercial_settlement') }}</span>
                            </span>
                            <span class="font-mono text-[11px] font-bold text-slate-500" id="summaryCurrencyCode">
                                {{ $supplierQuotation->currency }}
                            </span>
                        </div>

                        <!-- Subtotal -->
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>{{ __('supplier_quotations.subtotal') }}:</span>
                            <span class="font-mono font-bold text-sm text-slate-900 dark:text-white" id="summarySubtotal">
                                {{ number_format($supplierQuotation->subtotal, 2) }} {{ $supplierQuotation->currency }}
                            </span>
                        </div>

                        <!-- VAT / Tax -->
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>{{ __('supplier_quotations.tax_amount') }} (VAT):</span>
                            <span class="font-mono font-bold text-sm text-slate-900 dark:text-white" id="summaryTax">
                                {{ number_format($supplierQuotation->tax_amount, 2) }} {{ $supplierQuotation->currency }}
                            </span>
                        </div>

                        <!-- Freight Estimate -->
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-slate-600 dark:text-slate-400">{{ __('supplier_quotations.freight_estimate') }}:</span>
                            <div class="relative w-36">
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       name="estimated_freight" 
                                       id="inputFreight" 
                                       value="{{ old('estimated_freight', $supplierQuotation->estimated_freight ?: 0) }}" 
                                       class="form-control text-xs font-mono font-bold text-end pr-10" 
                                       oninput="calculateTotals()">
                                <span class="currency-tag absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-[10px] font-mono text-slate-400">
                                    {{ $supplierQuotation->currency }}
                                </span>
                            </div>
                        </div>

                        <!-- Customs Estimate -->
                        <div class="flex justify-between items-center gap-3">
                            <span class="text-slate-600 dark:text-slate-400">{{ __('supplier_quotations.customs_estimate') }}:</span>
                            <div class="relative w-36">
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       name="estimated_customs" 
                                       id="inputCustoms" 
                                       value="{{ old('estimated_customs', $supplierQuotation->estimated_customs ?: 0) }}" 
                                       class="form-control text-xs font-mono font-bold text-end pr-10" 
                                       oninput="calculateTotals()">
                                <span class="currency-tag absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-[10px] font-mono text-slate-400">
                                    {{ $supplierQuotation->currency }}
                                </span>
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200 dark:border-slate-700 font-extrabold text-sm text-slate-900 dark:text-white">
                            <span>{{ __('supplier_quotations.grand_total') }}:</span>
                            <span class="text-lg text-primary font-mono font-black" id="summaryGrandTotal">
                                {{ number_format($supplierQuotation->grand_total, 2) }} {{ $supplierQuotation->currency }}
                            </span>
                        </div>

                        <!-- Base Currency Equivalent (If foreign currency) -->
                        <div id="baseCurrencyEquivalentBox" class="hidden p-2.5 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-900/40 text-[11px] text-sky-900 dark:text-sky-300 flex items-center justify-between">
                            <span class="font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-coins text-sky-600"></i>
                                <span>{{ __('supplier_quotations.base_currency_equivalent') }}:</span>
                            </span>
                            <span class="font-mono font-bold" id="summaryBaseEquivalent">
                                {{ $baseCurrencySymbol }} 0.00 {{ $baseCurrency }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.supplier-quotations.show', $supplierQuotation) }}" class="btn btn-secondary text-sm font-semibold flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left {{ app()->getLocale() === 'ar' ? 'fa-flip-horizontal' : '' }}"></i>
                    <span>{{ __('supplier_quotations.cancel_return') }}</span>
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-md hover:shadow-lg transition-all px-8 py-2.5 flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>{{ __('supplier_quotations.save_changes') }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Data Payloads & Reactive Javascript Architecture -->
    <script>
        const RAW_MATERIALS = @json($rawMaterials);
        const PRODUCTS = @json($products);
        const BASE_CURRENCY = @json($baseCurrency);
        const BASE_CURRENCY_SYMBOL = @json($baseCurrencySymbol);
        const INITIAL_ITEMS = @json($supplierQuotation->items);
        let rowCounter = 0;

        function onSupplierChanged(select) {
            const opt = select.options[select.selectedIndex];
            const intelCard = document.getElementById('vendorIntelCard');

            if (!opt || !opt.value) {
                if (intelCard) intelCard.classList.add('hidden');
                return;
            }

            const curr = opt.getAttribute('data-currency');
            const terms = opt.getAttribute('data-terms');
            const lead = opt.getAttribute('data-lead');
            const name = opt.getAttribute('data-name');
            const code = opt.getAttribute('data-code');
            const phone = opt.getAttribute('data-phone');
            const country = opt.getAttribute('data-country');
            const rating = opt.getAttribute('data-rating');
            const gmp = opt.getAttribute('data-gmp') === '1';

            if (intelCard) {
                intelCard.classList.remove('hidden');
                document.getElementById('intelVendorName').textContent = name || '--';
                document.getElementById('intelVendorCode').textContent = code || '--';
                document.getElementById('intelCurrency').textContent = curr || BASE_CURRENCY;
                document.getElementById('intelTerms').textContent = terms || 'Net 30';
                document.getElementById('intelLead').textContent = (lead || 14) + ' Days';
                
                const gmpBadge = document.getElementById('intelGmpBadge');
                if (gmpBadge) {
                    gmp ? gmpBadge.classList.remove('hidden') : gmpBadge.classList.add('hidden');
                }

                const countryEl = document.getElementById('intelCountry');
                if (countryEl) countryEl.querySelector('span').textContent = country || 'International';

                const phoneEl = document.getElementById('intelPhone');
                if (phoneEl) phoneEl.querySelector('span').textContent = phone || 'No phone recorded';

                const ratingEl = document.getElementById('intelRating');
                if (ratingEl) ratingEl.querySelector('span').textContent = (rating || '5.0') + '/100';
            }

            if (curr && !document.getElementById('currencySelect').value) {
                document.getElementById('currencySelect').value = curr;
                onCurrencyChanged();
            }
        }

        function onCurrencyChanged() {
            const curr = document.getElementById('currencySelect')?.value || BASE_CURRENCY;

            document.querySelectorAll('.currency-tag').forEach(el => {
                el.textContent = `(${curr})`;
            });

            const summaryCodeEl = document.getElementById('summaryCurrencyCode');
            if (summaryCodeEl) summaryCodeEl.textContent = curr;

            calculateTotals();
        }

        function addNewItemRow(presetData = null) {
            rowCounter++;
            const tbody = document.getElementById('itemsTableBody');
            const emptyState = document.getElementById('emptyTableState');
            if (emptyState) emptyState.classList.add('hidden');

            const tr = document.createElement('tr');
            tr.id = 'row-' + rowCounter;
            tr.className = 'hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors';

            const idx = rowCounter;
            const currentCurrency = document.getElementById('currencySelect')?.value || BASE_CURRENCY;

            const selectedRawId = presetData ? presetData.raw_material_id : '';
            const selectedProdId = presetData ? presetData.product_id : '';
            const itemName = presetData ? presetData.item_name : '';
            const itemQty = presetData ? (presetData.quantity || 100.0) : 100.00;
            const itemPrice = presetData ? (presetData.unit_price || 0.0) : 0.00;
            const itemTaxRate = presetData ? (presetData.tax_rate ?? 15.0) : 15.00;
            const itemType = presetData ? (presetData.item_type || 'raw_material') : 'raw_material';

            let selectedUom = 'kg';

            // Build grouped options
            let optionsHtml = '<option value="">' + '{{ __("supplier_quotations.select_catalog_item") }}' + '</option>';
            
            // Raw materials group
            optionsHtml += '<optgroup label="{{ __("supplier_quotations.raw_materials_group") }}">';
            RAW_MATERIALS.forEach(m => {
                const isSel = (selectedRawId && selectedRawId == m.id) ? 'selected' : '';
                if (isSel) selectedUom = m.unit_of_measure || 'kg';
                optionsHtml += `<option value="${m.id}" data-type="raw_material" data-cost="${m.standard_cost || 0}" data-uom="${m.unit_of_measure || 'kg'}" data-name="${m.name_en}" ${isSel}>${m.name_en} (${m.code || 'RAW'})</option>`;
            });
            optionsHtml += '</optgroup>';

            // Products group
            optionsHtml += '<optgroup label="{{ __("supplier_quotations.products_group") }}">';
            PRODUCTS.forEach(p => {
                const isSel = (selectedProdId && selectedProdId == p.id) ? 'selected' : '';
                if (isSel) selectedUom = 'units';
                optionsHtml += `<option value="${p.id}" data-type="product" data-cost="${p.cost_price || 0}" data-uom="units" data-name="${p.name_en}" ${isSel}>[Product] ${p.name_en} (${p.sku || 'SKU'})</option>`;
            });
            optionsHtml += '</optgroup>';

            tr.innerHTML = `
                <td class="text-center font-mono text-xs text-slate-400 font-bold py-3.5">
                    ${idx}
                </td>
                <td class="py-2.5">
                    <select name="items[${idx}][catalog_item]" class="form-select text-xs font-semibold item-catalog-select" onchange="onCatalogItemSelected(${idx}, this)" required>
                        ${optionsHtml}
                    </select>
                    <input type="hidden" name="items[${idx}][item_type]" id="item-type-${idx}" value="${itemType}">
                    <input type="hidden" name="items[${idx}][raw_material_id]" id="item-raw-id-${idx}" value="${selectedRawId || ''}">
                    <input type="hidden" name="items[${idx}][product_id]" id="item-prod-id-${idx}" value="${selectedProdId || ''}">
                </td>
                <td class="py-2.5">
                    <input type="text" name="items[${idx}][item_name]" id="item-name-${idx}" value="${itemName}" required class="form-control text-xs" placeholder="Grade, chemical specs or item title">
                </td>
                <td class="py-2.5">
                    <div class="relative">
                        <input type="number" step="0.0001" min="0.0001" name="items[${idx}][quantity]" id="item-qty-${idx}" value="${itemQty}" required class="form-control text-xs font-mono font-bold text-end pr-11" oninput="calculateRow(${idx})">
                        <span class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-[10px] font-mono text-slate-400" id="item-uom-${idx}">${selectedUom}</span>
                    </div>
                </td>
                <td class="py-2.5">
                    <div class="relative">
                        <input type="number" step="0.0001" min="0" name="items[${idx}][unit_price]" id="item-price-${idx}" value="${itemPrice}" required class="form-control text-xs font-mono font-bold text-end pr-10" oninput="calculateRow(${idx})">
                        <span class="currency-tag absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-[10px] font-mono text-slate-400">(${currentCurrency})</span>
                    </div>
                    <span class="text-[9px] text-slate-400 font-mono mt-0.5 block" id="item-stdcost-${idx}"></span>
                </td>
                <td class="py-2.5">
                    <input type="number" step="0.01" min="0" max="100" name="items[${idx}][tax_rate]" id="item-taxrate-${idx}" value="${itemTaxRate}" required class="form-control text-xs font-mono text-end" oninput="calculateRow(${idx})">
                </td>
                <td class="text-end font-bold font-mono text-slate-800 dark:text-white py-2.5" id="item-total-${idx}">
                    0.00 ${currentCurrency}
                </td>
                <td class="text-center py-2.5">
                    <button type="button" onclick="removeItemRow(${idx})" class="w-7 h-7 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 inline-flex items-center justify-center transition-colors cursor-pointer" title="{{ __('supplier_quotations.remove_row') }}">
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
            const uom = opt.getAttribute('data-uom') || 'units';
            const name = opt.getAttribute('data-name') || '';

            const typeInput = document.getElementById(`item-type-${idx}`);
            const rawIdInput = document.getElementById(`item-raw-id-${idx}`);
            const prodIdInput = document.getElementById(`item-prod-id-${idx}`);
            const nameInput = document.getElementById(`item-name-${idx}`);
            const priceInput = document.getElementById(`item-price-${idx}`);
            const uomEl = document.getElementById(`item-uom-${idx}`);
            const stdEl = document.getElementById(`item-stdcost-${idx}`);

            if (typeInput) typeInput.value = type;
            if (type === 'raw_material') {
                if (rawIdInput) rawIdInput.value = opt.value;
                if (prodIdInput) prodIdInput.value = '';
            } else {
                if (prodIdInput) prodIdInput.value = opt.value;
                if (rawIdInput) rawIdInput.value = '';
            }

            if (uomEl) uomEl.textContent = uom;
            if (nameInput && (!nameInput.value || nameInput.value.trim() === '')) {
                nameInput.value = name;
            }
            if (priceInput && (!priceInput.value || parseFloat(priceInput.value) === 0)) {
                priceInput.value = cost.toFixed(4);
            }
            if (stdEl) {
                stdEl.textContent = `Std: ${cost.toFixed(2)}`;
            }

            calculateRow(idx);
        }

        function calculateRow(idx) {
            const qty = parseFloat(document.getElementById(`item-qty-${idx}`)?.value) || 0;
            const price = parseFloat(document.getElementById(`item-price-${idx}`)?.value) || 0;
            const taxRate = parseFloat(document.getElementById(`item-taxrate-${idx}`)?.value) || 0;
            const curr = document.getElementById('currencySelect')?.value || BASE_CURRENCY;

            const sub = qty * price;
            const tax = sub * (taxRate / 100);
            const total = sub + tax;

            const totalEl = document.getElementById(`item-total-${idx}`);
            if (totalEl) totalEl.textContent = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + curr;

            calculateTotals();
        }

        function removeItemRow(idx) {
            const tr = document.getElementById('row-' + idx);
            if (tr) tr.remove();

            const rowsCount = document.querySelectorAll('#itemsTableBody tr').length;
            const emptyState = document.getElementById('emptyTableState');
            if (rowsCount === 0 && emptyState) {
                emptyState.classList.remove('hidden');
            }

            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            let taxTotal = 0;
            const curr = document.getElementById('currencySelect')?.value || BASE_CURRENCY;

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
            const fxRate = parseFloat(document.getElementById('exchangeRateInput')?.value) || 1.0;

            const subEl = document.getElementById('summarySubtotal');
            const taxEl = document.getElementById('summaryTax');
            const grandEl = document.getElementById('summaryGrandTotal');

            if (subEl) subEl.textContent = subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + curr;
            if (taxEl) taxEl.textContent = taxTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + curr;
            if (grandEl) grandEl.textContent = grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + curr;

            // Base Currency Equivalent Calculation
            const baseBox = document.getElementById('baseCurrencyEquivalentBox');
            const baseEquivalentEl = document.getElementById('summaryBaseEquivalent');
            if (baseBox && baseEquivalentEl) {
                if (curr !== BASE_CURRENCY) {
                    baseBox.classList.remove('hidden');
                    const baseAmount = grandTotal * fxRate;
                    baseEquivalentEl.textContent = `${BASE_CURRENCY_SYMBOL} ${baseAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${BASE_CURRENCY}`;
                } else {
                    baseBox.classList.add('hidden');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const supSelect = document.getElementById('supplierSelect');
            if (supSelect && supSelect.value) {
                onSupplierChanged(supSelect);
            }
            onCurrencyChanged();

            if (Array.isArray(INITIAL_ITEMS) && INITIAL_ITEMS.length > 0) {
                INITIAL_ITEMS.forEach(item => addNewItemRow(item));
            } else {
                addNewItemRow({ item_type: 'raw_material' });
            }
        });
    </script>
</x-layouts.admin>
