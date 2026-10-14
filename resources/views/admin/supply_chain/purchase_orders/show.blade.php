<x-layouts.admin 
    :pageTitle="__('purchase_orders.po_number') . ': ' . $purchaseOrder->po_number" 
    :pageSubtitle="__('purchase_orders.supplier') . ': ' . $purchaseOrder->supplier?->name"
    :breadcrumbs="[__('purchase_orders.title') => route('admin.purchase-orders.index'), $purchaseOrder->po_number => '#']"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.purchase-orders.print', $purchaseOrder) }}" target="_blank" class="btn btn-secondary font-bold text-sm">
                <i class="fa-solid fa-print mr-1 ml-1"></i> {{ __('purchase_orders.print_po') }}
            </a>

            <!-- Status Transition Buttons based on state machine -->
            @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::Draft)
                <form method="POST" action="{{ route('admin.purchase-orders.submit', $purchaseOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary font-bold text-sm">
                        <i class="fa-solid fa-paper-plane mr-1 ml-1"></i> {{ __('purchase_orders.submit_for_approval') }}
                    </button>
                </form>
            @endif

            @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::Submitted)
                <form method="POST" action="{{ route('admin.purchase-orders.approve', $purchaseOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-success font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white">
                        <i class="fa-solid fa-stamp mr-1 ml-1"></i> {{ __('purchase_orders.approve_po') }}
                    </button>
                </form>
            @endif

            @if(in_array($purchaseOrder->status, [\App\Enums\PurchaseOrderStatus::Confirmed, \App\Enums\PurchaseOrderStatus::PartiallyReceived]))
                <a href="{{ route('admin.goods-receipts.create', ['po_id' => $purchaseOrder->id]) }}" class="btn btn-primary font-bold text-sm bg-sky-600 hover:bg-sky-700 text-white">
                    <i class="fa-solid fa-truck-ramp-box mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'استلام شحنة بالرصيف (GRN)' : 'Receive at Dock (GRN)' }}
                </a>
            @endif

            @if(in_array($purchaseOrder->status, [\App\Enums\PurchaseOrderStatus::Draft, \App\Enums\PurchaseOrderStatus::Submitted]))
                <form method="POST" action="{{ route('admin.purchase-orders.cancel', $purchaseOrder) }}" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من إلغاء أمر الشراء؟' : 'Are you sure you want to cancel this PO?' }}')">
                    @csrf
                    <button type="submit" class="btn btn-danger font-bold text-sm">
                        <i class="fa-solid fa-ban mr-1 ml-1"></i> {{ __('purchase_orders.cancel_po') }}
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <!-- PO Overview & Status Ribbon -->
    <div class="card p-5 mb-6 border shadow-sm border-gray-100 dark:border-gray-800">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/40 text-sky-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-base font-black text-slate-800 dark:text-white font-mono">{{ $purchaseOrder->po_number }}</h4>
                        <span class="badge {{ $purchaseOrder->status->badgeClass() }} text-xs">
                            {{ $purchaseOrder->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">
                        {{ __('purchase_orders.order_date') }}: {{ $purchaseOrder->order_date?->format('Y-m-d') }} |
                        {{ __('purchase_orders.expected_delivery_date') }}: {{ $purchaseOrder->expected_delivery_date?->format('Y-m-d') ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <!-- Approval & Audit Separation Details -->
            <div class="flex items-center gap-3 text-xs">
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700">
                    <span class="text-gray-400 block">{{ app()->getLocale() === 'ar' ? 'المُنشئ' : 'Created By' }}:</span>
                    <span class="font-bold text-slate-700 dark:text-gray-300">{{ $purchaseOrder->creator?->name ?? 'System' }}</span>
                </div>
                @if($purchaseOrder->approver)
                    <div class="bg-emerald-50 dark:bg-emerald-950/30 p-2.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                        <span class="text-emerald-600 block font-bold">{{ app()->getLocale() === 'ar' ? 'المعتمد (فصل المهام)' : 'Approved By' }}:</span>
                        <span class="font-bold text-emerald-700 dark:text-emerald-300">{{ $purchaseOrder->approver->name }}</span>
                        <span class="text-[10px] text-emerald-500 block">{{ $purchaseOrder->approved_at?->format('Y-m-d H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h4 class="text-sm font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4 flex items-center justify-between">
            <span><i class="fa-solid fa-list-check text-sky-600 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'بنود المواد والكميات المستلمة' : 'Ordered Materials & Received Fulfillments' }}</span>
            <span class="badge badge-sky text-xs">{{ $purchaseOrder->items->count() }} {{ app()->getLocale() === 'ar' ? 'بند' : 'Items' }}</span>
        </h4>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>{{ __('purchase_orders.raw_material') }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الرمز' : 'SKU' }}</th>
                        <th>{{ __('purchase_orders.quantity') }}</th>
                        <th>{{ __('purchase_orders.received_qty') }}</th>
                        <th>{{ __('purchase_orders.unit_price') }}</th>
                        <th>{{ __('purchase_orders.line_total') }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'نسبة الاستلام' : 'Fulfillment' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                        @php
                            $pct = $item->quantity_ordered > 0 ? min(100, round(($item->quantity_received / $item->quantity_ordered) * 100)) : 0;
                        @endphp
                        <tr>
                            <td class="font-bold text-slate-800 dark:text-white">
                                <a href="{{ route('admin.raw-materials.show', $item->rawMaterial) }}" class="text-sky-600 hover:underline">
                                    {{ $item->rawMaterial?->name }}
                                </a>
                            </td>
                            <td class="font-mono text-gray-500">{{ $item->rawMaterial?->sku }}</td>
                            <td class="font-mono font-bold">{{ number_format($item->quantity_ordered, 2) }} {{ $item->rawMaterial?->uom?->value }}</td>
                            <td class="font-mono font-bold text-emerald-600">{{ number_format($item->quantity_received, 2) }} {{ $item->rawMaterial?->uom?->value }}</td>
                            <td class="font-mono">{{ number_format($item->unit_price, 2) }} {{ $purchaseOrder->currency }}</td>
                            <td class="font-mono font-bold">{{ number_format($item->total_price, 2) }} {{ $purchaseOrder->currency }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-full" style="width: {{ $pct }}%;"></div>
                                    </div>
                                    <span class="font-mono text-[11px] font-bold {{ $pct === 100 ? 'text-emerald-600' : 'text-slate-600' }}">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Landed Costs -->
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <div class="w-72 space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-500">
                    <span>{{ __('purchase_orders.subtotal') }}:</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-white">{{ number_format($purchaseOrder->subtotal, 2) }} {{ $purchaseOrder->currency }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>{{ __('purchase_orders.shipping_cost') }}:</span>
                    <span class="font-mono font-bold">{{ number_format($purchaseOrder->shipping_cost, 2) }} {{ $purchaseOrder->currency }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>{{ __('purchase_orders.customs_duty') }}:</span>
                    <span class="font-mono font-bold">{{ number_format($purchaseOrder->customs_duty, 2) }} {{ $purchaseOrder->currency }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>{{ __('purchase_orders.tax_amount') }}:</span>
                    <span class="font-mono font-bold">{{ number_format($purchaseOrder->tax_amount, 2) }} {{ $purchaseOrder->currency }}</span>
                </div>
                <div class="flex justify-between text-sm font-black border-t border-gray-200 dark:border-gray-700 pt-2 text-slate-800 dark:text-white">
                    <span>{{ __('purchase_orders.total_amount') }}:</span>
                    <span class="font-mono text-sky-600">{{ number_format($purchaseOrder->total_amount, 2) }} {{ $purchaseOrder->currency }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Linked Goods Receipts (GRNs) -->
    <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h4 class="text-sm font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4 flex items-center justify-between">
            <span><i class="fa-solid fa-truck-ramp-box text-emerald-600 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'إشعارات الاستلام بالرصيف (GRN) المرتبطة' : 'Linked Dock Goods Receipts (GRNs)' }}</span>
            <span class="badge badge-emerald text-xs">{{ $purchaseOrder->goodsReceipts->count() }}</span>
        </h4>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'رقم الإشعار' : 'GRN #' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تاريخ الاستلام' : 'Received Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المستلم' : 'Received By' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'قراءة مسجل الحرارة' : 'Temp Logger' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'فحص الأختام' : 'Seal Intact' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'عرض' : 'Action' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrder->goodsReceipts as $grn)
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                <a href="{{ route('admin.goods-receipts.show', $grn) }}" class="hover:underline">
                                    {{ $grn->grn_number }}
                                </a>
                            </td>
                            <td>{{ $grn->received_date?->format('Y-m-d') }}</td>
                            <td>{{ $grn->receiver?->name ?? 'Warehouse Staff' }}</td>
                            <td class="font-mono">
                                {{ $grn->temperature_reading !== null ? $grn->temperature_reading . ' °C' : 'N/A' }}
                            </td>
                            <td>
                                @if($grn->seal_intact)
                                    <span class="text-emerald-600 font-bold"><i class="fa-solid fa-check"></i> {{ app()->getLocale() === 'ar' ? 'سليم' : 'Intact' }}</span>
                                @else
                                    <span class="text-rose-600 font-bold"><i class="fa-solid fa-triangle-exclamation"></i> {{ app()->getLocale() === 'ar' ? 'مكسور' : 'Broken' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $grn->status->badgeClass() }} text-[11px]">
                                    {{ $grn->status->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.goods-receipts.show', $grn) }}" class="btn btn-ghost btn-xs text-sky-600">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'لم يتم استلام أي دفعات بالرصيف بعد لهذا الطلب.' : 'No dock receipts filed against this PO yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
