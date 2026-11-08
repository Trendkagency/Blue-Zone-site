<x-layouts.admin 
    :pageTitle="__('goods_receipts.new_grn')" 
    :pageSubtitle="__('goods_receipts.subtitle')"
    :breadcrumbs="[
        __('goods_receipts.title') => route('admin.goods-receipts.index'), 
        __('goods_receipts.new_grn') => '#'
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-file-invoice text-indigo-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'أوامر الشراء' : 'Purchase Orders' }}</span>
        </a>
        <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'العودة لسجل الاستلام' : 'Back to Receipts' }}</span>
        </a>
    </x-slot>
    <div class="max-w-6xl mx-auto space-y-6">
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm">
                <div class="font-bold flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ app()->getLocale() === 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.goods-receipts.store') }}" class="space-y-6">
            @csrf

            <!-- 1. PO Selector & Dock Receiving Header -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3.5 flex-wrap gap-2">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">1</span>
                        <span>{{ __('goods_receipts.dock_reception') }}</span>
                    </h3>
                    <span class="badge badge-sky text-xs font-mono font-bold tracking-wider">{{ __('goods_receipts.dock_reception_badge') }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.po_number') }} <span class="text-rose-500">*</span>
                        </label>
                        @php
                            $ordersList = $openOrders ?? $purchaseOrders ?? collect();
                        @endphp
                        <select name="purchase_order_id" required class="form-select text-sm w-full" onchange="window.location.href = '{{ route('admin.goods-receipts.create') }}?po_id=' + this.value">
                            <option value="">{{ __('goods_receipts.select_po') }}</option>
                            @foreach($ordersList as $po)
                                <option value="{{ $po->id }}" {{ (old('purchase_order_id', request('po_id', $selectedPo?->id)) == $po->id) ? 'selected' : '' }}>
                                    {{ $po->po_number }} - {{ $po->supplier?->name }} ({{ number_format($po->grand_total ?? $po->total_amount ?? 0, 2) }} {{ $po->currency }})
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_order_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.destination_warehouse') }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="location_id" required class="form-select text-sm w-full">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ (old('location_id', $selectedPo?->destination_location_id) == $loc->id || $loc->id === 'central_wh') ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' ? ($loc->name_ar ?? $loc->name_en ?? $loc->name) : ($loc->name_en ?? $loc->name ?? $loc->id) }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.received_date') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" required class="form-control text-sm w-full">
                        @error('received_date') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.carrier_name') }}
                        </label>
                        <input type="text" name="carrier_name" value="{{ old('carrier_name') }}" class="form-control text-sm w-full" placeholder="e.g. DHL Express, Aramex, Cold-Chain Logistics">
                        @error('carrier_name') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.tracking_number') }}
                        </label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number') }}" class="form-control text-sm font-mono w-full" placeholder="e.g. AWB-90812938">
                        @error('tracking_number') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.container_count') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="container_count" value="{{ old('container_count', 1) }}" min="1" required class="form-control text-sm font-mono w-full">
                        @error('container_count') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Integrity, Temp & Seals Verification Bar -->
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.temperature_logger_reading') }}
                        </label>
                        <div class="relative">
                            <input type="number" step="0.1" name="temperature_logger_reading" value="{{ old('temperature_logger_reading', 21.0) }}" class="form-control text-sm font-mono" placeholder="e.g. 21.5">
                            <span class="absolute right-3 rtl:left-3 rtl:right-auto top-2 text-xs text-gray-400 font-bold">°C</span>
                        </div>
                        @error('temperature_logger_reading') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="temperature_within_range" value="0">
                            <input type="checkbox" name="temperature_within_range" value="1" {{ old('temperature_within_range', '1') == '1' ? 'checked' : '' }} class="form-checkbox text-emerald-600 rounded">
                            <span class="text-xs font-bold text-slate-800 dark:text-white">{{ __('goods_receipts.temperature_within_range') }}</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="seal_verified" value="0">
                            <input type="checkbox" name="seal_verified" value="1" {{ old('seal_verified', '1') == '1' ? 'checked' : '' }} class="form-checkbox text-emerald-600 rounded">
                            <span class="text-xs font-bold text-slate-800 dark:text-white">{{ __('goods_receipts.seal_intact') }} ({{ __('goods_receipts.seal_verified') }})</span>
                        </label>
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.discrepancy_notes') }}
                        </label>
                        <input type="text" name="discrepancy_notes" value="{{ old('discrepancy_notes') }}" class="form-control text-xs" placeholder="{{ app()->getLocale() === 'ar' ? 'سجل أي تلف خارجي أو فروقات كمية...' : 'Note outer packaging condition, variances...' }}">
                    </div>
                </div>
            </div>

            <!-- 2. Line Items Reception & Lot / Expiry Intake -->
            @if($selectedPo)
                <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3.5 flex-wrap gap-2">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold">2</span>
                                <span>{{ __('goods_receipts.line_items_intake') }}</span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('goods_receipts.line_items_desc') }}
                            </p>
                        </div>

                        <span class="badge badge-emerald text-xs font-mono font-bold">
                            {{ $selectedPo->items->count() }} {{ app()->getLocale() === 'ar' ? 'بند للشحن' : 'Lines in PO' }}
                        </span>
                    </div>

                    <!-- Quarantine protocol notice -->
                    <div class="p-3.5 rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 text-xs text-purple-900 dark:text-purple-200 flex items-center gap-2.5">
                        <i class="fa-solid fa-shield-halved text-purple-600 dark:text-purple-400 text-base flex-shrink-0"></i>
                        <span>{{ __('goods_receipts.quarantine_notice') }}</span>
                    </div>

                    <div class="space-y-4 pt-2">
                        @foreach($selectedPo->items as $index => $poItem)
                            @php
                                $ordered = (float) $poItem->ordered_qty;
                                $received = (float) $poItem->received_qty;
                                $pending = max(0.0, $ordered - $received);
                                $isProduct = $poItem->item_type === 'product';
                            @endphp

                            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-slate-800/40 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2.5">
                                        @if($isProduct)
                                            <span class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm">
                                                <i class="fa-solid fa-box"></i>
                                            </span>
                                        @else
                                            <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm">
                                                <i class="fa-solid fa-flask"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <h4 class="font-bold text-slate-800 dark:text-white text-sm">
                                                {{ $poItem->item_name }}
                                            </h4>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                                SKU: {{ $poItem->item_sku }} | 
                                                {{ __('purchase_orders.quantity') }}: <strong class="text-slate-700 dark:text-slate-300">{{ number_format($ordered, 2) }}</strong> | 
                                                {{ __('purchase_orders.received_qty') }}: <strong class="text-emerald-600 dark:text-emerald-400">{{ number_format($received, 2) }}</strong> | 
                                                <span class="text-amber-600 dark:text-amber-400 font-bold">{{ __('goods_receipts.pending') }}: {{ number_format($pending, 2) }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    @if($isProduct)
                                        <span class="badge badge-sky text-xs font-bold inline-flex items-center gap-1">
                                            <i class="fa-solid fa-warehouse"></i>
                                            <span>{{ __('goods_receipts.direct_stock_update') }}</span>
                                        </span>
                                    @else
                                        <span class="badge badge-purple text-xs font-bold inline-flex items-center gap-1">
                                            <i class="fa-solid fa-lock"></i>
                                            <span>{{ __('goods_receipts.qc_quarantine_lock') }}</span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Hidden Identifiers -->
                                <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $poItem->id }}">
                                <input type="hidden" name="items[{{ $index }}][item_type]" value="{{ $poItem->item_type ?? ($isProduct ? 'product' : 'raw_material') }}">
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $poItem->product_id }}">
                                <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $poItem->raw_material_id }}">

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 pt-3 border-t border-gray-200 dark:border-gray-700 text-xs">
                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('goods_receipts.received_qty') }} <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="number" step="0.0001" min="0.0001" name="items[{{ $index }}][received_qty]" value="{{ old('items.'.$index.'.received_qty', $pending > 0 ? $pending : 1) }}" required class="form-control text-xs font-mono font-bold">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('goods_receipts.rejected_qty') }}
                                        </label>
                                        <input type="number" step="0.0001" min="0" name="items[{{ $index }}][rejected_qty]" value="{{ old('items.'.$index.'.rejected_qty', 0) }}" class="form-control text-xs font-mono">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('goods_receipts.supplier_lot') }} <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" name="items[{{ $index }}][supplier_lot]" value="{{ old('items.'.$index.'.supplier_lot') }}" required class="form-control text-xs font-mono" placeholder="e.g. LOT-2026-X1">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('goods_receipts.manufacture_date') }} <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="date" name="items[{{ $index }}][manufacture_date]" value="{{ old('items.'.$index.'.manufacture_date', date('Y-m-d', strtotime('-1 month'))) }}" required class="form-control text-xs">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('goods_receipts.expiry_date') }} <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="date" name="items[{{ $index }}][expiry_date]" value="{{ old('items.'.$index.'.expiry_date', date('Y-m-d', strtotime('+2 years'))) }}" required class="form-control text-xs">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('goods_receipts.storage_bin') }}
                                        </label>
                                        <input type="text" name="items[{{ $index }}][storage_bin]" value="{{ old('items.'.$index.'.storage_bin', 'BIN-A-01') }}" class="form-control text-xs font-mono" placeholder="e.g. RACK-B2">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card p-12 border border-dashed border-gray-300 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400 rounded-2xl">
                    <div class="w-16 h-16 rounded-2xl bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-3xl mx-auto mb-3.5 shadow-inner">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 dark:text-white text-base mb-1.5">
                        {{ __('goods_receipts.select_po_first') }}
                    </h4>
                    <p class="text-xs text-gray-400 max-w-md mx-auto leading-relaxed">
                        {{ __('goods_receipts.select_po_first_desc') }}
                    </p>
                </div>
            @endif

            <!-- Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-secondary text-sm">
                    {{ __('goods_receipts.cancel') }}
                </a>
                @if($selectedPo)
                    <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm px-6 inline-flex items-center gap-2">
                        <i class="fa-solid fa-check-double"></i>
                        <span>{{ __('goods_receipts.confirm_receipt') }}</span>
                    </button>
                @endif
            </div>
        </form>
    </div>
</x-layouts.admin>
