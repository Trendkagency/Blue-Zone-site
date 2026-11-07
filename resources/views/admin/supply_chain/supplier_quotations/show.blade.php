<x-layouts.admin 
    :pageTitle="__('supplier_quotations.quote_number') . ': ' . $supplierQuotation->quote_number" 
    :pageSubtitle="__('supplier_quotations.subtitle')"
    :breadcrumbs="[__('supplier_quotations.title') => route('admin.supplier-quotations.index'), $supplierQuotation->quote_number => '#']"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <!-- Print Official RFQ / Quote -->
            <a href="{{ route('admin.supplier-quotations.print', $supplierQuotation) }}" target="_blank" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>{{ __('supplier_quotations.actions.print') }}</span>
            </a>

            @if($supplierQuotation->status !== 'converted')
                <!-- Edit Draft / Pending Quote -->
                <a href="{{ route('admin.supplier-quotations.edit', $supplierQuotation) }}" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>{{ __('supplier_quotations.edit_quote') }}</span>
                </a>

                <!-- 1-Click Convert to Purchase Order -->
                <form method="POST" action="{{ route('admin.supplier-quotations.convert', $supplierQuotation) }}" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من تحويل عرض السعر هذا إلى أمر شراء رسمي؟' : 'Convert this supplier quotation directly into an official Purchase Order?' }}')">
                    @csrf
                    <button type="submit" class="btn btn-primary text-xs font-bold flex items-center gap-1.5 shadow-sm px-4 cursor-pointer">
                        <i class="fa-solid fa-cart-plus"></i>
                        <span>{{ __('supplier_quotations.actions.convert_to_po') }}</span>
                    </button>
                </form>
            @else
                <!-- View Linked PO -->
                @if($supplierQuotation->convertedPurchaseOrder)
                    <a href="{{ route('admin.purchase-orders.show', $supplierQuotation->convertedPurchaseOrder) }}" class="btn btn-success text-xs font-bold flex items-center gap-1.5 shadow-sm px-4">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>{{ __('supplier_quotations.actions.view_converted_po') }} ({{ $supplierQuotation->convertedPurchaseOrder->po_number }})</span>
                    </a>
                @endif
            @endif
        </div>
    </x-slot>

    <!-- Converted Alert Banner -->
    @if($supplierQuotation->status === 'converted' && $supplierQuotation->convertedPurchaseOrder)
        <div class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-between gap-4 mb-6 text-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500 text-white flex items-center justify-center text-lg shadow-sm">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-purple-900 dark:text-purple-200">
                        {{ app()->getLocale() === 'ar' ? 'تم تحويل عرض السعر هذا بنجاح إلى أمر شراء رسمي' : 'Quotation Converted to Official Purchase Order' }}
                    </h4>
                    <p class="text-purple-700 dark:text-purple-300">
                        {{ app()->getLocale() === 'ar' ? 'أمر الشراء المرتبط:' : 'Linked Purchase Order:' }}
                        <strong class="font-mono">{{ $supplierQuotation->convertedPurchaseOrder->po_number }}</strong>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.purchase-orders.show', $supplierQuotation->convertedPurchaseOrder) }}" class="btn btn-sm btn-primary">
                {{ app()->getLocale() === 'ar' ? 'فتح أمر الشراء' : 'Open Purchase Order' }}
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Quotation Details & Items Table -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quotation Header Info Card -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider block font-bold">{{ __('supplier_quotations.quote_number') }}</span>
                        <h2 class="text-xl font-black font-mono text-slate-900 dark:text-white mt-0.5">{{ $supplierQuotation->quote_number }}</h2>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $supplierQuotation->statusBadgeClass() }}">
                        {{ __('supplier_quotations.statuses.' . $supplierQuotation->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-gray-500 block mb-0.5">{{ __('supplier_quotations.quote_date') }}</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ $supplierQuotation->quote_date?->format('Y-m-d') }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-0.5">{{ __('supplier_quotations.valid_until') }}</span>
                        <strong class="{{ $supplierQuotation->valid_until && $supplierQuotation->valid_until->isPast() && $supplierQuotation->status !== 'converted' ? 'text-rose-500' : 'text-slate-800 dark:text-slate-200' }}">
                            {{ $supplierQuotation->valid_until?->format('Y-m-d') ?? '--' }}
                        </strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-0.5">{{ __('supplier_quotations.lead_time_days') }}</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ $supplierQuotation->lead_time_days }} {{ app()->getLocale() === 'ar' ? 'أيام' : 'Days' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-0.5">{{ __('supplier_quotations.currency') }}</span>
                        <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $supplierQuotation->currency }} (Rate: {{ $supplierQuotation->exchange_rate }})</strong>
                    </div>
                </div>

                @if($supplierQuotation->rfq_reference || $supplierQuotation->payment_terms)
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        @if($supplierQuotation->rfq_reference)
                            <div>
                                <span class="text-gray-500 block mb-0.5">{{ __('supplier_quotations.rfq_reference') }}:</span>
                                <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $supplierQuotation->rfq_reference }}</strong>
                            </div>
                        @endif
                        @if($supplierQuotation->payment_terms)
                            <div>
                                <span class="text-gray-500 block mb-0.5">{{ __('supplier_quotations.payment_terms') }}:</span>
                                <strong class="text-slate-800 dark:text-slate-200">{{ $supplierQuotation->payment_terms }}</strong>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Itemized Line Items Table -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-purple-600"></i>
                        <span>{{ __('supplier_quotations.line_items') }}</span>
                    </h3>
                    <span class="text-xs font-mono text-gray-500 font-bold">
                        {{ $supplierQuotation->items->count() }} {{ app()->getLocale() === 'ar' ? 'بنود' : 'Items' }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table-modern text-xs">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المادة / البند' : 'Material / Item' }}</th>
                                <th class="text-end">{{ __('supplier_quotations.quantity') }}</th>
                                <th class="text-end">{{ __('supplier_quotations.unit_price') }}</th>
                                <th class="text-end">{{ __('supplier_quotations.tax_rate') }}</th>
                                <th class="text-end">{{ __('supplier_quotations.total_price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($supplierQuotation->items as $idx => $item)
                                <tr>
                                    <td class="font-mono text-gray-400">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="font-bold text-slate-800 dark:text-slate-200">
                                            {{ $item->item_name }}
                                        </div>
                                        @if($item->rawMaterial)
                                            <span class="text-[10px] font-mono text-sky-600 dark:text-sky-400 block">
                                                Code: {{ $item->rawMaterial->material_code }}
                                            </span>
                                        @elseif($item->product)
                                            <span class="text-[10px] font-mono text-purple-600 dark:text-purple-400 block">
                                                SKU: {{ $item->product->sku }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end font-mono font-bold">
                                        {{ number_format($item->quantity, 2) }}
                                        <span class="text-[10px] text-gray-500">{{ $item->rawMaterial?->unit_of_measure ?? 'Units' }}</span>
                                    </td>
                                    <td class="text-end font-mono">
                                        {{ number_format($item->unit_price, 4) }} {{ $supplierQuotation->currency }}
                                    </td>
                                    <td class="text-end font-mono text-gray-500">
                                        {{ $item->tax_rate }}%
                                    </td>
                                    <td class="text-end font-mono font-bold text-slate-900 dark:text-white">
                                        {{ number_format($item->total_price, 2) }} {{ $supplierQuotation->currency }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($supplierQuotation->notes)
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 text-xs">
                        <span class="text-gray-500 block mb-1 font-bold">{{ __('supplier_quotations.notes') }}:</span>
                        <p class="text-slate-700 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-900/40 p-3 rounded-xl border border-gray-200 dark:border-gray-800 font-sans">
                            {{ $supplierQuotation->notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Vendor Profile Summary & Financial Ledger Breakdown -->
        <div class="space-y-6">
            <!-- Vendor Profile Summary Card -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-building text-sky-600"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'ملف الشريك المورد' : 'Supplier Profile' }}</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-500 block">{{ __('suppliers.title') }}:</span>
                        <a href="{{ route('admin.suppliers.show', $supplierQuotation->supplier) }}" class="font-bold text-sm text-sky-600 dark:text-sky-400 hover:underline">
                            {{ app()->getLocale() === 'ar' ? ($supplierQuotation->supplier?->name_ar ?? $supplierQuotation->supplier?->name_en) : $supplierQuotation->supplier?->name_en }}
                        </a>
                        <span class="text-[10px] font-mono text-gray-500 block">{{ $supplierQuotation->supplier?->code }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 block">{{ __('suppliers.contact_name') }}:</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ $supplierQuotation->supplier?->contact_name ?? '--' }}</strong>
                    </div>

                    <div>
                        <span class="text-gray-500 block">{{ __('suppliers.email') }}:</span>
                        <a href="mailto:{{ $supplierQuotation->supplier?->email }}" class="text-sky-600 dark:text-sky-400 hover:underline">
                            {{ $supplierQuotation->supplier?->email }}
                        </a>
                    </div>

                    <div>
                        <span class="text-gray-500 block">{{ __('suppliers.country') }}:</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ $supplierQuotation->supplier?->country?->name_en ?? 'International' }}</strong>
                    </div>

                    <div class="pt-2 border-t border-gray-100 dark:border-gray-800 flex items-center gap-2">
                        <span class="badge {{ $supplierQuotation->supplier?->is_active ? 'badge-success' : 'badge-warning' }} text-[10px]">
                            {{ $supplierQuotation->supplier?->is_active ? __('suppliers.active') : __('suppliers.under_review') }}
                        </span>
                        @if($supplierQuotation->supplier?->gmp_certified)
                            <span class="badge badge-info text-[10px]">cGMP Qualified</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Financial Totals Box -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3 bg-slate-50/50 dark:bg-slate-900/30 text-xs">
                <h4 class="font-extrabold text-sm text-slate-800 dark:text-white border-b border-gray-200 dark:border-gray-800 pb-2">
                    {{ app()->getLocale() === 'ar' ? 'التسوية المالية الشاملة' : 'Commercial Financial Totals' }}
                </h4>

                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>{{ __('supplier_quotations.subtotal') }}:</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white">
                        {{ number_format($supplierQuotation->subtotal, 2) }} {{ $supplierQuotation->currency }}
                    </span>
                </div>

                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>{{ __('supplier_quotations.tax_amount') }} (VAT):</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white">
                        {{ number_format($supplierQuotation->tax_amount, 2) }} {{ $supplierQuotation->currency }}
                    </span>
                </div>

                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>{{ __('supplier_quotations.freight_estimate') }}:</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white">
                        {{ number_format($supplierQuotation->estimated_freight, 2) }} {{ $supplierQuotation->currency }}
                    </span>
                </div>

                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>{{ __('supplier_quotations.customs_estimate') }}:</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white">
                        {{ number_format($supplierQuotation->estimated_customs, 2) }} {{ $supplierQuotation->currency }}
                    </span>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700 font-black text-sm text-slate-900 dark:text-white">
                    <span>{{ __('supplier_quotations.grand_total') }}:</span>
                    <span class="text-base text-primary font-mono">
                        {{ number_format($supplierQuotation->grand_total, 2) }} {{ $supplierQuotation->currency }}
                    </span>
                </div>
            </div>

            <!-- Status Quick Controls (if not converted) -->
            @if($supplierQuotation->status !== 'converted')
                <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
                    <h4 class="font-bold text-xs text-slate-700 dark:text-slate-300">
                        {{ app()->getLocale() === 'ar' ? 'تحديث مرحلة العرض' : 'Update Quotation Status' }}
                    </h4>
                    <div class="grid grid-cols-2 gap-2">
                        <form method="POST" action="{{ route('admin.supplier-quotations.update-status', $supplierQuotation) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-sm btn-outline-success w-full font-bold text-xs">
                                <i class="fa-solid fa-check mr-1 ml-1"></i> {{ __('supplier_quotations.actions.mark_accepted') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.supplier-quotations.update-status', $supplierQuotation) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-outline-danger w-full font-bold text-xs">
                                <i class="fa-solid fa-xmark mr-1 ml-1"></i> {{ __('supplier_quotations.actions.mark_rejected') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
