<x-layouts.customer :title="__('shop.account.orders') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.orders') }}
        </h1>

        <div class="account-layout">
            <!-- Navigation -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link">
                    📊 {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link active">
                    📦 {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link">
                    🧾 {{ __('shop.account.invoices') }}
                </a>
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link">
                    📍 {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link">
                    👤 {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link">
                    ⚙️ {{ __('shop.account.settings') }}
                </a>
            </aside>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Longevity Protocol Orders</h3>
                </div>

                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('admin.orders.order_number') }}</th>
                                <th>{{ __('admin.orders.date') }}</th>
                                <th>Items</th>
                                <th>{{ __('admin.orders.status') }}</th>
                                <th>Payment</th>
                                <th>{{ __('admin.orders.amount') }}</th>
                                <th>{{ __('app.actions.view') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('customer.account.orders.show', $order['order_number']) }}" class="font-bold text-primary">
                                            {{ $order['order_number'] }}
                                        </a>
                                    </td>
                                    <td>{{ $order['date'] }}</td>
                                    <td class="text-sm text-muted">
                                        {{ count($order['items']) }} formulations
                                    </td>
                                    <td>
                                        <x-status-badge :status="$order['status']" />
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $order['payment_status'] }}</span>
                                    </td>
                                    <td class="font-bold">${{ number_format($order['total'], 2) }}</td>
                                    <td>
                                        <a href="{{ route('customer.account.orders.show', $order['order_number']) }}" class="btn btn-secondary btn-sm">
                                            {{ __('app.actions.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-pagination :currentPage="1" :totalPages="1" :totalItems="count($orders)" />
            </div>
        </div>
    </div>
</x-layouts.customer>
