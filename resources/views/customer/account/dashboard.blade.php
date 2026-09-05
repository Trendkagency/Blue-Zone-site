<x-layouts.customer :title="__('shop.account.dashboard') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <span class="badge badge-accent text-xs font-bold" style="margin-bottom: 0.5rem;">
                    {{ $customer->tier }}
                </span>
                <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0;">
                    {{ __('shop.account.welcome', ['name' => $customer->name]) }}
                </h1>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'عضوية معتمدة برقم: ' : 'Verified Longevity ID: ' }} 
                    <strong class="font-mono text-primary">BZ-CUST-{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</strong>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('customer.shop') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-bag-shopping mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصفح المتجر' : 'Shop Formulations' }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-box mr-1.5 ml-1.5"></i> {{ __('shop.account.orders') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-check mr-1.5 ml-1.5 text-success"></i> {{ session('success') }}
            </div>
        @endif

        <div class="account-layout">
            <!-- Account Navigation Sidebar -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link active">
                    <i class="fa-solid fa-chart-pie mr-1.5 ml-1.5"></i> {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
                    <i class="fa-solid fa-box mr-1.5 ml-1.5"></i> {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link">
                    <i class="fa-solid fa-file-invoice-dollar mr-1.5 ml-1.5"></i> {{ __('shop.account.invoices') }}
                </a>
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link">
                    <i class="fa-solid fa-location-dot mr-1.5 ml-1.5"></i> {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.wishlist') }}" class="account-nav-link">
                    <i class="fa-solid fa-heart mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'المنتجات المحفوظة' : 'Saved Formulations' }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link">
                    <i class="fa-solid fa-user mr-1.5 ml-1.5"></i> {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link">
                    <i class="fa-solid fa-gear mr-1.5 ml-1.5"></i> {{ __('shop.account.settings') }}
                </a>
                
                <form action="{{ route('customer.auth.logout') }}" method="POST" style="margin-top: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 0.5rem;">
                    @csrf
                    <button type="submit" class="account-nav-link" style="width: 100%; text-align: start; background: none; border: none; cursor: pointer; color: var(--color-danger);">
                        <i class="fa-solid fa-right-from-bracket mr-1.5 ml-1.5"></i> {{ __('app.nav.logout') }}
                    </button>
                </form>
            </aside>

            <!-- Main Content Area -->
            <div>
                <!-- Stat Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                    <div class="card stat-card stat-accent">
                        <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'فئة العضوية الحيوية' : 'Member Tier' }}</div>
                        <div class="stat-value" style="font-size: 1.2rem;">{{ $stats['tier'] }}</div>
                        <div class="stat-footer">{{ app()->getLocale() === 'ar' ? 'أفضل 5% في استمرارية البروتوكول' : 'Top 5% Longevity Cohort' }}</div>
                    </div>

                    <div class="card stat-card stat-success">
                        <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'نقاط الولاء المكتسبة' : 'Longevity Points' }}</div>
                        <div class="stat-value">{{ number_format($stats['loyalty_points']) }} <span class="text-xs font-normal">pts</span></div>
                        <div class="stat-footer">{{ app()->getLocale() === 'ar' ? 'قابلة للخصم التلقائي عند التجديد' : 'Redeemable for refill discounts' }}</div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'إجمالي الطلبات' : 'Total Orders' }}</div>
                        <div class="stat-value">{{ $stats['total_orders'] }}</div>
                        <div class="stat-footer">{{ app()->getLocale() === 'ar' ? 'شحن مبرد معتمد' : 'All fulfilled with cold-chain' }}</div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-label">{{ app()->getLocale() === 'ar' ? 'العناوين المسجلة' : 'Saved Destinations' }}</div>
                        <div class="stat-value">{{ $stats['saved_addresses_count'] }}</div>
                        <div class="stat-footer"><a href="{{ route('customer.account.addresses') }}" class="text-primary font-bold">{{ app()->getLocale() === 'ar' ? 'إدارة العناوين →' : 'Manage addresses →' }}</a></div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border);">
                        <h3 class="card-title" style="font-size: 1.15rem; font-weight: 800; margin: 0;">
                            <i class="fa-solid fa-clock-rotate-left mr-1 ml-1 text-primary"></i>
                            {{ __('shop.account.recent_orders') }}
                        </h3>
                        <a href="{{ route('customer.account.orders') }}" class="text-xs font-bold" style="color: var(--color-primary);">
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
                                @forelse($recentOrders as $order)
                                    @php
                                        $oNum = is_array($order) ? $order['order_number'] : $order->order_number;
                                        $oDate = is_array($order) ? $order['date'] : ($order->date?->format('Y-m-d') ?? now()->toDateString());
                                        $oStatus = is_array($order) ? $order['status'] : $order->status;
                                        $oTotal = is_array($order) ? $order['total'] : $order->total;
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('customer.account.orders.show', $oNum) }}" class="font-bold text-primary">
                                                {{ $oNum }}
                                            </a>
                                        </td>
                                        <td class="text-xs text-muted">{{ $oDate }}</td>
                                        <td>
                                            <x-status-badge :status="$oStatus" />
                                        </td>
                                        <td class="font-bold">${{ number_format((float)$oTotal, 2) }}</td>
                                        <td>
                                            <a href="{{ route('customer.account.orders.show', $oNum) }}" class="btn btn-secondary btn-xs">
                                                {{ __('app.actions.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-muted">
                                            {{ app()->getLocale() === 'ar' ? 'لم تقم بطلب أي منتجات بعد.' : 'No orders placed yet.' }}
                                            <a href="{{ route('customer.shop') }}" class="text-primary font-bold ml-1">{{ app()->getLocale() === 'ar' ? 'ابدأ التسوق الآن' : 'Start shopping now' }}</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
