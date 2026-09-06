<x-layouts.admin 
    :pageTitle="__('admin.menu.online_orders')" 
    pageSubtitle="Manage verified online customer orders, dispatch status, cold-chain tracking, and payments."
    :breadcrumbs="['Sales' => route('admin.orders.index'), 'Orders' => route('admin.orders.index')]"
>
    <!-- Filter Toolbar -->
    <div class="shop-toolbar" style="margin-bottom: 1.5rem;">
        <div class="search-wrapper" style="max-width: 300px;">
            <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" class="form-control search-input text-sm" placeholder="Search order #, customer...">
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <select class="form-select text-sm" style="width: auto;">
                <option value="">All Fulfillment Statuses</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <select class="form-select text-sm" style="width: auto;">
                <option value="">All Channels</option>
                <option value="online">Online Hub</option>
                <option value="offline">Flagship POS</option>
            </select>

            <button type="button" class="btn btn-secondary btn-sm">
                📥 {{ __('app.actions.export') }}
            </button>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.orders.order_number') }}</th>
                        <th>{{ __('admin.orders.customer') }}</th>
                        <th>{{ __('admin.orders.channel') }}</th>
                        <th>{{ __('admin.orders.status') }}</th>
                        <th>{{ __('admin.orders.payment') }}</th>
                        <th>{{ __('admin.orders.amount') }}</th>
                        <th>{{ __('admin.orders.date') }}</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order['order_number']) }}" class="font-bold text-primary">
                                    {{ $order['order_number'] }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-sm">{{ $order['customer_name'] }}</div>
                                <div class="text-xs text-muted">{{ $order['customer_email'] }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $order['channel'] === 'online' ? 'badge-accent' : 'badge-warning' }} text-xs">
                                    {{ ucfirst($order['channel']) }}
                                </span>
                            </td>
                            <td>
                                <x-status-badge :status="$order['status']" />
                            </td>
                            <td>
                                <span class="badge badge-success text-xs">{{ $order['payment_status'] }}</span>
                            </td>
                            <td class="font-bold">${{ number_format($order['total'], 2) }}</td>
                            <td class="text-xs text-muted">{{ $order['date'] }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.orders.show', $order['order_number']) }}" class="action-btn" title="Manage Order">
                                        👁️
                                    </a>
                                    <a href="{{ route('admin.invoices.show', $order['order_number']) }}" class="action-btn" title="View Tax Invoice">
                                        🧾
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
