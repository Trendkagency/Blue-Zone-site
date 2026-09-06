<x-layouts.admin 
    :pageTitle="__('admin.menu.invoices')" 
    pageSubtitle="Official corporate and e-commerce tax invoices with QR serialization."
    :breadcrumbs="['Sales' => route('admin.invoices.index'), 'Invoices' => route('admin.invoices.index')]"
>
    <!-- Invoices Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Order Ref</th>
                        <th>Customer</th>
                        <th>Tax (15%)</th>
                        <th>Gross Total</th>
                        <th>Payment Status</th>
                        <th>Issue Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="font-bold font-mono text-primary">
                                <a href="{{ route('admin.invoices.show', $order['order_number']) }}">
                                    {{ $order['invoice_number'] }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order['order_number']) }}" class="text-secondary font-mono">
                                    {{ $order['order_number'] }}
                                </a>
                            </td>
                            <td class="font-bold text-sm">{{ $order['customer_name'] }}</td>
                            <td>${{ number_format($order['tax'], 2) }}</td>
                            <td class="font-bold">${{ number_format($order['total'], 2) }}</td>
                            <td>
                                <span class="badge badge-success text-xs">{{ $order['payment_status'] }}</span>
                            </td>
                            <td class="text-xs text-muted">{{ $order['date'] }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.invoices.show', $order['order_number']) }}" class="action-btn" title="View & Print">
                                        🖨️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($orders)" />
    </div>
</x-layouts.admin>
