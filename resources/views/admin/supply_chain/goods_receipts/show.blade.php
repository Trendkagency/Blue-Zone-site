<x-layouts.admin 
    :pageTitle="__('goods_receipts.grn_number') . ': ' . $goodsReceipt->grn_number" 
    :pageSubtitle="__('goods_receipts.supplier') . ': ' . $goodsReceipt->supplier?->name"
    :breadcrumbs="[__('goods_receipts.title') => route('admin.goods-receipts.index'), $goodsReceipt->grn_number => '#']"
>
    <!-- Dock Inspection Status Card -->
    <div class="card p-5 mb-6 border shadow-sm border-gray-100 dark:border-gray-800">
        <div class="flex items-center justify-between flex-wrap gap-4">
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
                        <a href="{{ route('admin.purchase-orders.show', $goodsReceipt->purchaseOrder) }}" class="font-mono text-sky-600 hover:underline">
                            {{ $goodsReceipt->purchaseOrder?->po_number }}
                        </a> |
                        {{ __('goods_receipts.received_date') }}: {{ $goodsReceipt->received_date?->format('Y-m-d') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                    <span class="text-gray-400 block">{{ __('goods_receipts.received_by') }}</span>
                    <span class="font-bold text-slate-700 dark:text-gray-300">{{ $goodsReceipt->receiver?->name ?? 'Dock Receiver' }}</span>
                </div>
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                    <span class="text-gray-400 block">{{ __('goods_receipts.temperature_reading') }}</span>
                    <span class="font-mono font-bold text-slate-700 dark:text-gray-300">
                        {{ $goodsReceipt->temperature_reading !== null ? $goodsReceipt->temperature_reading . ' °C' : 'N/A' }}
                    </span>
                </div>
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                    <span class="text-gray-400 block">{{ __('goods_receipts.seal_intact') }}</span>
                    @if($goodsReceipt->seal_intact)
                        <span class="font-bold text-emerald-600"><i class="fa-solid fa-check"></i> {{ app()->getLocale() === 'ar' ? 'سليم' : 'Intact' }}</span>
                    @else
                        <span class="font-bold text-rose-600"><i class="fa-solid fa-triangle-exclamation"></i> {{ app()->getLocale() === 'ar' ? 'مكسور' : 'Broken' }}</span>
                    @endif
                </div>
            </div>
        </div>

        @if($goodsReceipt->discrepancy_notes)
            <div class="mt-4 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-900 dark:text-amber-200">
                <strong>{{ __('goods_receipts.discrepancy_notes') }}:</strong> {{ $goodsReceipt->discrepancy_notes }}
            </div>
        @endif
    </div>

    <!-- Ingested Batches Table -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h4 class="text-sm font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4 flex items-center justify-between">
            <span><i class="fa-solid fa-boxes-stacked text-purple-600 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'التشغيلات المنشأة بالحجر الصحي ومتابعة الفحص QC' : 'Generated Quarantine Batches & QC Release Status' }}</span>
            <span class="badge badge-purple text-xs">{{ $goodsReceipt->items->count() }} {{ app()->getLocale() === 'ar' ? 'تشغيلة' : 'Batches' }}</span>
        </h4>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>{{ __('batches.batch_number') }}</th>
                        <th>{{ __('purchase_orders.raw_material') }}</th>
                        <th>{{ __('batches.supplier_lot') }}</th>
                        <th>{{ __('goods_receipts.quantity_received') }}</th>
                        <th>{{ __('batches.location') }}</th>
                        <th>{{ __('batches.expiry_date') }}</th>
                        <th>{{ __('batches.status') }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'إجراءات الجودة QC' : 'QC Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($goodsReceipt->items as $item)
                        @php
                            $batch = $item->batch;
                        @endphp
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                @if($batch)
                                    <a href="{{ route('admin.batches.show', $batch) }}" class="hover:underline">
                                        {{ $batch->batch_number }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    <a href="{{ route('admin.raw-materials.show', $item->rawMaterial) }}" class="hover:underline text-sky-600">
                                        {{ $item->rawMaterial?->name }}
                                    </a>
                                </div>
                                <span class="text-[11px] text-gray-500 font-mono">{{ $item->rawMaterial?->sku }}</span>
                            </td>
                            <td class="font-mono text-gray-600 dark:text-gray-300">
                                {{ $item->supplier_lot }}
                            </td>
                            <td class="font-mono font-bold">
                                {{ number_format($item->quantity_received, 2) }} {{ $item->rawMaterial?->uom?->value }}
                            </td>
                            <td class="font-mono">{{ $item->location_id }}</td>
                            <td class="font-mono">{{ $item->expiry_date?->format('Y-m-d') }}</td>
                            <td>
                                @if($batch)
                                    <span class="badge {{ $batch->status->badgeClass() }} text-[11px]">
                                        {{ $batch->status->label() }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($batch)
                                    @if($batch->status === \App\Enums\RawMaterialBatchStatus::Quarantine)
                                        <a href="{{ route('admin.qc.create', ['batch_id' => $batch->id]) }}" class="btn btn-primary btn-xs font-bold bg-purple-600 hover:bg-purple-700 text-white">
                                            <i class="fa-solid fa-microscope mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'فحص الجودة QC' : 'Inspect' }}
                                        </a>
                                    @else
                                        <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-ghost btn-xs text-sky-600">
                                            <i class="fa-solid fa-eye mr-1"></i> {{ app()->getLocale() === 'ar' ? 'عرض السجل' : 'View Batch' }}
                                        </a>
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
