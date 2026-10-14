<x-layouts.admin 
    :pageTitle="__('purchase_orders.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة أوامر الشراء للمواد الأولية، تتبع دورة الاعتماد، وتكاليف الوصول Landed Cost' : 'Procurement Orders, Multi-tier Approvals, Status Workflows & Landed Cost Ledger'"
    :breadcrumbs="[__('purchase_orders.title') => route('admin.purchase-orders.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('purchase_orders.new_po') }}
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي الأوامر' : 'Total Purchase Orders' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $purchaseOrders->total() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'بانتظار الاعتماد' : 'Pending Approval' }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $kpis['pending_approval'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-stamp"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'في انتظار الاستلام بالرصيف' : 'Awaiting Dock Receipt' }}</p>
                <p class="text-2xl font-black text-indigo-600 mt-1">{{ $kpis['awaiting_receipt'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'أوامر متأخرة عن الشحن' : 'Overdue Deliveries' }}</p>
                <p class="text-2xl font-black text-rose-500 mt-1">{{ $kpis['overdue'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.purchase-orders.index') }}" class="flex items-center justify-between gap-4 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم الطلب PO-YYYY-XXXX...' : 'Search PO number PO-YYYY-XXXX...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    @foreach(\App\Enums\PurchaseOrderStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <select name="supplier_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الموردين' : 'All Suppliers' }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- PO Table -->
    <x-admin.table-toolbar 
        table="#poTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل أوامر الشراء' : 'Purchase Orders Master Log'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table" id="poTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('purchase_orders.po_number') }}</th>
                        <th data-sort-type="text">{{ __('purchase_orders.supplier') }}</th>
                        <th data-sort-type="date">{{ __('purchase_orders.order_date') }}</th>
                        <th data-sort-type="date">{{ __('purchase_orders.expected_delivery_date') }}</th>
                        <th data-sort-type="number">{{ __('purchase_orders.total_amount') }}</th>
                        <th data-sort-type="text">{{ __('purchase_orders.status') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                        <tr>
                            <td class="font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.purchase-orders.show', $po) }}" class="hover:underline">
                                    {{ $po->po_number }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    <a href="{{ route('admin.suppliers.show', $po->supplier) }}" class="hover:underline">
                                        {{ $po->supplier?->name }}
                                    </a>
                                </div>
                                <span class="text-xs text-gray-500">{{ $po->supplier?->code }}</span>
                            </td>
                            <td class="font-mono text-xs">
                                {{ $po->order_date?->format('Y-m-d') }}
                            </td>
                            <td class="font-mono text-xs">
                                @if($po->expected_delivery_date)
                                    @php
                                        $isOverdue = $po->expected_delivery_date->isPast() && $po->status === \App\Enums\PurchaseOrderStatus::Confirmed;
                                    @endphp
                                    <span class="{{ $isOverdue ? 'text-rose-600 font-bold' : '' }}">
                                        {{ $po->expected_delivery_date->format('Y-m-d') }}
                                        @if($isOverdue)
                                            <span class="text-rose-500">({{ app()->getLocale() === 'ar' ? 'متأخر' : 'Overdue' }})</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="font-mono font-bold text-slate-800 dark:text-white">
                                {{ number_format($po->total_amount, 2) }} {{ $po->currency }}
                            </td>
                            <td>
                                <span class="badge {{ $po->status->badgeClass() }} text-xs">
                                    {{ $po->status->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.purchase-orders.show', $po) }}" class="btn btn-ghost btn-sm text-sky-600" title="{{ app()->getLocale() === 'ar' ? 'عرض أمر الشراء' : 'View PO' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.purchase-orders.print', $po) }}" target="_blank" class="btn btn-ghost btn-sm text-slate-600" title="{{ app()->getLocale() === 'ar' ? 'طباعة أمر الشراء' : 'Print PDF' }}">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                <i class="fa-solid fa-file-invoice text-3xl mb-2 text-gray-400"></i>
                                <p>{{ app()->getLocale() === 'ar' ? 'لم يتم العثور على أوامر شراء.' : 'No purchase orders recorded.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchaseOrders->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
