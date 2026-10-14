<x-layouts.admin 
    :pageTitle="__('goods_receipts.new_grn')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'استلام شحنة واردة بالرصيف، مطابقة أمر الشراء، تدقيق درجات الحرارة والأختام، وإيداع المواد بالحجر الصحي' : 'Inbound Dock Ingestion, Temperature & Container Inspection, Automatic Quarantine Allocation'"
    :breadcrumbs="[__('goods_receipts.title') => route('admin.goods-receipts.index'), __('goods_receipts.new_grn') => '#']"
>
    <div class="max-w-5xl mx-auto">
        <form method="POST" action="{{ route('admin.goods-receipts.store') }}" class="space-y-6">
            @csrf

            <!-- Header & Inbound Inspection -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-truck-ramp-box text-sky-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'بيانات الاستلام بالرصيف ومعايير الحماية' : 'Receiving Dock Logistics & Integrity Checks' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('goods_receipts.po_number') }} <span class="text-rose-500">*</span></label>
                        <select name="purchase_order_id" required class="form-select text-sm" onchange="window.location.href = '{{ route('admin.goods-receipts.create') }}?po_id=' + this.value">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر أمر الشراء المعتمد --' : '-- Select Approved PO --' }}</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}" {{ (old('purchase_order_id', request('po_id')) == $po->id) ? 'selected' : '' }}>
                                    {{ $po->po_number }} - {{ $po->supplier?->name }} ({{ number_format($po->total_amount, 2) }} {{ $po->currency }})
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_order_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('goods_receipts.received_date') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" required class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('goods_receipts.container_count') }} <span class="text-rose-500">*</span></label>
                        <input type="number" name="container_count" value="{{ old('container_count', 1) }}" min="1" required class="form-control text-sm font-mono">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('goods_receipts.temperature_reading') }} (°C)</label>
                        <input type="number" step="0.1" name="temperature_reading" value="{{ old('temperature_reading', 21.5) }}" class="form-control text-sm font-mono" placeholder="e.g. 21.5">
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="seal_intact" value="1" {{ old('seal_intact', '1') ? 'checked' : '' }} class="form-checkbox text-emerald-600 rounded">
                            <span class="font-bold text-xs text-slate-800 dark:text-white">{{ __('goods_receipts.seal_intact') }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label font-bold text-xs">{{ __('goods_receipts.discrepancy_notes') }}</label>
                    <textarea name="discrepancy_notes" rows="2" class="form-control text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'سجل أي ملاحظات أو تلف خارجي في الكراتين أو اختلاف في الأختام...' : 'Note any packaging damage, seal tampering, or quantity variances observed...' }}">{{ old('discrepancy_notes') }}</textarea>
                </div>
            </div>

            <!-- Items to Ingest into Quarantine -->
            @if($selectedPo)
                <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-boxes-packing text-emerald-600"></i>
                            {{ app()->getLocale() === 'ar' ? 'بنود الشحنة والتشغيلات المخصصة للحجر الصحي' : 'Inbound Lots for Quarantine Deposit' }}
                        </span>
                        <span class="badge badge-purple text-xs">
                            <i class="fa-solid fa-lock mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تُحجز إجبارياً حتى اجتياز فحص الجودة' : 'Strict Quarantine Lock Enforced' }}
                        </span>
                    </h3>

                    <div class="space-y-4">
                        @foreach($selectedPo->items as $index => $poItem)
                            @php
                                $remaining = max(0, $poItem->quantity_ordered - $poItem->quantity_received);
                            @endphp
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-slate-800/40 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-white text-sm">
                                            {{ $poItem->rawMaterial?->name_en }} ({{ $poItem->rawMaterial?->sku }})
                                        </h4>
                                        <span class="text-xs text-gray-500">
                                            {{ __('purchase_orders.quantity') }}: {{ number_format($poItem->quantity_ordered, 2) }} {{ $poItem->rawMaterial?->uom?->value }} |
                                            {{ __('purchase_orders.received_qty') }}: {{ number_format($poItem->quantity_received, 2) }} |
                                            <span class="font-bold text-amber-600">{{ app()->getLocale() === 'ar' ? 'المتبقي' : 'Pending' }}: {{ number_format($remaining, 2) }}</span>
                                        </span>
                                    </div>
                                    <span class="badge badge-indigo text-xs">
                                        {{ $poItem->rawMaterial?->category?->label() }}
                                    </span>
                                </div>

                                <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $poItem->id }}">
                                <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $poItem->raw_material_id }}">

                                <div class="grid grid-cols-1 md:grid-cols-5 gap-3 pt-2 border-t border-gray-200 dark:border-gray-700 text-xs">
                                    <div>
                                        <label class="form-label font-bold text-[11px]">{{ __('goods_receipts.quantity_received') }} <span class="text-rose-500">*</span></label>
                                        <input type="number" step="0.0001" name="items[{{ $index }}][quantity_received]" value="{{ old('items.'.$index.'.quantity_received', $remaining) }}" required class="form-control text-xs font-mono">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px]">{{ __('batches.supplier_lot') }} <span class="text-rose-500">*</span></label>
                                        <input type="text" name="items[{{ $index }}][supplier_lot]" value="{{ old('items.'.$index.'.supplier_lot') }}" required class="form-control text-xs font-mono" placeholder="Vendor Lot #">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px]">{{ __('batches.manufacture_date') }}</label>
                                        <input type="date" name="items[{{ $index }}][manufacture_date]" value="{{ old('items.'.$index.'.manufacture_date') }}" class="form-control text-xs">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px]">{{ __('batches.expiry_date') }} <span class="text-rose-500">*</span></label>
                                        <input type="date" name="items[{{ $index }}][expiry_date]" value="{{ old('items.'.$index.'.expiry_date') }}" required class="form-control text-xs">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px]">{{ __('batches.location') }} <span class="text-rose-500">*</span></label>
                                        <select name="items[{{ $index }}][location_id]" required class="form-select text-xs font-mono">
                                            @foreach($locations as $loc)
                                                <option value="{{ $loc->id }}">{{ $loc->name ?? $loc->id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card p-8 border border-dashed border-gray-300 dark:border-gray-700 text-center text-gray-500">
                    <i class="fa-solid fa-arrow-up text-3xl mb-2 text-sky-500"></i>
                    <p class="font-bold">{{ app()->getLocale() === 'ar' ? 'الرجاء اختيار أمر شراء معتمد لعرض بنود المواد المتاحة للاستلام.' : 'Please select an approved Purchase Order above to load line items.' }}</p>
                </div>
            @endif

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                @if($selectedPo)
                    <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm">
                        <i class="fa-solid fa-check-double mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد الاستلام وإيداع التشغيلات في الحجر' : 'Confirm Dock Receipt & Quarantine' }}
                    </button>
                @endif
            </div>
        </form>
    </div>
</x-layouts.admin>
