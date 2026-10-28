<x-layouts.admin 
    :pageTitle="__('goods_receipts.new_grn')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'استلام شحنة واردة بالرصيف، تدقيق درجات الحرارة والأختام، وتحديث الأرصدة المستودعية والمطابقة' : 'Receiving dock intake, cold-chain temperature verification, warehouse stock credit, and 3-way matching'"
    :breadcrumbs="[
        __('goods_receipts.title') => route('admin.goods-receipts.index'), 
        __('goods_receipts.new_grn') => '#'
    ]"
>
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
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 flex-wrap gap-2">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/50 text-sky-600 flex items-center justify-center text-sm font-bold">1</span>
                        <span>{{ app()->getLocale() === 'ar' ? 'بيانات أمر الشراء والرصيف اللوجستي' : 'Procurement Order & Receiving Dock Logistics' }}</span>
                    </h3>
                    <span class="badge badge-sky text-xs font-mono">DOCK RECEPTION</span>
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
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر أمر الشراء المؤكد --' : '-- Select Confirmed Purchase Order --' }}</option>
                            @foreach($ordersList as $po)
                                <option value="{{ $po->id }}" {{ (old('purchase_order_id', request('po_id')) == $po->id) ? 'selected' : '' }}>
                                    {{ $po->po_number }} - {{ $po->supplier?->name }} ({{ number_format($po->grand_total ?? $po->total_amount ?? 0, 2) }} {{ $po->currency }})
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_order_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ app()->getLocale() === 'ar' ? 'مستودع الاستلام' : 'Destination Warehouse' }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="location_id" required class="form-select text-sm w-full">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ (old('location_id', $selectedPo?->destination_location_id) == $loc->id || $loc->id === 'central_wh') ? 'selected' : '' }}>
                                    {{ $loc->name_en ?? $loc->name ?? $loc->id }}
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
                            {{ app()->getLocale() === 'ar' ? 'شركة الشحن والناقل' : 'Freight Carrier / Logistics' }}
                        </label>
                        <input type="text" name="carrier_name" value="{{ old('carrier_name') }}" class="form-control text-sm w-full" placeholder="e.g. DHL Express, Aramex, Cold-Chain Freight">
                        @error('carrier_name') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs text-slate-700 dark:text-slate-300">
                            {{ app()->getLocale() === 'ar' ? 'رقم البوليصة / التتبع' : 'Tracking / Waybill #' }}
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
                            {{ __('goods_receipts.temperature_reading') }} (°C)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.1" name="temperature_logger_reading" value="{{ old('temperature_logger_reading', 21.0) }}" class="form-control text-sm font-mono" placeholder="e.g. 21.5">
                            <span class="absolute right-3 top-2 text-xs text-gray-400 font-bold">°C</span>
                        </div>
                        @error('temperature_logger_reading') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="temperature_within_range" value="0">
                            <input type="checkbox" name="temperature_within_range" value="1" {{ old('temperature_within_range', '1') == '1' ? 'checked' : '' }} class="form-checkbox text-emerald-600 rounded">
                            <span class="text-xs font-bold text-slate-800 dark:text-white">{{ app()->getLocale() === 'ar' ? 'درجة الحرارة مطابقة لمعايير الصنف' : 'Temperature within validated cold-chain range' }}</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="seal_verified" value="0">
                            <input type="checkbox" name="seal_verified" value="1" {{ old('seal_verified', '1') == '1' ? 'checked' : '' }} class="form-checkbox text-emerald-600 rounded">
                            <span class="text-xs font-bold text-slate-800 dark:text-white">{{ __('goods_receipts.seal_intact') }} ({{ app()->getLocale() === 'ar' ? 'الأختام سليمة وغير متلاعب بها' : 'Seals intact' }})</span>
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
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 flex-wrap gap-2">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center text-sm font-bold">2</span>
                                <span>{{ app()->getLocale() === 'ar' ? 'بنود الشحنة، أرقام التشغيلات، والتواريخ' : 'Inbound Line Items, Lot Numbers & Shelf Expiry' }}</span>
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'تُضاف المنتجات التجارية فوراً إلى المخزون المركزي، وتُوضع المواد الخام الأولية تحت الحجر الصحي للفحص' : 'Commercial products update warehouse stock immediately; compounding raw materials enter QC quarantine' }}
                            </p>
                        </div>

                        <span class="badge badge-emerald text-xs font-mono">
                            {{ $selectedPo->items->count() }} {{ app()->getLocale() === 'ar' ? 'بند للشحن' : 'Lines in PO' }}
                        </span>
                    </div>

                    <div class="space-y-4">
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
                                            <span class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-950/50 text-sky-600 flex items-center justify-center text-xs">
                                                <i class="fa-solid fa-box"></i>
                                            </span>
                                        @else
                                            <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center text-xs">
                                                <i class="fa-solid fa-flask"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <h4 class="font-bold text-slate-800 dark:text-white text-sm">
                                                {{ $poItem->item_name }}
                                            </h4>
                                            <span class="text-xs text-gray-500 font-mono">
                                                SKU: {{ $poItem->item_sku }} | 
                                                {{ __('purchase_orders.quantity') }}: <strong class="text-slate-700 dark:text-slate-300">{{ number_format($ordered, 2) }}</strong> | 
                                                {{ __('purchase_orders.received_qty') }}: <strong class="text-emerald-600">{{ number_format($received, 2) }}</strong> | 
                                                <span class="text-amber-600 font-bold">{{ app()->getLocale() === 'ar' ? 'المتبقي' : 'Pending' }}: {{ number_format($pending, 2) }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    @if($isProduct)
                                        <span class="badge badge-sky text-xs font-bold">
                                            <i class="fa-solid fa-warehouse mr-1"></i> {{ app()->getLocale() === 'ar' ? 'تحديث مخزون تجاري' : 'Direct Stock Central' }}
                                        </span>
                                    @else
                                        <span class="badge badge-purple text-xs font-bold">
                                            <i class="fa-solid fa-lock mr-1"></i> {{ app()->getLocale() === 'ar' ? 'حجر صحي وفحص QC' : 'QC Quarantine Lock' }}
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
                                            {{ app()->getLocale() === 'ar' ? 'كمية مرفوضة' : 'Rejected Qty' }}
                                        </label>
                                        <input type="number" step="0.0001" min="0" name="items[{{ $index }}][rejected_qty]" value="{{ old('items.'.$index.'.rejected_qty', 0) }}" class="form-control text-xs font-mono">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('batches.supplier_lot') }} <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" name="items[{{ $index }}][supplier_lot]" value="{{ old('items.'.$index.'.supplier_lot') }}" required class="form-control text-xs font-mono" placeholder="e.g. LOT-2026-X1">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('batches.manufacture_date') }} <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="date" name="items[{{ $index }}][manufacture_date]" value="{{ old('items.'.$index.'.manufacture_date', date('Y-m-d', strtotime('-1 month'))) }}" required class="form-control text-xs">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ __('batches.expiry_date') }} <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="date" name="items[{{ $index }}][expiry_date]" value="{{ old('items.'.$index.'.expiry_date', date('Y-m-d', strtotime('+2 years'))) }}" required class="form-control text-xs">
                                    </div>

                                    <div>
                                        <label class="form-label font-bold text-[11px] text-slate-700 dark:text-slate-300">
                                            {{ app()->getLocale() === 'ar' ? 'الرف / الموقع بالصالة' : 'Storage Bin / Shelf' }}
                                        </label>
                                        <input type="text" name="items[{{ $index }}][storage_bin]" value="{{ old('items.'.$index.'.storage_bin', 'BIN-A-01') }}" class="form-control text-xs font-mono" placeholder="e.g. RACK-B2">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card p-10 border border-dashed border-gray-300 dark:border-gray-700 text-center text-gray-500 rounded-2xl">
                    <div class="w-14 h-14 rounded-2xl bg-sky-50 dark:bg-sky-950/40 text-sky-600 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 dark:text-white text-base mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الرجاء اختيار أمر شراء معتمد أعلاه' : 'Please select an approved Purchase Order above' }}
                    </h4>
                    <p class="text-xs text-gray-400 max-w-md mx-auto">
                        {{ app()->getLocale() === 'ar' ? 'عند اختيار أمر الشراء، سيتم تحميل جميع بنود المنتجات والمواد الأولية تلقائياً لإدخال التشغيلات والتواريخ.' : 'Once selected, ordered items will populate automatically for dock inspection and lot ingestion.' }}
                    </p>
                </div>
            @endif

            <!-- Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                @if($selectedPo)
                    <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm px-6">
                        <i class="fa-solid fa-check-double mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد استلام الشحنة وتحديث المخزون' : 'Confirm Inbound Receipt & Credit Inventory' }}
                    </button>
                @endif
            </div>
        </form>
    </div>
</x-layouts.admin>
