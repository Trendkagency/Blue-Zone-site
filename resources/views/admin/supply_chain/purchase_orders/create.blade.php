<x-layouts.admin 
    :pageTitle="__('purchase_orders.new_po')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إنشاء أمر شراء معتمد للمنتجات التجارية أو المواد الأولية مع حساب تكاليف الوصول' : 'Create an approved purchase order for commercial products or raw materials with landed cost calculation'"
    :breadcrumbs="[
        __('purchase_orders.title') => route('admin.purchase-orders.index'), 
        __('purchase_orders.new_po') => '#'
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.supplier-quotations.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-file-invoice text-amber-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'عروض الأسعار المتاحة' : 'Supplier Quotes' }}</span>
        </a>
        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'العودة لقائمة الأوامر' : 'Back to Orders' }}</span>
        </a>
    </x-slot>
    <div class="max-w-6xl mx-auto space-y-6" x-data="purchaseOrderForm()">
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm">
                <div class="font-bold flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($selectedQuotation))
            <div class="p-4 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 flex items-center justify-between gap-3 text-sky-800 dark:text-sky-200 text-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm">
                            {{ app()->getLocale() === 'ar' ? 'تحويل من عرض السعر:' : 'Prefilled from RFQ / Quotation:' }} 
                            <span class="font-mono text-sky-600 dark:text-sky-400 font-black">{{ $selectedQuotation->quote_number }}</span>
                        </h4>
                        <p class="text-xs text-sky-600/80 dark:text-sky-300/80">
                            {{ $selectedQuotation->supplier?->name }} &bull; {{ $selectedQuotation->currency }} &bull; {{ count($selectedQuotation->items) }} {{ app()->getLocale() === 'ar' ? 'بنود مسجلة' : 'Line Items' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.supplier-quotations.show', $selectedQuotation) }}" target="_blank" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'عرض الأصل' : 'View Quote' }}
                </a>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.purchase-orders.store') }}" class="space-y-6">
            @csrf
            @if(!empty($selectedQuotation))
                <input type="hidden" name="supplier_quotation_id" value="{{ $selectedQuotation->id }}">
            @endif

            <!-- 1. Header Information & Logistics -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/50 text-sky-600 flex items-center justify-center text-sm font-bold">1</span>
                        <span>{{ app()->getLocale() === 'ar' ? 'بيانات المورد وأمر الشراء' : 'Purchase Order & Supplier Details' }}</span>
                    </h3>
                    <span class="badge badge-sky text-xs font-mono font-bold">DRAFT ORDER</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Supplier Selector with Name and Number -->
                    <div class="md:col-span-1">
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('purchase_orders.supplier') }} <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="supplier_id" 
                            required 
                            class="form-select text-sm w-full font-semibold @error('supplier_id') border-rose-500 @enderror" 
                            x-model="supplierId"
                            @change="onSupplierChange()"
                        >
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المورد المعتمد --' : '-- Select Qualified Supplier --' }}</option>
                            @foreach($suppliers as $supplier)
                                @php
                                    $isAuditExpired = $supplier->isAuditExpired();
                                    $isAuditExpiring = $supplier->isAuditExpiringSoon();
                                @endphp
                                <option 
                                    value="{{ $supplier->id }}" 
                                    {{ old('supplier_id', $selectedQuotation?->supplier_id) == $supplier->id ? 'selected' : '' }}
                                    data-currency="{{ $supplier->currency ?? 'AED' }}"
                                    data-phone="{{ $supplier->phone ?? '' }}"
                                    data-code="{{ $supplier->code ?? 'VND-'.$supplier->id }}"
                                    data-rating="{{ $supplier->rating ?? 5.0 }}"
                                    data-expired="{{ $isAuditExpired ? '1' : '0' }}"
                                    data-expiring="{{ $isAuditExpiring ? '1' : '0' }}"
                                    data-audit-date="{{ $supplier->audit_valid_until?->format('Y-m-d') ?? '' }}"
                                    @if($isAuditExpired) disabled @endif
                                >
                                    {{ $supplier->name }} — [{{ $supplier->code ?? 'VND-'.$supplier->id }}@if($supplier->phone) | {{ $supplier->phone }}@endif]@if($isAuditExpired) — ({{ app()->getLocale() === 'ar' ? 'منتهي تدقيق الجودة' : 'Audit Expired' }})@elseif($isAuditExpiring) — ({{ app()->getLocale() === 'ar' ? 'قريب الانتهاء' : 'Expiring Soon' }})@endif
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                        <!-- Selected Supplier Preview Pill -->
                        <template x-if="getSelectedSupplierInfo()">
                            <div class="mt-2.5 p-2.5 rounded-xl border text-xs flex flex-wrap items-center justify-between gap-2 shadow-xs transition-all"
                                 :class="getSelectedSupplierInfo().is_expired ? 'bg-rose-50/80 dark:bg-rose-950/30 border-rose-200 dark:border-rose-900/50 text-rose-900 dark:text-rose-200' : (getSelectedSupplierInfo().is_expiring ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-200' : 'bg-sky-50/60 dark:bg-sky-950/30 border-sky-100 dark:border-sky-900/40 text-sky-900 dark:text-sky-300')">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid" :class="getSelectedSupplierInfo().is_expired ? 'fa-triangle-exclamation text-rose-600' : (getSelectedSupplierInfo().is_expiring ? 'fa-clock text-amber-600' : 'fa-building-circle-check text-sky-600')"></i>
                                    <span class="font-bold" x-text="getSelectedSupplierInfo().name"></span>
                                    <span class="font-mono text-[11px] opacity-75" x-text="'(' + getSelectedSupplierInfo().code + ')'"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <template x-if="getSelectedSupplierInfo().is_expired">
                                        <span class="badge badge-rose text-[10px] font-bold">
                                            <i class="fa-solid fa-ban mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'منتهي تدقيق الجودة (لا يمكن الشراء)' : 'Audit Expired (Order Blocked)' }}
                                        </span>
                                    </template>
                                    <template x-if="getSelectedSupplierInfo().is_expiring && !getSelectedSupplierInfo().is_expired">
                                        <span class="badge badge-amber text-[10px] font-bold">
                                            <i class="fa-solid fa-clock mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تدقيق الجودة ينتهي قريباً' : 'Audit Expiring Soon' }}
                                        </span>
                                    </template>
                                    <template x-if="!getSelectedSupplierInfo().is_expired && !getSelectedSupplierInfo().is_expiring">
                                        <span class="badge badge-emerald text-[10px] font-bold">
                                            <i class="fa-solid fa-check mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'معتمد ومؤهل بالكامل' : 'cGMP Qualified' }}
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('purchase_orders.order_date') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required class="form-control text-sm w-full font-mono @error('order_date') border-rose-500 @enderror">
                        @error('order_date') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('purchase_orders.expected_delivery_date') }}
                        </label>
                        <input type="date" name="expected_date" value="{{ old('expected_date', date('Y-m-d', strtotime('+14 days'))) }}" class="form-control text-sm w-full font-mono @error('expected_date') border-rose-500 @enderror">
                        @error('expected_date') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('purchase_orders.destination') }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="destination_location_id" required class="form-select text-sm w-full @error('destination_location_id') border-rose-500 @enderror">
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('destination_location_id') == $location->id || $location->id === 'central_wh' ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' && !empty($location->name_ar) ? $location->name_ar : ($location->name_en ?? $location->name ?? $location->id) }}
                                </option>
                            @endforeach
                        </select>
                        @error('destination_location_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('purchase_orders.currency') }} &amp; FX Rate <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="currency" required class="form-select text-sm font-bold" x-model="currency">
                                @foreach(($currencies ?? ['USD', 'SAR', 'AED', 'EUR', 'GBP']) as $curr)
                                    <option value="{{ $curr }}">{{ $curr }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="0.0001" min="0.0001" name="exchange_rate" value="{{ old('exchange_rate', 1.0000) }}" required class="form-control text-sm font-mono font-bold" placeholder="FX Rate" title="Exchange rate to base currency">
                        </div>
                        @error('currency') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ app()->getLocale() === 'ar' ? 'رقم عرض السعر / مرجع المورد' : 'Supplier Quote / Proforma Ref' }}
                        </label>
                        <input type="text" name="supplier_reference" value="{{ old('supplier_reference', $selectedQuotation?->quote_number) }}" class="form-control text-sm w-full font-mono" placeholder="e.g. QTE-99214">
                        @error('supplier_reference') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                        {{ app()->getLocale() === 'ar' ? 'ملاحظات أو تعليمات خاصة بالشحن والتخليص' : 'Procurement Instructions & Shipping Notes' }}
                    </label>
                    <textarea name="notes" rows="2" class="form-control text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب أي متطلبات خاصة بالمطابقة أو درجات حرارة التخزين...' : 'Specify temperature sensitivity, packaging requirements, or delivery bay rules...' }}">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- 2. Dual-Catalog Line Items (Products & Raw Materials) -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 flex-wrap gap-2">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center text-sm font-bold">2</span>
                            <span>{{ app()->getLocale() === 'ar' ? 'بنود الشراء (منتجات تجارية / مواد خام)' : 'Purchase Items (Commercial Products & Raw Materials)' }}</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? 'يمكنك طلب منتجات تجارية نهائية أو مواد خام أولية في نفس أمر الشراء' : 'Add finished longevity retail goods or compounding raw materials seamlessly' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" class="btn btn-secondary btn-sm font-bold flex items-center gap-1.5 shadow-sm" @click="addItem('product')">
                            <i class="fa-solid fa-box text-sky-600"></i>
                            <span>{{ app()->getLocale() === 'ar' ? '+ إضافة منتج تجاري' : '+ Add Product' }}</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm font-bold flex items-center gap-1.5 shadow-sm" @click="addItem('raw_material')">
                            <i class="fa-solid fa-flask text-emerald-600"></i>
                            <span>{{ app()->getLocale() === 'ar' ? '+ إضافة مادة خام' : '+ Add Raw Material' }}</span>
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table text-xs w-full">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300">
                                <th style="width: 14%;">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Catalog Type' }}</th>
                                <th style="width: 36%;">{{ app()->getLocale() === 'ar' ? 'البند المطلوب' : 'Item Selection' }}</th>
                                <th style="width: 14%; text-align: right;">{{ __('purchase_orders.quantity') }}</th>
                                <th style="width: 16%; text-align: right;">{{ __('purchase_orders.unit_price') }}</th>
                                <th style="width: 15%; text-align: right;">{{ __('purchase_orders.line_total') }}</th>
                                <th style="width: 5%; text-align: center;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-sky-50/30 dark:hover:bg-slate-800/40 transition">
                                    <!-- Item Type -->
                                    <td>
                                        <select :name="`items[${index}][item_type]`" x-model="item.item_type" @change="onTypeChange(index)" class="form-select text-xs font-semibold py-1">
                                            <option value="product">📦 {{ app()->getLocale() === 'ar' ? 'منتج تجاري' : 'Finished Product' }}</option>
                                            <option value="raw_material">🧪 {{ app()->getLocale() === 'ar' ? 'مادة خام' : 'Raw Material' }}</option>
                                        </select>
                                    </td>

                                    <!-- Item Selection (Product vs Raw Material) -->
                                    <td>
                                        <!-- Product Selector -->
                                        <div x-show="item.item_type === 'product'">
                                            <select :name="`items[${index}][product_id]`" :required="item.item_type === 'product'" x-model="item.product_id" @change="onProductSelect(index)" class="form-select text-xs w-full">
                                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المنتج التجاري --' : '-- Select Finished Product --' }}</option>
                                                @foreach($products as $prod)
                                                    <option value="{{ $prod->id }}" data-cost="{{ $prod->cost_price ?? $prod->price * 0.5 }}">
                                                        {{ app()->getLocale() === 'ar' && !empty($prod->name_ar) ? $prod->name_ar : $prod->name_en }} ({{ $prod->sku ?? 'PRD-'.$prod->id }}) - Cost: {{ number_format($prod->cost_price ?? 0, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Raw Material Selector -->
                                        <div x-show="item.item_type === 'raw_material'">
                                            <select :name="`items[${index}][raw_material_id]`" :required="item.item_type === 'raw_material'" x-model="item.raw_material_id" class="form-select text-xs w-full">
                                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المادة الخام --' : '-- Select Raw Material --' }}</option>
                                                @foreach($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ app()->getLocale() === 'ar' && !empty($material->name_ar) ? $material->name_ar : ($material->name_en ?? $material->name) }} ({{ $material->code ?? $material->sku ?? 'RM-'.$material->id }}) - {{ $material->uom?->value ?? 'Units' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <input type="text" :name="`items[${index}][notes]`" x-model="item.notes" placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظة خاصة بالبند (اختياري)...' : 'Item note (e.g. batch/grade specs)...' }}" class="form-control text-[11px] mt-1 py-0.5">
                                    </td>

                                    <!-- Ordered Qty -->
                                    <td>
                                        <input type="number" step="0.0001" min="0.0001" :name="`items[${index}][ordered_qty]`" required class="form-control text-xs font-mono font-bold py-1 text-right" x-model.number="item.ordered_qty" @input="calculateTotal()">
                                    </td>

                                    <!-- Unit Price -->
                                    <td>
                                        <div class="relative">
                                            <input type="number" step="0.0001" min="0" :name="`items[${index}][unit_price]`" required class="form-control text-xs font-mono font-bold py-1 text-right pr-10" x-model.number="item.unit_price" @input="calculateTotal()">
                                            <span class="absolute right-2 top-1.5 text-[10px] text-gray-400 font-bold" x-text="currency"></span>
                                        </div>
                                    </td>

                                    <!-- Line Subtotal -->
                                    <td class="font-mono font-bold text-slate-800 dark:text-white text-right">
                                        <span x-text="((Number(item.ordered_qty) || 0) * (Number(item.unit_price) || 0)).toFixed(2)"></span>
                                        <span class="text-[10px] text-gray-400" x-text="currency"></span>
                                    </td>

                                    <!-- Remove Action -->
                                    <td style="text-align: center;">
                                        <button type="button" class="btn btn-ghost btn-xs text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30" @click="removeItem(index)" x-show="items.length > 1">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- 3. Landed Cost & Calculation Summary -->
                <div class="pt-5 border-t border-gray-100 dark:border-gray-800 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="space-y-3 p-4 rounded-xl bg-gray-50/70 dark:bg-slate-800/40 border border-gray-100 dark:border-gray-800">
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-calculator text-sky-600"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'رسوم الوصول والتخليص (Landed Costs)' : 'Landed Costs & Duties' }}</span>
                        </h4>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="form-label font-bold text-[11px] text-slate-600 dark:text-slate-400">{{ __('purchase_orders.shipping_cost') }}</label>
                                <input type="number" step="0.01" min="0" name="freight_cost" value="0.00" class="form-control text-xs font-mono font-bold" x-model.number="freightCost" @input="calculateTotal()">
                            </div>
                            <div>
                                <label class="form-label font-bold text-[11px] text-slate-600 dark:text-slate-400">{{ __('purchase_orders.customs_duty') }}</label>
                                <input type="number" step="0.01" min="0" name="customs_duties" value="0.00" class="form-control text-xs font-mono font-bold" x-model.number="customsDuties" @input="calculateTotal()">
                            </div>
                            <div>
                                <label class="form-label font-bold text-[11px] text-slate-600 dark:text-slate-400">{{ __('purchase_orders.tax_amount') }} (VAT)</label>
                                <input type="number" step="0.01" min="0" name="tax_amount" value="0.00" class="form-control text-xs font-mono font-bold" x-model.number="taxAmount" @input="calculateTotal()">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-sky-50/40 dark:bg-slate-800/60 border border-sky-100 dark:border-gray-700 space-y-2 text-end">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ __('purchase_orders.subtotal') }}:</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-white">
                                <span x-text="subtotal.toFixed(2)"></span> <span x-text="currency"></span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ app()->getLocale() === 'ar' ? 'إجمالي رسوم التوصيل والضرائب' : 'Freight, Customs & Taxes' }}:</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-white">
                                <span x-text="(freightCost + customsDuties + taxAmount).toFixed(2)"></span> <span x-text="currency"></span>
                            </span>
                        </div>
                        <div class="pt-2 border-t border-sky-200 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-sm font-black text-slate-800 dark:text-white uppercase">{{ __('purchase_orders.total_amount') }}:</span>
                            <span class="text-lg font-black text-sky-600 font-mono">
                                <span x-text="total.toFixed(2)"></span> <span x-text="currency"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm px-6">
                    <i class="fa-solid fa-save mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ ومتابعة مسودة أمر الشراء' : 'Save & Draft Purchase Order' }}
                </button>
            </div>
        </form>
    </div>

@php
    $initialItems = [];
    if (!empty($selectedQuotation) && $selectedQuotation->items->isNotEmpty()) {
        foreach ($selectedQuotation->items as $qItem) {
            $initialItems[] = [
                'item_type' => $qItem->item_type,
                'product_id' => $qItem->product_id ? (string) $qItem->product_id : '',
                'raw_material_id' => $qItem->raw_material_id ? (string) $qItem->raw_material_id : '',
                'ordered_qty' => (float) $qItem->quantity,
                'unit_price' => (float) $qItem->unit_price,
                'notes' => $qItem->notes ?? '',
            ];
        }
    }
    if (empty($initialItems)) {
        $initialItems[] = [
            'item_type' => 'product',
            'product_id' => '',
            'raw_material_id' => '',
            'ordered_qty' => 50,
            'unit_price' => 25,
            'notes' => '',
        ];
    }

    $suppliersData = $suppliers->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'code' => $s->code ?? 'VND-'.$s->id,
        'phone' => $s->phone ?? '',
        'currency' => $s->currency ?? 'AED',
        'is_expired' => $s->isAuditExpired(),
        'is_expiring' => $s->isAuditExpiringSoon(),
        'audit_date' => $s->audit_valid_until?->format('Y-m-d') ?? '',
    ]);
@endphp

    <script>
        function purchaseOrderForm() {
            return {
                supplierId: '{{ old("supplier_id", $selectedQuotation?->supplier_id ?? "") }}',
                currency: '{{ old("currency", $selectedQuotation?->currency ?? ($baseCurrency ?? "USD")) }}',
                freightCost: {{ (float) old('freight_cost', $selectedQuotation?->estimated_freight ?? 0) }},
                customsDuties: 0,
                taxAmount: {{ (float) old('tax_amount', $selectedQuotation?->tax_amount ?? 0) }},
                subtotal: 0,
                total: 0,
                items: @json($initialItems),
                suppliers: @json($suppliersData),
                getSelectedSupplierInfo() {
                    if (!this.supplierId) return null;
                    return this.suppliers.find(s => s.id == this.supplierId) || null;
                },
                onSupplierChange() {
                    const sup = this.getSelectedSupplierInfo();
                    if (sup && sup.currency) {
                        this.currency = sup.currency;
                    }
                },
                addItem(type = 'product') {
                    this.items.push({
                        item_type: type,
                        product_id: '',
                        raw_material_id: '',
                        ordered_qty: 1,
                        unit_price: 0,
                        notes: ''
                    });
                    this.calculateTotal();
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    }
                },
                onTypeChange(index) {
                    this.items[index].product_id = '';
                    this.items[index].raw_material_id = '';
                    this.items[index].unit_price = 0;
                    this.calculateTotal();
                },
                onProductSelect(index) {
                    const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
                    if (select && select.selectedOptions[0]) {
                        const cost = parseFloat(select.selectedOptions[0].dataset.cost || 0);
                        if (cost > 0 && (!this.items[index].unit_price || this.items[index].unit_price == 0)) {
                            this.items[index].unit_price = cost;
                        }
                    }
                    this.calculateTotal();
                },
                calculateTotal() {
                    let sub = 0;
                    this.items.forEach(i => {
                        sub += (Number(i.ordered_qty) || 0) * (Number(i.unit_price) || 0);
                    });
                    this.subtotal = sub;
                    this.total = sub + (Number(this.freightCost) || 0) + (Number(this.customsDuties) || 0) + (Number(this.taxAmount) || 0);
                },
                init() {
                    this.calculateTotal();
                }
            }
        }
    </script>
</x-layouts.admin>
