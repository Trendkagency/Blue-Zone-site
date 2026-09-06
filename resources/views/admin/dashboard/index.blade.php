<x-layouts.admin 
    :pageTitle="__('admin.menu.dashboard')" 
<<<<<<< HEAD
    pageSubtitle="Real-time multi-channel overview for online fulfillment and offline flagship boutique."
=======
    :pageSubtitle="__('admin.dashboard.subtitle')"
>>>>>>> origin/main
>
    <!-- KPI Overview Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card stat-card stat-accent">
            <div class="stat-header">
                <span class="stat-label">{{ __('admin.kpi.total_revenue') }}</span>
<<<<<<< HEAD
                <span class="stat-icon">💰</span>
            </div>
            <div class="stat-value">${{ number_format($kpi['total_sales'], 2) }}</div>
            <div class="stat-footer text-success font-bold">
                ▲ {{ $kpi['growth_rate'] }} vs last month
=======
                <span class="stat-icon"><i class="fa-solid fa-wallet text-accent"></i></span>
            </div>
            <div class="stat-value">${{ number_format($kpi['total_sales'], 2) }}</div>
            <div class="stat-footer text-success font-bold">
                <i class="fa-solid fa-arrow-trend-up mr-1 ml-1"></i> {{ $kpi['growth_rate'] }} {{ __('admin.dashboard.vs_last_month') }}
>>>>>>> origin/main
            </div>
        </div>

        <div class="card stat-card stat-success">
            <div class="stat-header">
                <span class="stat-label">{{ __('admin.kpi.online_channel') }}</span>
<<<<<<< HEAD
                <span class="stat-icon">🌐</span>
            </div>
            <div class="stat-value">${{ number_format($kpi['online_sales'], 2) }}</div>
            <div class="stat-footer text-muted">
                71.6% of gross sales
=======
                <span class="stat-icon"><i class="fa-solid fa-globe text-success"></i></span>
            </div>
            <div class="stat-value">${{ number_format($kpi['online_sales'], 2) }}</div>
            <div class="stat-footer text-muted">
                71.6% {{ __('admin.dashboard.pct_of_gross') }}
>>>>>>> origin/main
            </div>
        </div>

        <div class="card stat-card stat-warning">
            <div class="stat-header">
                <span class="stat-label">{{ __('admin.kpi.offline_channel') }}</span>
<<<<<<< HEAD
                <span class="stat-icon">🏬</span>
            </div>
            <div class="stat-value">${{ number_format($kpi['offline_sales'], 2) }}</div>
            <div class="stat-footer text-muted">
                28.4% of gross sales
=======
                <span class="stat-icon"><i class="fa-solid fa-store text-warning"></i></span>
            </div>
            <div class="stat-value">${{ number_format($kpi['offline_sales'], 2) }}</div>
            <div class="stat-footer text-muted">
                28.4% {{ __('admin.dashboard.pct_of_gross') }}
>>>>>>> origin/main
            </div>
        </div>

        <div class="card stat-card stat-danger">
            <div class="stat-header">
                <span class="stat-label">{{ __('admin.kpi.low_stock_alerts') }}</span>
<<<<<<< HEAD
                <span class="stat-icon">⚠️</span>
            </div>
            <div class="stat-value">{{ $kpi['low_stock_count'] }} SKUs</div>
            <div class="stat-footer text-danger font-bold">
                Requires replenishment transfer
=======
                <span class="stat-icon"><i class="fa-solid fa-triangle-exclamation text-danger"></i></span>
            </div>
            <div class="stat-value">{{ $kpi['low_stock_count'] }} SKUs</div>
            <div class="stat-footer text-danger font-bold">
                {{ __('admin.dashboard.replenishment_required') }}
>>>>>>> origin/main
            </div>
        </div>
    </div>

    <!-- Second Row: Recent Orders & Stock Movements -->
    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <!-- Recent Orders Card -->
        <div class="card">
            <div class="card-header">
<<<<<<< HEAD
                <h3 class="card-title">Recent Commerce Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                    {{ __('app.actions.view_all') }} →
=======
                <h3 class="card-title">{{ __('admin.dashboard.recent_orders') }}</h3>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                    {{ __('app.actions.view_all') }} <i class="fa-solid fa-arrow-right mr-1 ml-1"></i>
>>>>>>> origin/main
                </a>
            </div>

            <div class="table-responsive" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                        <tr>
<<<<<<< HEAD
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Channel</th>
                            <th>Status</th>
                            <th>Total</th>
=======
                            <th>{{ __('admin.orders.order_number') }}</th>
                            <th>{{ __('admin.orders.customer') }}</th>
                            <th>{{ __('admin.orders.channel') }}</th>
                            <th>{{ __('admin.orders.status') }}</th>
                            <th>{{ __('admin.orders.amount') }}</th>
>>>>>>> origin/main
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order['order_number']) }}" class="font-bold text-primary">
                                        {{ $order['order_number'] }}
                                    </a>
                                </td>
                                <td>
                                    <div class="font-bold text-sm">{{ $order['customer_name'] }}</div>
                                    <div class="text-xs text-muted">{{ $order['date'] }}</div>
                                </td>
                                <td>
                                    @if($order['channel'] === 'online')
<<<<<<< HEAD
                                        <span class="badge badge-accent">Online</span>
                                    @else
                                        <span class="badge badge-warning">Boutique POS</span>
=======
                                        <span class="badge badge-accent">{{ __('admin.reports.online_only') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ __('admin.reports.pos_only') }}</span>
>>>>>>> origin/main
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge :status="$order['status']" />
                                </td>
                                <td class="font-bold">${{ number_format($order['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Stock Movements Audit -->
        <div class="card">
            <div class="card-header">
<<<<<<< HEAD
                <h3 class="card-title">Live Stock Movements Audit</h3>
                <a href="{{ route('admin.inventory.history') }}" class="btn btn-secondary btn-sm">
                    Audit Log →
=======
                <h3 class="card-title">{{ __('admin.dashboard.stock_movements_audit') }}</h3>
                <a href="{{ route('admin.inventory.history') }}" class="btn btn-secondary btn-sm">
                    {{ __('admin.dashboard.audit_log_btn') }}
>>>>>>> origin/main
                </a>
            </div>

            <div class="table-responsive" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                        <tr>
<<<<<<< HEAD
                            <th>Action</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Staff User</th>
=======
                            <th>{{ __('admin.inventory.movement_type') }}</th>
                            <th>{{ __('admin.products.fields.name_' . app()->getLocale()) }}</th>
                            <th>{{ __('admin.inventory.transfer_qty') }}</th>
                            <th>{{ __('admin.inventory.logged_user') }}</th>
>>>>>>> origin/main
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentMovements as $mov)
                            <tr>
                                <td>
                                    <span class="badge badge-neutral text-xs">{{ $mov['movement_type'] }}</span>
                                </td>
                                <td>
<<<<<<< HEAD
                                    <div class="font-bold text-sm">{{ $mov['product_name_en'] }}</div>
=======
                                    <div class="font-bold text-sm">{{ app()->getLocale() == 'ar' ? ($mov['product_name_ar'] ?? $mov['product_name_en']) : $mov['product_name_en'] }}</div>
>>>>>>> origin/main
                                    <div class="text-xs text-muted">{{ $mov['date'] }} {{ $mov['time'] }}</div>
                                </td>
                                <td class="font-bold {{ $mov['quantity'] < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $mov['quantity'] > 0 ? '+' : '' }}{{ $mov['quantity'] }}
                                </td>
                                <td class="text-xs text-muted">
                                    {{ $mov['user'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Action Launchpad -->
    <div class="card" style="padding: 1.5rem; background: var(--color-bg-subtle);">
<<<<<<< HEAD
        <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">Quick Operational Actions</h4>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('admin.offline-sales.create') }}" class="btn btn-primary">
                🛒 Open POS Terminal (Offline Sale)
            </a>
            <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-secondary">
                🔄 Inter-Location Stock Transfer
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-secondary">
                ➕ New Product Formulation
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                📊 Financial & Inventory Reports
=======
        <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">{{ __('admin.dashboard.quick_actions') }}</h4>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('admin.offline-sales.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-cash-register mr-1.5 ml-1.5"></i> {{ __('admin.dashboard.open_pos') }}
            </a>
            <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-right-arrow-left mr-1.5 ml-1.5"></i> {{ __('admin.dashboard.stock_transfer') }}
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-secondary">
                <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('admin.dashboard.new_formulation') }}
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-chart-pie mr-1.5 ml-1.5"></i> {{ __('admin.dashboard.financial_reports') }}
>>>>>>> origin/main
            </a>
        </div>
    </div>
</x-layouts.admin>
