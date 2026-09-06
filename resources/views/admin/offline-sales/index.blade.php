<x-layouts.admin 
    :pageTitle="__('admin.menu.offline_sales')" 
    pageSubtitle="Physical store counter sales, walk-in transactions, and register drawer audit."
    :breadcrumbs="['Sales' => route('admin.offline-sales.index'), 'POS Sales' => route('admin.offline-sales.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.offline-sales.create') }}" class="btn btn-primary">
            🛒 Open POS Cashier Terminal
        </a>
    </x-slot>

    <!-- POS Sales Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Invoice #</th>
                        <th>Boutique Location</th>
                        <th>Cashier Specialist</th>
                        <th>Guest / Customer</th>
                        <th>Tender Type</th>
                        <th>Gross Amount</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
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
