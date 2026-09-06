<x-layouts.customer :title="__('shop.account.orders') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
<<<<<<< HEAD
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.orders') }}
        </h1>
=======
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0;">
                    {{ __('shop.account.orders') }}
                </h1>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'تتبع مسارات الشحن المبرد وتفاصيل طلبيات بروتوكولات طول العمر.' : 'Track active cold-chain fulfillment, order milestones, and re-order clinical protocols.' }}
                </div>
            </div>
            <a href="{{ route('customer.shop') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'طلب تركيبة جديدة' : 'Order New Formulation' }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-check mr-1.5 ml-1.5 text-success"></i> {{ session('success') }}
            </div>
        @endif
>>>>>>> origin/main

        <div class="account-layout">
            <!-- Navigation -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link">
<<<<<<< HEAD
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
=======
                    <i class="fa-solid fa-chart-pie mr-1.5 ml-1.5"></i> {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link active">
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

            <!-- Orders Table Area -->
            <div>
                <!-- Status Filter Pills & Search -->
                <div class="card" style="padding: 1rem 1.5rem; margin-bottom: 1.5rem;">
                    <form method="GET" action="{{ route('customer.account.orders') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ route('customer.account.orders', ['status' => 'all', 'search' => $search]) }}" 
                               class="btn btn-xs {{ $selectedStatus === 'all' ? 'btn-neutral active' : 'btn-secondary' }}">
                                {{ app()->getLocale() === 'ar' ? 'جميع الطلبات' : 'All Orders' }}
                            </a>
                            <a href="{{ route('customer.account.orders', ['status' => 'processing', 'search' => $search]) }}" 
                               class="btn btn-xs {{ $selectedStatus === 'processing' ? 'btn-neutral active' : 'btn-secondary' }}">
                                {{ app()->getLocale() === 'ar' ? 'قيد التجهيز' : 'Processing' }}
                            </a>
                            <a href="{{ route('customer.account.orders', ['status' => 'shipped', 'search' => $search]) }}" 
                               class="btn btn-xs {{ $selectedStatus === 'shipped' ? 'btn-neutral active' : 'btn-secondary' }}">
                                {{ app()->getLocale() === 'ar' ? 'تم الشحن' : 'Shipped' }}
                            </a>
                            <a href="{{ route('customer.account.orders', ['status' => 'delivered', 'search' => $search]) }}" 
                               class="btn btn-xs {{ $selectedStatus === 'delivered' ? 'btn-neutral active' : 'btn-secondary' }}">
                                {{ app()->getLocale() === 'ar' ? 'تم التسليم' : 'Delivered' }}
                            </a>
                        </div>

                        <div class="search-wrapper" style="max-width: 240px; flex: 1;">
                            <input type="text" name="search" value="{{ $search }}" class="form-control text-xs search-input" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم الطلب...' : 'Search Order #...' }}">
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border);">
                        <h3 class="card-title" style="font-size: 1.15rem; font-weight: 800; margin: 0;">
                            <i class="fa-solid fa-boxes-stacked text-primary mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'سجل طلبيات بروتوكول طول العمر' : 'All Longevity Protocol Orders' }}
                        </h3>
                    </div>

                    <div class="table-responsive" style="border: none; border-radius: 0;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.orders.order_number') }}</th>
                                    <th>{{ __('admin.orders.date') }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'التركيبات' : 'Items' }}</th>
                                    <th>{{ __('admin.orders.status') }}</th>
                                    <th>{{ __('admin.orders.amount') }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    @php
                                        $oNum = is_array($order) ? $order['order_number'] : $order->order_number;
                                        $oDate = is_array($order) ? $order['date'] : ($order->date?->format('Y-m-d') ?? now()->toDateString());
                                        $oStatus = is_array($order) ? $order['status'] : $order->status;
                                        $oTotal = is_array($order) ? $order['total'] : $order->total;
                                        $itemsCount = is_array($order) ? count($order['items'] ?? []) : $order->items->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('customer.account.orders.show', $oNum) }}" class="font-bold text-primary">
                                                {{ $oNum }}
                                            </a>
                                        </td>
                                        <td class="text-xs text-muted">{{ $oDate }}</td>
                                        <td class="text-xs">
                                            <span class="badge badge-neutral">
                                                {{ $itemsCount }} {{ app()->getLocale() === 'ar' ? 'تركيبات' : 'items' }}
                                            </span>
                                        </td>
                                        <td>
                                            <x-status-badge :status="$oStatus" />
                                        </td>
                                        <td class="font-bold">${{ number_format((float)$oTotal, 2) }}</td>
                                        <td>
                                            <div class="table-actions" style="display: flex; gap: 0.35rem;">
                                                <a href="{{ route('customer.account.orders.show', $oNum) }}" class="btn btn-secondary btn-xs" title="View Details">
                                                    <i class="fa-solid fa-eye mr-1 ml-1"></i> {{ __('app.actions.view') }}
                                                </a>
                                                <form action="{{ route('customer.account.orders.reorder', $oNum) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-xs" title="{{ app()->getLocale() === 'ar' ? 'إعادة طلب هذه التركيبات' : 'Re-order formulations' }}">
                                                        <i class="fa-solid fa-rotate mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة الطلب' : 'Re-order' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8 text-muted">
                                            <i class="fa-solid fa-box-open fa-2x mb-2" style="display: block; opacity: 0.4;"></i>
                                            {{ app()->getLocale() === 'ar' ? 'لا توجد طلبات مطابقة.' : 'No orders found matching criteria.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class))
                        <x-pagination :currentPage="$orders->currentPage()" :totalPages="$orders->lastPage()" :totalItems="$orders->total()" />
                    @endif
                </div>
>>>>>>> origin/main
            </div>
        </div>
    </div>
</x-layouts.customer>
