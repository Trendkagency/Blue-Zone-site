<x-layouts.admin 
    :pageTitle="__('admin.menu.offline_sales')" 
    :pageSubtitle="app()->getLocale() == 'ar' ? 'مبيعات مستودع المبيعات المباشرة، معاملات العملاء المباشرين، وتدقيق سجل الصندوق.' : 'Physical store counter sales, walk-in transactions, and register drawer audit.'"
    :breadcrumbs="[__('admin.menu.sales') => route('admin.offline-sales.index'), __('admin.menu.offline_sales') => route('admin.offline-sales.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.offline-sales.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-cash-register mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'فتح نقطة البيع (كاشير المستودع)' : 'Open POS Cashier Terminal' }}
        </a>
    </x-slot>

    <!-- Table Action Toolbar (Search, Excel Export, CSV & Print) -->
    <x-admin.table-toolbar 
        table="#offlineSalesTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل مبيعات المعرض والكاشير المباشر' : 'POS Sales & Cashier Ledger'" 
    />

    <!-- POS Sales Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table" id="offlineSalesTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ app()->getLocale() == 'ar' ? 'رقم المعاملة' : 'Sale ID' }}</th>
                        <th data-sort-type="text">{{ __('admin.invoices.invoice_number') }}</th>
                        <th data-sort-type="text">{{ app()->getLocale() == 'ar' ? 'موقع المستودع' : 'Warehouse Location' }}</th>
                        <th data-sort-type="text">{{ app()->getLocale() == 'ar' ? 'أخصائي الصندوق' : 'Cashier Specialist' }}</th>
                        <th data-sort-type="text">{{ __('admin.orders.customer') }}</th>
                        <th data-sort-type="text">{{ __('admin.pos.payment_method') }}</th>
                        <th data-sort-type="number">{{ __('admin.orders.amount') }}</th>
                        <th data-sort-type="date">{{ __('admin.orders.date') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() == 'ar' ? 'الإجراء' : 'Action' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                        @php
                            $sId = $sale['id'] ?? 1;
                            $sNum = $sale['sale_number'] ?? 'POS-001';
                            $sInv = $sale['invoice_number'] ?? 'INV-POS-001';
                            $sLoc = app()->getLocale() == 'ar' ? 'مستودع الرياض الرئيسي' : ($sale['store_location'] ?? 'Riyadh Central Warehouse');
                            $sCashier = app()->getLocale() == 'ar' ? 'أخصائي طول العمر المعتمد' : ($sale['cashier'] ?? 'Senior Specialist');
                            $sCust = $sale['customer_name'] ?? 'Walk-In Client';
                            $sPay = $sale['payment_method'] ?? 'Mada';
                            $sTotal = $sale['total'] ?? 0;
                            $sDate = $sale['date'] ?? '';
                            $sTime = $sale['time'] ?? '';
                        @endphp
                        <tr>
                            <td class="font-bold text-primary font-mono">
                                <a href="{{ route('admin.offline-sales.show', $sId) }}">
                                    {{ $sNum }}
                                </a>
                            </td>
                            <td class="font-mono text-xs">{{ $sInv }}</td>
                            <td>{{ $sLoc }}</td>
                            <td><i class="fa-solid fa-user-tie mr-1 ml-1 text-muted"></i> {{ $sCashier }}</td>
                            <td class="font-bold text-sm">{{ $sCust }}</td>
                            <td>
                                <span class="badge badge-accent text-xs">{{ $sPay }}</span>
                            </td>
                            <td class="font-bold">@currency((float)$sTotal)</td>
                            <td class="text-xs text-muted">{{ $sDate }} {{ $sTime }}</td>
                            <td>
                                <a href="{{ route('admin.offline-sales.show', $sId) }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-receipt mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'الإيصال' : 'Receipt' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$totalCount ?? count($sales)" />
    </div>
</x-layouts.admin>
