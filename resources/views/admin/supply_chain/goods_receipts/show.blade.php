<x-layouts.admin 
    :pageTitle="__('goods_receipts.grn_number') . ': ' . $goodsReceipt->grn_number" 
    :pageSubtitle="__('goods_receipts.supplier') . ': ' . ($goodsReceipt->supplier?->name ?? 'Vendor')"
    :breadcrumbs="[
        __('goods_receipts.title') => route('admin.goods-receipts.index'), 
        $goodsReceipt->grn_number => '#'
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap">
            @if($goodsReceipt->purchaseOrder)
                <a href="{{ route('admin.purchase-orders.show', $goodsReceipt->purchaseOrder) }}" class="btn btn-secondary font-bold text-sm shadow-sm inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice text-sky-600"></i>
                    <span>{{ __('goods_receipts.view_origin_po') }}</span>
                </a>
            @endif

            <!-- 3-Way Match Supplier Bill Status / Action -->
            @if(!empty($existingInvoice))
                <a href="{{ route('admin.finance.purchase-invoices.show', $existingInvoice) }}" class="btn btn-primary font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm inline-flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'عرض فاتورة المورد (3-Way Match)' : 'View Matched Supplier Bill' }}</span>
                    <span class="badge bg-white/20 text-white text-[11px] font-mono">{{ $existingInvoice->invoice_number }}</span>
                </a>
            @else
                <form method="POST" action="{{ route('admin.goods-receipts.generate-bill', $goodsReceipt) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>{{ __('goods_receipts.generate_supplier_bill') }}</span>
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <!-- 1. Dock Inspection & Transport Telemetry Card -->
    <div class="card p-6 mb-6 border shadow-sm border-gray-100 dark:border-gray-800 space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl shadow-inner">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h4 class="text-base font-black text-slate-800 dark:text-white font-mono">{{ $goodsReceipt->grn_number }}</h4>
                        <span class="badge {{ $goodsReceipt->status->badgeClass() }} text-xs">
                            {{ $goodsReceipt->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('goods_receipts.po_number') }}: 
                        @if($goodsReceipt->purchaseOrder)
                            <a href="{{ route('admin.purchase-orders.show', $goodsReceipt->purchaseOrder) }}" class="font-mono text-sky-600 dark:text-sky-400 hover:underline font-bold">
                                {{ $goodsReceipt->purchaseOrder->po_number }}
                            </a>
                        @else
                            <span class="font-mono text-gray-400">N/A</span>
                        @endif
                        <span class="mx-1.5 text-gray-300 dark:text-gray-600">|</span>
                        {{ __('goods_receipts.received_date') }}: 
                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ $goodsReceipt->received_date?->format('Y-m-d') ?? '—' }}</span>
                    </p>
                </div>
            </div>

            <!-- Key Metrics Badges -->
            <div class="flex items-center gap-3 text-xs flex-wrap">
                <div class="bg-gray-50 dark:bg-slate-800/60 p-2.5 px-3.5 rounded-xl border border-gray-200 dark:border-gray-700 text-center min-w-[110px]">
                    <span class="text-gray-400 dark:text-gray-400 block text-[10px] uppercase font-bold tracking-wider">{{ __('goods_receipts.received_by') }}</span>
                    <span class="font-bold text-slate-800 dark:text-gray-200 mt-0.5 block">{{ $goodsReceipt->receiver?->name ?? 'Dock Receiver' }}</span>
                </div>

                <div class="bg-gray-50 dark:bg-slate-800/60 p-2.5 px-3.5 rounded-xl border border-gray-200 dark:border-gray-700 text-center min-w-[110px]">
                    <span class="text-gray-400 dark:text-gray-400 block text-[10px] uppercase font-bold tracking-wider">{{ __('goods_receipts.temperature_reading') }}</span>
                    <span class="font-mono font-bold mt-0.5 block {{ ($goodsReceipt->temperature_within_range ?? true) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $goodsReceipt->temperature_logger_reading !== null ? number_format($goodsReceipt->temperature_logger_reading, 1) . ' °C' : ($goodsReceipt->temperature_reading !== null ? number_format($goodsReceipt->temperature_reading, 1) . ' °C' : 'N/A') }}
                    </span>
                </div>

                <div class="bg-gray-50 dark:bg-slate-800/60 p-2.5 px-3.5 rounded-xl border border-gray-200 dark:border-gray-700 text-center min-w-[110px]">
                    <span class="text-gray-400 dark:text-gray-400 block text-[10px] uppercase font-bold tracking-wider">{{ __('goods_receipts.seal_intact') }}</span>
                    <span class="font-bold mt-0.5 block">
                        @if($goodsReceipt->seal_verified || $goodsReceipt->seal_intact)
                            <span class="text-emerald-600 dark:text-emerald-400 inline-flex items-center gap-1"><i class="fa-solid fa-check"></i> {{ __('goods_receipts.intact') }}</span>
                        @else
                            <span class="text-rose-600 dark:text-rose-400 inline-flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ __('goods_receipts.broken') }}</span>
                        @endif
                    </span>
                </div>

                <div class="bg-gray-50 dark:bg-slate-800/60 p-2.5 px-3.5 rounded-xl border border-gray-200 dark:border-gray-700 text-center min-w-[110px]">
                    <span class="text-gray-400 dark:text-gray-400 block text-[10px] uppercase font-bold tracking-wider">{{ __('goods_receipts.warehouse_hub') }}</span>
                    <span class="font-bold text-slate-800 dark:text-gray-200 mt-0.5 block">{{ $goodsReceipt->location?->name_en ?? $goodsReceipt->location?->name ?? $goodsReceipt->location_id }}</span>
                </div>
            </div>
        </div>

        <!-- Carrier & Waybill details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs pt-1">
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <span class="text-gray-400 dark:text-gray-400 block mb-0.5">{{ __('goods_receipts.carrier_forwarder') }}:</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ $goodsReceipt->carrier_name ?? 'Standard Freight' }}</span>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <span class="text-gray-400 dark:text-gray-400 block mb-0.5">{{ __('goods_receipts.tracking_number') }}:</span>
                <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $goodsReceipt->tracking_number ?? 'N/A' }}</span>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <span class="text-gray-400 dark:text-gray-400 block mb-0.5">{{ __('goods_receipts.container_count') }}:</span>
                <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $goodsReceipt->container_count ?? 1 }} {{ app()->getLocale() === 'ar' ? 'صندوق / طرد' : 'Cartons / Packages' }}</span>
            </div>
        </div>

        @if($goodsReceipt->discrepancy_notes)
            <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-900 dark:text-amber-200 flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400 mt-0.5"></i>
                <div>
                    <strong>{{ __('goods_receipts.discrepancy_notes') }}:</strong> 
                    <span class="ml-1">{{ $goodsReceipt->discrepancy_notes }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- 2. Ingested Line Items Table (Dual Product & Material) -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 flex-wrap gap-2">
            <h4 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-purple-600 dark:text-purple-400"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'البنود المستلمة، التشغيلات، وحالة المخزون / الفحص' : 'Received Line Items, Lots & Warehouse Status' }}</span>
            </h4>
            <span class="badge badge-purple text-xs font-mono font-bold">{{ $goodsReceipt->items->count() }} {{ app()->getLocale() === 'ar' ? 'بند' : 'Items' }}</span>
        </div>

        <div class="table-responsive">
            <table class="table text-xs w-full">
                <thead>
                    <tr class="bg-gray-50/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300">
                        <th class="whitespace-nowrap px-3.5 py-2.5" style="width: 14%;">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Catalog Type' }}</th>
                        <th class="whitespace-nowrap px-3.5 py-2.5" style="width: 26%;">{{ app()->getLocale() === 'ar' ? 'البند' : 'Item Description' }}</th>
                        <th class="whitespace-nowrap px-3.5 py-2.5" style="width: 14%;">{{ __('goods_receipts.supplier_lot') }}</th>
                        <th class="whitespace-nowrap px-3.5 py-2.5" style="width: 12%;">{{ __('goods_receipts.received_qty') }}</th>
                        <th class="whitespace-nowrap px-3.5 py-2.5" style="width: 12%;">{{ __('goods_receipts.expiry_date') }}</th>
                        <th class="whitespace-nowrap px-3.5 py-2.5" style="width: 10%;">{{ __('goods_receipts.storage_bin') }}</th>
                        <th class="whitespace-nowrap px-3.5 py-2.5 text-center" style="width: 12%;">{{ app()->getLocale() === 'ar' ? 'حالة التخزين / QC' : 'Stock Status' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($goodsReceipt->items as $item)
                        @php
                            $isProduct = $item->item_type === 'product';
                            $batch = $goodsReceipt->batches->firstWhere('raw_material_id', $item->raw_material_id);
                        @endphp
                        <tr class="hover:bg-purple-50/20 dark:hover:bg-slate-800/30 transition">
                            <td class="whitespace-nowrap px-3.5 py-3">
                                @if($isProduct)
                                    <span class="badge badge-sky text-[11px] font-bold inline-flex items-center gap-1">
                                        <i class="fa-solid fa-box"></i>
                                        <span>Product</span>
                                    </span>
                                @else
                                    <span class="badge badge-emerald text-[11px] font-bold inline-flex items-center gap-1">
                                        <i class="fa-solid fa-flask"></i>
                                        <span>Raw Material</span>
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3.5 py-3">
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ $item->item_name }}
                                </div>
                                <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400">SKU: {{ $item->item_sku }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3.5 py-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $item->supplier_lot }}
                            </td>
                            <td class="whitespace-nowrap px-3.5 py-3 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($item->received_qty, 2) }}
                                @if($item->rejected_qty > 0)
                                    <span class="text-rose-500 text-[10px] block">({{ number_format($item->rejected_qty, 2) }} rejected)</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3.5 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ $item->expiry_date?->format('Y-m-d') ?? 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-3.5 py-3 font-mono text-gray-600 dark:text-gray-400">
                                {{ $item->storage_bin ?? 'A-01' }}
                            </td>
                            <td class="whitespace-nowrap px-3.5 py-3 text-center">
                                @if($isProduct)
                                    <span class="badge badge-emerald text-[11px] font-bold inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>{{ __('goods_receipts.stock_credited') }}</span>
                                    </span>
                                @else
                                    @if($batch)
                                        <a href="{{ route('admin.batches.show', $batch) }}" class="badge {{ $batch->status->badgeClass() }} text-[11px] hover:opacity-80 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-flask"></i>
                                            <span>{{ $batch->batch_number }}</span>
                                        </a>
                                    @else
                                        <span class="badge badge-purple text-[11px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-lock"></i>
                                            <span>{{ __('goods_receipts.quarantine') }}</span>
                                        </span>
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
