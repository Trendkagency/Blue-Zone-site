<x-layouts.admin 
    :pageTitle="__('purchase_orders.po_number') . ': ' . $purchaseOrder->po_number" 
    :pageSubtitle="__('purchase_orders.supplier') . ': ' . ($purchaseOrder->supplier?->name ?? 'Vendor')"
    :breadcrumbs="[
        __('purchase_orders.title') => route('admin.purchase-orders.index'), 
        $purchaseOrder->po_number => '#'
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.purchase-orders.print', $purchaseOrder) }}" target="_blank" class="btn btn-secondary font-bold text-sm shadow-sm">
                <i class="fa-solid fa-print mr-1 ml-1"></i> {{ __('purchase_orders.print_po') }}
            </a>

            <!-- Status Transition Buttons based on state machine -->
            @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::Draft)
                <form method="POST" action="{{ route('admin.purchase-orders.submit', $purchaseOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary font-bold text-sm shadow-sm">
                        <i class="fa-solid fa-paper-plane mr-1 ml-1"></i> {{ __('purchase_orders.submit_for_approval') }}
                    </button>
                </form>
            @endif

            @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::Submitted)
                <form method="POST" action="{{ route('admin.purchase-orders.approve', $purchaseOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-success font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                        <i class="fa-solid fa-stamp mr-1 ml-1"></i> {{ __('purchase_orders.approve_po') }}
                    </button>
                </form>
            @endif

            @if(in_array($purchaseOrder->status, [\App\Enums\PurchaseOrderStatus::Confirmed, \App\Enums\PurchaseOrderStatus::PartiallyReceived]))
                <a href="{{ route('admin.goods-receipts.create', ['po_id' => $purchaseOrder->id]) }}" class="btn btn-primary font-bold text-sm bg-sky-600 hover:bg-sky-700 text-white shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'استلام شحنة واردة بالرصيف (GRN)' : 'Receive at Dock (GRN)' }}</span>
                </a>
            @endif

            @if(in_array($purchaseOrder->status, [\App\Enums\PurchaseOrderStatus::Draft, \App\Enums\PurchaseOrderStatus::Submitted]))
                <form method="POST" action="{{ route('admin.purchase-orders.cancel', $purchaseOrder) }}" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من إلغاء أمر الشراء هذا؟' : 'Are you sure you want to cancel this PO?' }}')">
                    @csrf
                    <button type="submit" class="btn btn-danger font-bold text-sm">
                        <i class="fa-solid fa-ban mr-1 ml-1"></i> {{ __('purchase_orders.cancel_po') }}
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <!-- 1. Workflow Stepper -->
    @php
        $statusVal = $purchaseOrder->status->value ?? 'draft';
        $steps = [
            'draft' => ['label' => app()->getLocale() === 'ar' ? 'مسودة' : 'Draft', 'icon' => 'fa-file-pen'],
            'submitted' => ['label' => app()->getLocale() === 'ar' ? 'مقدم للاعتماد' : 'Submitted', 'icon' => 'fa-paper-plane'],
            'confirmed' => ['label' => app()->getLocale() === 'ar' ? 'معتمد ومؤكد' : 'Confirmed', 'icon' => 'fa-stamp'],
            'partially_received' => ['label' => app()->getLocale() === 'ar' ? 'استلام جزئي' : 'Partially Received', 'icon' => 'fa-boxes-packing'],
            'received' => ['label' => app()->getLocale() === 'ar' ? 'مكتمل الاستلام' : 'Completed', 'icon' => 'fa-circle-check'],
        ];
        $isCancelled = $statusVal === 'cancelled';
    @endphp

    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        @if($isCancelled)
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 flex items-center gap-3 text-rose-700 dark:text-rose-300">
                <i class="fa-solid fa-ban text-2xl"></i>
                <div>
                    <h5 class="font-bold text-sm">{{ app()->getLocale() === 'ar' ? 'تم إلغاء أمر الشراء هذا' : 'This Purchase Order Has Been Cancelled' }}</h5>
                    <p class="text-xs">{{ app()->getLocale() === 'ar' ? 'لا يمكن إجراء أي عمليات استلام أو تعديلات على أمر شراء ملغي.' : 'No dock intake or modifications are permitted on cancelled procurement records.' }}</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                @php
                    $reached = true;
                    $orderMap = ['draft' => 1, 'submitted' => 2, 'confirmed' => 3, 'partially_received' => 4, 'received' => 5];
                    $currentOrder = $orderMap[$statusVal] ?? 1;
                @endphp
                @foreach($steps as $key => $step)
                    @php
                        $stepOrder = $orderMap[$key];
                        $isCurrent = $statusVal === $key;
                        $isPast = $stepOrder < $currentOrder;
                    @endphp
                    <div class="flex items-center gap-2 p-2.5 rounded-xl border {{ $isCurrent ? 'bg-sky-50 dark:bg-sky-950/40 border-sky-300 dark:border-sky-700' : ($isPast ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800' : 'bg-gray-50/50 dark:bg-slate-800/30 border-gray-100 dark:border-gray-800 opacity-60') }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold {{ $isCurrent ? 'bg-sky-600 text-white' : ($isPast ? 'bg-emerald-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-500') }}">
                            @if($isPast)
                                <i class="fa-solid fa-check"></i>
                            @else
                                <i class="fa-solid {{ $step['icon'] }}"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Step {{ $stepOrder }}</p>
                            <p class="text-xs font-bold truncate {{ $isCurrent ? 'text-sky-700 dark:text-sky-300' : ($isPast ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-400') }}">
                                {{ $step['label'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 2. PO Overview & Vendor Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Vendor Info Card -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 dark:border-gray-800 pb-2">
                <i class="fa-solid fa-truck-field text-sky-600"></i>
                <span>{{ __('purchase_orders.supplier') }}</span>
            </h4>
            <div class="space-y-1.5 text-xs">
                <div class="text-sm font-bold text-slate-800 dark:text-white">
                    <a href="{{ route('admin.suppliers.show', $purchaseOrder->supplier) }}" class="text-sky-600 hover:underline">
                        {{ $purchaseOrder->supplier?->name }}
                    </a>
                </div>
                <div class="text-gray-500 font-mono">Code: {{ $purchaseOrder->supplier?->code }}</div>
                @if($purchaseOrder->supplier?->email)
                    <div class="text-gray-500"><i class="fa-solid fa-envelope mr-1 text-gray-400"></i> {{ $purchaseOrder->supplier?->email }}</div>
                @endif
                @if($purchaseOrder->supplier?->phone)
                    <div class="text-gray-500"><i class="fa-solid fa-phone mr-1 text-gray-400"></i> {{ $purchaseOrder->supplier?->phone }}</div>
                @endif
                @if($purchaseOrder->supplier_reference)
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-gray-400 block">{{ app()->getLocale() === 'ar' ? 'مرجع المورد / عرض السعر' : 'Supplier Quote / Ref' }}:</span>
                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ $purchaseOrder->supplier_reference }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Logistics & Delivery Card -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 dark:border-gray-800 pb-2">
                <i class="fa-solid fa-warehouse text-emerald-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'اللوجستيات وموقع الاستلام' : 'Logistics & Receiving Dock' }}</span>
            </h4>
            <div class="space-y-1.5 text-xs">
                <div>
                    <span class="text-gray-400 block">{{ app()->getLocale() === 'ar' ? 'المستودع المستهدف' : 'Destination Hub' }}:</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ $purchaseOrder->destinationLocation?->name_en ?? $purchaseOrder->destination_location_id }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">{{ __('purchase_orders.order_date') }}:</span>
                    <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ $purchaseOrder->order_date?->format('Y-m-d') }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">{{ __('purchase_orders.expected_delivery_date') }}:</span>
                    <span class="font-mono font-bold {{ $purchaseOrder->expected_date && $purchaseOrder->expected_date < now() && !in_array($purchaseOrder->status, [\App\Enums\PurchaseOrderStatus::Received, \App\Enums\PurchaseOrderStatus::Cancelled]) ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300' }}">
                        {{ $purchaseOrder->expected_date?->format('Y-m-d') ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Governance & Approvals Audit Card -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 dark:border-gray-800 pb-2">
                <i class="fa-solid fa-shield-halved text-purple-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'الحوكمة وفصل المهام' : 'Governance & Dual Audit' }}</span>
            </h4>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-gray-400 block">{{ app()->getLocale() === 'ar' ? 'المُنشئ' : 'Created By' }}:</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ $purchaseOrder->creator?->name ?? 'System' }}</span>
                    <span class="text-[10px] text-gray-400 block">{{ $purchaseOrder->created_at?->format('Y-m-d H:i') }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">{{ app()->getLocale() === 'ar' ? 'الاعتماد' : 'Approval Status' }}:</span>
                    @if($purchaseOrder->approver)
                        <span class="font-bold text-emerald-600 flex items-center gap-1">
                            <i class="fa-solid fa-check-circle"></i> {{ $purchaseOrder->approver->name }}
                        </span>
                        <span class="text-[10px] text-emerald-500 block">{{ $purchaseOrder->approved_at?->format('Y-m-d H:i') }}</span>
                    @else
                        <span class="text-amber-500 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-clock"></i> {{ app()->getLocale() === 'ar' ? 'بانتظار الاعتماد المالي' : 'Pending Formal Sign-off' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Dual-Catalog Line Items Table -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
            <h4 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-sky-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'بنود الشراء ونسب الاستلام الفعلي' : 'Ordered Items & Fulfillment Progress' }}</span>
            </h4>
            <span class="badge badge-sky text-xs font-mono">{{ $purchaseOrder->items->count() }} {{ app()->getLocale() === 'ar' ? 'بند' : 'Items' }}</span>
        </div>

        <div class="table-responsive">
            <table class="table text-xs w-full">
                <thead>
                    <tr class="bg-gray-50/70 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300">
                        <th style="width: 5%;">#</th>
                        <th style="width: 14%;">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Catalog Type' }}</th>
                        <th style="width: 31%;">{{ app()->getLocale() === 'ar' ? 'البند' : 'Item Description' }}</th>
                        <th style="width: 12%;">{{ __('purchase_orders.quantity') }}</th>
                        <th style="width: 12%;">{{ __('purchase_orders.received_qty') }}</th>
                        <th style="width: 13%;">{{ __('purchase_orders.unit_price') }}</th>
                        <th style="width: 13%;">{{ __('purchase_orders.line_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $index => $item)
                        @php
                            $ord = (float) $item->ordered_qty;
                            $rec = (float) $item->received_qty;
                            $pct = $ord > 0 ? min(100, round(($rec / $ord) * 100)) : 0;
                            $isProduct = $item->item_type === 'product';
                        @endphp
                        <tr class="hover:bg-sky-50/20 dark:hover:bg-slate-800/30 transition">
                            <td class="font-mono text-gray-400">{{ $index + 1 }}</td>
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
                                <div class="font-mono text-[11px] text-gray-500">
                                    SKU: {{ $item->item_sku }}
                                </div>
                                @if($item->notes)
                                    <div class="text-[10px] text-slate-500 italic mt-0.5">{{ $item->notes }}</div>
                                @endif
                            </td>
                            <td class="font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ number_format($ord, 2) }}
                            </td>
                            <td>
                                <div class="space-y-1">
                                    <span class="font-mono font-bold {{ $rec >= $ord ? 'text-emerald-600' : ($rec > 0 ? 'text-amber-600' : 'text-gray-400') }}">
                                        {{ number_format($rec, 2) }}
                                    </span>
                                    <div class="w-20 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-full transition-all" style="width: {{ $pct }}%;"></div>
                                    </div>
                                    <span class="text-[10px] font-mono text-gray-400">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td class="font-mono text-slate-700 dark:text-slate-300">
                                {{ number_format($item->unit_price, 2) }} {{ $purchaseOrder->currency }}
                            </td>
                            <td class="font-mono font-bold text-slate-800 dark:text-white">
                                {{ number_format($item->total_price, 2) }} {{ $purchaseOrder->currency }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Landed Cost Breakdown -->
        <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <div class="w-80 space-y-2 text-xs">
                <div class="flex justify-between text-gray-500">
                    <span>{{ __('purchase_orders.subtotal') }}:</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-white">{{ number_format($purchaseOrder->subtotal, 2) }} {{ $purchaseOrder->currency }}</span>
                </div>
                @if($purchaseOrder->freight_cost > 0)
                    <div class="flex justify-between text-gray-500">
                        <span>{{ __('purchase_orders.shipping_cost') }}:</span>
                        <span class="font-mono text-slate-800 dark:text-white">{{ number_format($purchaseOrder->freight_cost, 2) }} {{ $purchaseOrder->currency }}</span>
                    </div>
                @endif
                @if($purchaseOrder->customs_duties > 0)
                    <div class="flex justify-between text-gray-500">
                        <span>{{ __('purchase_orders.customs_duty') }}:</span>
                        <span class="font-mono text-slate-800 dark:text-white">{{ number_format($purchaseOrder->customs_duties, 2) }} {{ $purchaseOrder->currency }}</span>
                    </div>
                @endif
                @if($purchaseOrder->tax_amount > 0)
                    <div class="flex justify-between text-gray-500">
                        <span>{{ __('purchase_orders.tax_amount') }} (VAT):</span>
                        <span class="font-mono text-slate-800 dark:text-white">{{ number_format($purchaseOrder->tax_amount, 2) }} {{ $purchaseOrder->currency }}</span>
                    </div>
                @endif
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700 flex justify-between text-sm">
                    <span class="font-black text-slate-800 dark:text-white uppercase">{{ __('purchase_orders.total_amount') }}:</span>
                    <span class="font-black font-mono text-sky-600 text-base">{{ number_format($purchaseOrder->grand_total, 2) }} {{ $purchaseOrder->currency }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Associated Goods Receipts (GRN) -->
    @if($purchaseOrder->goodsReceipts && $purchaseOrder->goodsReceipts->count() > 0)
        <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-800 dark:text-white flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-truck-ramp-box text-emerald-600"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'سندات الاستلام بالرصيف المسجلة (GRN)' : 'Inbound Goods Receipts (GRN) Log' }}</span>
                </span>
                <span class="badge badge-emerald text-xs font-mono">{{ $purchaseOrder->goodsReceipts->count() }} GRNs</span>
            </h4>

            <div class="table-responsive">
                <table class="table text-xs w-full">
                    <thead>
                        <tr class="bg-gray-50/70 dark:bg-slate-800/50">
                            <th>GRN #</th>
                            <th>{{ __('goods_receipts.received_date') }}</th>
                            <th>{{ __('goods_receipts.received_by') }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'الأختام والحرارة' : 'Integrity & Temp' }}</th>
                            <th>{{ __('goods_receipts.status') }}</th>
                            <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->goodsReceipts as $grn)
                            <tr>
                                <td class="font-mono font-bold text-sky-600">
                                    <a href="{{ route('admin.goods-receipts.show', $grn) }}" class="hover:underline">
                                        {{ $grn->grn_number }}
                                    </a>
                                </td>
                                <td class="font-mono">{{ $grn->received_date?->format('Y-m-d') }}</td>
                                <td>{{ $grn->receiver?->name ?? 'System' }}</td>
                                <td>
                                    @if($grn->seal_verified)
                                        <span class="text-emerald-600 font-bold"><i class="fa-solid fa-check"></i> Seal Intact</span>
                                    @else
                                        <span class="text-rose-600 font-bold"><i class="fa-solid fa-triangle-exclamation"></i> Seal Broken</span>
                                    @endif
                                    @if($grn->temperature_logger_reading !== null)
                                        <span class="text-gray-400 mx-1">|</span>
                                        <span class="font-mono text-slate-700 dark:text-slate-300">{{ $grn->temperature_logger_reading }}°C</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $grn->status->badgeClass() }} text-[11px]">
                                        {{ $grn->status->label() }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.goods-receipts.show', $grn) }}" class="btn btn-ghost btn-xs text-sky-600">
                                        <i class="fa-solid fa-eye mr-1"></i> {{ app()->getLocale() === 'ar' ? 'عرض السند' : 'View GRN' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.admin>
