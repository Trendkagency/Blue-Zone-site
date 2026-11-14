<x-layouts.admin 
    :pageTitle="__('goods_receipts.edit_receipt') . ': ' . $goodsReceipt->grn_number" 
    :pageSubtitle="__('goods_receipts.edit_subtitle')"
    :breadcrumbs="[
        __('goods_receipts.title') => route('admin.goods-receipts.index'), 
        $goodsReceipt->grn_number => route('admin.goods-receipts.show', $goodsReceipt),
        __('admin.edit') => '#'
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.goods-receipts.show', $goodsReceipt) }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'عرض الإشعار' : 'View Receipt' }}</span>
        </a>
        <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-list text-gray-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'سجل الاستلام' : 'Receipts List' }}</span>
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

        @if(!empty($existingInvoice))
            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-file-invoice-dollar text-amber-600 dark:text-amber-400 text-lg flex-shrink-0"></i>
                    <div>
                        <strong class="font-bold block">{{ __('goods_receipts.bill_exists_warning') }}</strong>
                        <span class="text-amber-700 dark:text-amber-300">
                            {{ app()->getLocale() === 'ar' ? 'فاتورة المورد المرتبطة:' : 'Linked Supplier Bill:' }} 
                            <span class="font-mono font-bold">{{ $existingInvoice->invoice_number }}</span>
                        </span>
                    </div>
                </div>
                <a href="{{ route('admin.finance.purchase-invoices.show', $existingInvoice) }}" class="btn btn-secondary btn-xs font-bold bg-white dark:bg-slate-900 shadow-sm text-amber-800 dark:text-amber-300 inline-flex items-center gap-1">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'عرض الفاتورة' : 'View Bill' }}</span>
                </a>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.goods-receipts.update', $goodsReceipt) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Origin PO Context & Dock Telemetry Card -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3.5 flex-wrap gap-2">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">1</span>
                        <span>{{ __('goods_receipts.dock_reception') }}</span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="badge {{ $goodsReceipt->status->badgeClass() }} text-xs font-mono font-bold">
                            {{ $goodsReceipt->status->label() }}
                        </span>
                        <span class="font-mono text-xs text-gray-500 font-bold bg-gray-100 dark:bg-slate-800 px-2 py-1 rounded">
                            {{ $goodsReceipt->grn_number }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- PO Info (Read-only reference) -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.po_number') }}
                        </label>
                        <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-slate-800/60 border border-gray-200 dark:border-gray-700 text-xs flex items-center justify-between">
                            <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $goodsReceipt->purchaseOrder?->po_number ?? 'N/A' }}
                            </span>
                            <span class="text-gray-500 dark:text-gray-400 truncate max-w-[150px]">
                                {{ $goodsReceipt->supplier?->name ?? 'Vendor' }}
                            </span>
                        </div>
                    </div>

                    <!-- Destination Warehouse -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.destination_warehouse') }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="location_id" required class="form-select text-sm w-full">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id', $goodsReceipt->location_id) == $loc->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' ? ($loc->name_ar ?? $loc->name_en ?? $loc->name) : ($loc->name_en ?? $loc->name ?? $loc->id) }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Receipt Date -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.received_date') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="received_date" value="{{ old('received_date', $goodsReceipt->received_date?->format('Y-m-d')) }}" required class="form-control text-sm w-full">
                        @error('received_date') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Carrier Name -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.carrier_name') }}
                        </label>
                        <input type="text" name="carrier_name" value="{{ old('carrier_name', $goodsReceipt->carrier_name) }}" class="form-control text-sm w-full" placeholder="e.g. DHL Express, Aramex">
                        @error('carrier_name') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tracking Number -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.tracking_number') }}
                        </label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $goodsReceipt->tracking_number) }}" class="form-control text-sm font-mono w-full" placeholder="e.g. AWB-90812938">
                        @error('tracking_number') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Container Count -->
                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.container_count') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="container_count" value="{{ old('container_count', $goodsReceipt->container_count ?? 1) }}" min="1" required class="form-control text-sm font-mono w-full">
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
                            <input type="number" step="0.1" name="temperature_logger_reading" value="{{ old('temperature_logger_reading', $goodsReceipt->temperature_logger_reading) }}" class="form-control text-sm font-mono" placeholder="e.g. 21.5">
                            <span class="absolute right-3 rtl:left-3 rtl:right-auto top-2 text-xs text-gray-400 font-bold">°C</span>
                        </div>
                        @error('temperature_logger_reading') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="temperature_within_range" value="0">
                            <input type="checkbox" name="temperature_within_range" value="1" {{ old('temperature_within_range', $goodsReceipt->temperature_within_range ? '1' : '0') == '1' ? 'checked' : '' }} class="form-checkbox text-emerald-600 rounded">
                            <span class="text-xs font-bold text-slate-800 dark:text-white">{{ __('goods_receipts.temperature_within_range') }}</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="seal_verified" value="0">
                            <input type="checkbox" name="seal_verified" value="1" {{ old('seal_verified', $goodsReceipt->seal_verified ? '1' : '0') == '1' ? 'checked' : '' }} class="form-checkbox text-emerald-600 rounded">
                            <span class="text-xs font-bold text-slate-800 dark:text-white">{{ __('goods_receipts.seal_intact') }} ({{ __('goods_receipts.seal_verified') }})</span>
                        </label>
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ __('goods_receipts.discrepancy_notes') }}
                        </label>
                        <input type="text" name="discrepancy_notes" value="{{ old('discrepancy_notes', $goodsReceipt->discrepancy_notes) }}" class="form-control text-xs" placeholder="{{ app()->getLocale() === 'ar' ? 'سجل أي ملاحظات أو تلف...' : 'Note outer packaging condition, variances...' }}">
                    </div>
                </div>
            </div>

            <!-- 2. Line Items Details, Lots, Quantities & Shelf Expiry -->
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
                        {{ $goodsReceipt->items->count() }} {{ app()->getLocale() === 'ar' ? 'بند مستلم' : 'Received Lines' }}
                    </span>
                </div>

                <div class="space-y-4 pt-2">
                    @foreach($goodsReceipt->items as $index => $item)
                        @php
                            $isProduct = $item->item_type === 'product' || !empty($item->product_id);
                            $poItem = $item->purchaseOrderItem;
                            $ordered = $poItem ? (float) $poItem->ordered_qty : (float) $item->ordered_qty;
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
                                            {{ $item->item_name }}
                                        </h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                            SKU: {{ $item->item_sku }} | 
                                            {{ __('purchase_orders.quantity') }}: <strong class="text-slate-700 dark:text-slate-300">{{ number_format($ordered, 2) }}</strong>
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
                                        <i class="fa-solid fa-flask"></i>
                                        <span>{{ __('goods_receipts.qc_quarantine_lock') }}</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Hidden identifiers for item tracking -->
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->purchase_order_item_id }}">
                            <input type="hidden" name="items[{{ $index }}][item_type]" value="{{ $item->item_type }}">
                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                            <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $item->raw_material_id }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 pt-3 border-t border-gray-200 dark:border-gray-700 text-xs">
                                <div>
                                    <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                        {{ __('goods_receipts.received_qty') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" step="0.0001" min="0.0001" name="items[{{ $index }}][received_qty]" value="{{ old('items.'.$index.'.received_qty', (float)$item->received_qty) }}" required class="form-control text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                </div>

                                <div>
                                    <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                        {{ __('goods_receipts.rejected_qty') }}
                                    </label>
                                    <input type="number" step="0.0001" min="0" name="items[{{ $index }}][rejected_qty]" value="{{ old('items.'.$index.'.rejected_qty', (float)$item->rejected_qty) }}" class="form-control text-xs font-mono">
                                </div>

                                <div>
                                    <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                        {{ __('goods_receipts.supplier_lot') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="items[{{ $index }}][supplier_lot]" value="{{ old('items.'.$index.'.supplier_lot', $item->supplier_lot) }}" required class="form-control text-xs font-mono" placeholder="e.g. LOT-2026-X1">
                                </div>

                                <div>
                                    <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                        {{ __('goods_receipts.manufacture_date') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="items[{{ $index }}][manufacture_date]" value="{{ old('items.'.$index.'.manufacture_date', $item->manufacture_date?->format('Y-m-d')) }}" required class="form-control text-xs">
                                </div>

                                <div>
                                    <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                        {{ __('goods_receipts.expiry_date') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="items[{{ $index }}][expiry_date]" value="{{ old('items.'.$index.'.expiry_date', $item->expiry_date?->format('Y-m-d')) }}" required class="form-control text-xs">
                                </div>

                                <div>
                                    <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                        {{ __('goods_receipts.storage_bin') }}
                                    </label>
                                    <input type="text" name="items[{{ $index }}][storage_bin]" value="{{ old('items.'.$index.'.storage_bin', $item->storage_bin) }}" class="form-control text-xs font-mono" placeholder="e.g. BIN-A-01">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.goods-receipts.show', $goodsReceipt) }}" class="btn btn-secondary text-sm">
                    {{ __('goods_receipts.cancel') }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm px-6 inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>{{ __('goods_receipts.update_grn') }}</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
