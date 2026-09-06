<x-layouts.admin 
    :pageTitle="__('admin.menu.offline_sales')" 
<<<<<<< HEAD
    pageSubtitle="Physical store counter sales, walk-in transactions, and register drawer audit."
    :breadcrumbs="['Sales' => route('admin.offline-sales.index'), 'POS Sales' => route('admin.offline-sales.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.offline-sales.create') }}" class="btn btn-primary">
            🛒 Open POS Cashier Terminal
=======
    :pageSubtitle="app()->getLocale() == 'ar' ? 'مبيعات معرض البوتيك المباشر، معاملات العملاء المباشرين، وتدقيق سجل الصندوق.' : 'Physical store counter sales, walk-in transactions, and register drawer audit.'"
    :breadcrumbs="[__('admin.menu.sales') => route('admin.offline-sales.index'), __('admin.menu.offline_sales') => route('admin.offline-sales.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.offline-sales.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-cash-register mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'فتح نقطة البيع (كاشير المعرض)' : 'Open POS Cashier Terminal' }}
>>>>>>> origin/main
        </a>
    </x-slot>

    <!-- POS Sales Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
<<<<<<< HEAD
                        <th>Sale ID</th>
                        <th>Invoice #</th>
                        <th>Boutique Location</th>
                        <th>Cashier Specialist</th>
                        <th>Guest / Customer</th>
                        <th>Tender Type</th>
                        <th>Gross Amount</th>
                        <th>Timestamp</th>
                        <th>Action</th>
=======
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم المعاملة' : 'Sale ID' }}</th>
                        <th>{{ __('admin.invoices.invoice_number') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'موقع المعرض' : 'Boutique Location' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'أخصائي الصندوق' : 'Cashier Specialist' }}</th>
                        <th>{{ __('admin.orders.customer') }}</th>
                        <th>{{ __('admin.pos.payment_method') }}</th>
                        <th>{{ __('admin.orders.amount') }}</th>
                        <th>{{ __('admin.orders.date') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الإجراء' : 'Action' }}</th>
>>>>>>> origin/main
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
<<<<<<< HEAD
                        <tr>
                            <td class="font-bold text-primary font-mono">
                                <a href="{{ route('admin.offline-sales.show', $sale['id']) }}">
                                    {{ $sale['sale_number'] }}
                                </a>
                            </td>
                            <td class="font-mono text-xs">{{ $sale['invoice_number'] }}</td>
                            <td>{{ $sale['store_location'] }}</td>
                            <td>👤 {{ $sale['cashier'] }}</td>
                            <td class="font-bold text-sm">{{ $sale['customer_name'] }}</td>
                            <td>
                                <span class="badge badge-accent text-xs">{{ $sale['payment_method'] }}</span>
                            </td>
                            <td class="font-bold">${{ number_format($sale['total'], 2) }}</td>
                            <td class="text-xs text-muted">{{ $sale['date'] }} {{ $sale['time'] }}</td>
                            <td>
                                <a href="{{ route('admin.offline-sales.show', $sale['id']) }}" class="btn btn-secondary btn-sm">
                                    🖨️ Receipt
=======
                        @php
                            $sId = $sale['id'] ?? 1;
                            $sNum = $sale['sale_number'] ?? 'POS-001';
                            $sInv = $sale['invoice_number'] ?? 'INV-POS-001';
                            $sLoc = app()->getLocale() == 'ar' ? 'بوتيك الرياض الرئيسي' : ($sale['store_location'] ?? 'Riyadh Flagship Boutique');
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
                            <td class="font-bold">${{ number_format((float)$sTotal, 2) }}</td>
                            <td class="text-xs text-muted">{{ $sDate }} {{ $sTime }}</td>
                            <td>
                                <a href="{{ route('admin.offline-sales.show', $sId) }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-receipt mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'الإيصال' : 'Receipt' }}
>>>>>>> origin/main
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($sales)" />
    </div>
</x-layouts.admin>
