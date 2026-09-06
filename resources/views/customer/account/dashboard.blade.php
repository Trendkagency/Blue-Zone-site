<x-layouts.customer :title="__('shop.account.dashboard') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.welcome', ['name' => $customer['name']]) }}
        </h1>

        <div class="account-layout">
            <!-- Account Navigation Sidebar -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link active">
                    📊 {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
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
                <a href="{{ route('customer.home') }}" class="account-nav-link" style="color: var(--color-danger); border-top: 1px solid var(--color-border); margin-top: 0.5rem; padding-top: 0.75rem;">
                    🚪 {{ __('app.nav.logout') }}
                </a>
            </aside>

            <!-- Main Content Area -->
            <div>
                <!-- Stat Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                    <div class="card stat-card stat-accent">
                        <div class="stat-label">Member Tier</div>
                        <div class="stat-value" style="font-size: 1.4rem;">{{ $stats['tier'] }}</div>
                        <div class="stat-footer">Top 5% Health Cohort</div>
                    </div>

                    <div class="card stat-card stat-success">
                        <div class="stat-label">Longevity Points</div>
                        <div class="stat-value">{{ $stats['loyalty_points'] }} pts</div>
                        <div class="stat-footer">Redeemable for future refills</div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-label">Active Orders</div>
                        <div class="stat-value">{{ $stats['total_orders'] }}</div>
                        <div class="stat-footer">All fulfilled with cold-chain</div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('shop.account.recent_orders') }}</h3>
                        <a href="{{ route('customer.account.orders') }}" class="text-sm font-bold" style="color: var(--color-primary);">
                            {{ __('app.actions.view_all') }} →
                        </a>
                    </div>

                    <div class="table-responsive" style="border: none; border-radius: 0;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.orders.order_number') }}</th>
                                    <th>{{ __('admin.orders.date') }}</th>
                                    <th>{{ __('admin.orders.status') }}</th>
                                    <th>{{ __('admin.orders.amount') }}</th>
                                    <th>{{ __('app.actions.view') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('customer.account.orders.show', $order['order_number']) }}" class="font-bold text-primary">
                                                {{ $order['order_number'] }}
                                            </a>
                                        </td>
                                        <td>{{ $order['date'] }}</td>
                                        <td>
                                            <x-status-badge :status="$order['status']" />
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
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
