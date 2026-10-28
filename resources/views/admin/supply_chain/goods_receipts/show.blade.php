<x-layouts.admin 
    :pageTitle="__('goods_receipts.grn_number') . ': ' . $goodsReceipt->grn_number" 
    :pageSubtitle="__('goods_receipts.supplier') . ': ' . ($goodsReceipt->supplier?->name ?? 'Vendor')"
    :breadcrumbs="[
        __('goods_receipts.title') => route('admin.goods-receipts.index'), 
        $goodsReceipt->grn_number => '#'
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.purchase-orders.show', $goodsReceipt->purchaseOrder) }}" class="btn btn-secondary font-bold text-sm shadow-sm">
                <i class="fa-solid fa-file-invoice mr-1 ml-1 text-sky-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'عرض أمر الشراء الأصلي' : 'View Origin PO' }}</span>
            </a>

            <!-- 3-Way Match Supplier Bill Generation -->
            <form method="POST" action="{{ route('admin.goods-receipts.generate-bill', $goodsReceipt) }}">
                @csrf
                <button type="submit" class="btn btn-primary font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'إنشاء فاتورة المورد (مطابقة 3-Way Match)' : 'Generate Supplier Bill (3-Way Match)' }}</span>
                </button>
            </form>
        </div>
    </x-slot>

    <!-- 1. Dock Inspection & Transport Telemetry Card -->
    <div class="card p-6 mb-6 border shadow-sm border-gray-100 dark:border-gray-800 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-base font-black text-slate-800 dark:text-white font-mono">{{ $goodsReceipt->grn_number }}</h4>
                        <span class="badge {{ $goodsReceipt->status->badgeClass() }} text-xs">
                            {{ $goodsReceipt->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">
                        {{ __('goods_receipts.po_number') }}: 
                        <a href="{{ route('admin.purchase-orders.show', $goodsReceipt->purchaseOrder) }}" class="font-mono text-sky-600 hover:underline font-bold">
                            {{ $goodsReceipt->purchaseOrder?->po_number }}
                        </a> |
                        {{ __('goods_receipts.received_date') }}: <span class="font-mono">{{ $goodsReceipt->received_date?->format('Y-m-d') }}</span>
                    </p>
                </div>
            </div>

            <!-- Key Metrics Badges -->
            <div class="flex items-center gap-3 text-xs flex-wrap">
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center min-w-[100px]">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold">{{ __('goods_receipts.received_by') }}</span>
                    <span class="font-bold text-slate-700 dark:text-gray-300">{{ $goodsReceipt->receiver?->name ?? 'Dock Receiver' }}</span>
                </div>

                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center min-w-[100px]">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold">{{ __('goods_receipts.temperature_reading') }}</span>
                    <span class="font-mono font-bold text-slate-700 dark:text-gray-300">
                        {{ $goodsReceipt->temperature_logger_reading !== null ? $goodsReceipt->temperature_logger_reading . ' °C' : ($goodsReceipt->temperature_reading !== null ? $goodsReceipt->temperature_reading . ' °C' : 'N/A') }}
                    </span>
                </div>

                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center min-w-[100px]">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold">{{ __('goods_receipts.seal_intact') }}</span>
                    @if($goodsReceipt->seal_verified || $goodsReceipt->seal_intact)
                        <span class="font-bold text-emerald-600"><i class="fa-solid fa-check"></i> {{ app()->getLocale() === 'ar' ? 'سليم' : 'Intact' }}</span>
                    @else
                        <span class="font-bold text-rose-600"><i class="fa-solid fa-triangle-exclamation"></i> {{ app()->getLocale() === 'ar' ? 'مكسور' : 'Broken' }}</span>
                    @endif
                </div>

                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center min-w-[100px]">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold">{{ app()->getLocale() === 'ar' ? 'المستودع' : 'Warehouse Hub' }}</span>
                    <span class="font-bold text-slate-700 dark:text-gray-300">{{ $goodsReceipt->location?->name_en ?? $goodsReceipt->location_id }}</span>
                </div>
            </div>
        </div>

        <!-- Carrier & Waybill details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-gray-400 block">{{ app()->getLocale() === 'ar' ? 'الناقل وشركة الشحن' : 'Carrier / Forwarder' }}:</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ $goodsReceipt->carrier_name ?? 'Standard Freight' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block">{{ app()->getLocale() === 'ar' ? 'رقم التتبع / البوليصة' : 'Tracking / Waybill #' }}:</span>
                <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $goodsReceipt->tracking_number ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block">{{ __('goods_receipts.container_count') }}:</span>
                <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $goodsReceipt->container_count ?? 1 }} {{ app()->getLocale() === 'ar' ? 'صندوق / طرد' : 'Cartons / Packages' }}</span>
            </div>
        </div>

        @if($goodsReceipt->discrepancy_notes)
            <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-900 dark:text-amber-200">
                <strong><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ __('goods_receipts.discrepancy_notes') }}:</strong> 
                {{ $goodsReceipt->discrepancy_notes }}
            </div>
        @endif
    </div>

    <!-- 2. Ingested Line Items Table (Dual Product & Material) -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
            <h4 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-purple-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'البنود المستلمة، التشغيلات، وحالة المخزون / الفحص' : 'Received Line Items, Lots & Warehouse Status' }}</span>
            </h4>
            <span class="badge badge-purple text-xs font-mono">{{ $goodsReceipt->items->count() }} {{ app()->getLocale() === 'ar' ? 'بند' : 'Items' }}</span>
        </div>

        <div class="table-responsive">
            <table class="table text-xs w-full">
                <thead>
                    <tr class="bg-gray-50/70 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300">
                        <th style="width: 14%;">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Catalog Type' }}</th>
                        <th style="width: 26%;">{{ app()->getLocale() === 'ar' ? 'البند' : 'Item Description' }}</th>
                        <th style="width: 14%;">{{ __('batches.supplier_lot') }}</th>
                        <th style="width: 12%;">{{ __('goods_receipts.received_qty') }}</th>
                        <th style="width: 12%;">{{ __('batches.expiry_date') }}</th>
                        <th style="width: 10%;">{{ app()->getLocale() === 'ar' ? 'الرف' : 'Bin' }}</th>
                        <th style="width: 12%; text-align: center;">{{ app()->getLocale() === 'ar' ? 'حالة التخزين / QC' : 'Stock Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($goodsReceipt->items as $item)
                        @php
                            $isProduct = $item->item_type === 'product';
                            $batch = $item->batch;
                        @endphp
                        <tr class="hover:bg-purple-50/20 dark:hover:bg-slate-800/30 transition">
                            <td>
                                @if($isProduct)
                                    <span class="badge badge-sky text-[11px] font-bold">
                                        <i class="fa-solid fa-box mr-1"></i> Product
                                    </span>
                                @else
                                    <span class="badge badge-emerald text-[11px] font-bold">
                                        <i class="fa-solid fa-flask mr-1"></i> Raw Material
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ $item->item_name }}
                                </div>
                                <span class="font-mono text-[11px] text-gray-500">SKU: {{ $item->item_sku }}</span>
                            </td>
                            <td class="font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $item->supplier_lot }}
                            </td>
                            <td class="font-mono font-bold text-emerald-600">
                                {{ number_format($item->received_qty, 2) }}
                                @if($item->rejected_qty > 0)
                                    <span class="text-rose-500 text-[10px] block">({{ number_format($item->rejected_qty, 2) }} rejected)</span>
                                @endif
                            </td>
                            <td class="font-mono">
                                {{ $item->expiry_date?->format('Y-m-d') ?? 'N/A' }}
                            </td>
                            <td class="font-mono text-gray-600 dark:text-gray-400">
                                {{ $item->storage_bin ?? 'A-01' }}
                            </td>
                            <td style="text-align: center;">
                                @if($isProduct)
                                    <span class="badge badge-emerald text-[11px] font-bold">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Stock Credited
                                    </span>
                                @else
                                    @if($batch)
                                        <a href="{{ route('admin.batches.show', $batch) }}" class="badge {{ $batch->status->badgeClass() }} text-[11px] hover:opacity-80">
                                            {{ $batch->batch_number }}
                                        </a>
                                    @else
                                        <span class="badge badge-purple text-[11px]">Quarantine</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
