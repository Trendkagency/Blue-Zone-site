<x-layouts.customer :title="__('shop.account.invoices') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.invoices') }}
        </h1>

        <div class="account-layout">
            <!-- Navigation -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link">
<<<<<<< HEAD
                    📊 {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
                    📦 {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link active">
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
=======
                    <i class="fa-solid fa-chart-pie mr-1.5 ml-1.5"></i> {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
                    <i class="fa-solid fa-box mr-1.5 ml-1.5"></i> {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link active">
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
>>>>>>> origin/main
            </aside>

            <!-- Invoices List Card -->
            <div class="card">
<<<<<<< HEAD
                <div class="card-header">
                    <h3 class="card-title">Tax Invoices & Official Receipts</h3>
=======
                <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title" style="font-size: 1.15rem; font-weight: 800; margin: 0;">
                        <i class="fa-solid fa-receipt text-primary mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'الفواتير الضريبية الرسمية المعتمدة' : 'Tax Invoices & Official Receipts' }}
                    </h3>
                    <span class="text-xs text-muted font-bold">15% VAT Compliant</span>
>>>>>>> origin/main
                </div>

                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
<<<<<<< HEAD
                                <th>Invoice #</th>
                                <th>Order Ref</th>
                                <th>Issue Date</th>
                                <th>Tax Rate</th>
                                <th>Total Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td class="font-bold font-mono">{{ $order['invoice_number'] }}</td>
                                    <td>
                                        <a href="{{ route('customer.account.orders.show', $order['order_number']) }}" class="text-primary font-bold">
                                            {{ $order['order_number'] }}
                                        </a>
                                    </td>
                                    <td>{{ $order['date'] }}</td>
                                    <td>15% VAT</td>
                                    <td class="font-bold">${{ number_format($order['total'], 2) }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ $order['payment_status'] }}</span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()" title="Print Receipt">
                                                🖨️ {{ __('app.actions.print') }}
                                            </button>
                                            <button type="button" class="btn btn-ghost btn-sm" title="Download PDF">
                                                ⬇️ {{ __('app.actions.download') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
=======
                                <th>{{ __('admin.invoices.invoice_number') }}</th>
                                <th>{{ __('admin.orders.order_number') }}</th>
                                <th>{{ __('admin.orders.date') }}</th>
                                <th>{{ __('admin.invoices.vat_breakdown') }}</th>
                                <th>{{ __('admin.invoices.grand_total') }}</th>
                                <th>{{ __('admin.orders.payment') }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                @php
                                    $invNum = is_array($order) ? $order['invoice_number'] : $order->invoice_number;
                                    $oNum = is_array($order) ? $order['order_number'] : $order->order_number;
                                    $oDate = is_array($order) ? $order['date'] : ($order->date?->format('Y-m-d') ?? now()->toDateString());
                                    $oTotal = is_array($order) ? $order['total'] : $order->total;
                                    $oPayStatus = is_array($order) ? $order['payment_status'] : $order->payment_status;
                                @endphp
                                <tr>
                                    <td class="font-bold font-mono text-primary text-xs">{{ $invNum ?? ('INV-' . $oNum) }}</td>
                                    <td>
                                        <a href="{{ route('customer.account.orders.show', $oNum) }}" class="text-primary font-bold">
                                            {{ $oNum }}
                                        </a>
                                    </td>
                                    <td class="text-xs text-muted">{{ $oDate }}</td>
                                    <td class="text-xs font-semibold">15% VAT</td>
                                    <td class="font-bold">${{ number_format((float)$oTotal, 2) }}</td>
                                    <td>
                                        <span class="badge badge-success text-xs font-bold">{{ $oPayStatus }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.account.orders.invoice', $oNum) }}" target="_blank" class="btn btn-secondary btn-xs" title="Print Official Tax Invoice">
                                            <i class="fa-solid fa-print mr-1 ml-1"></i> {{ __('app.actions.print') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-muted">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد فواتير ضريبية صادرة حتى الآن.' : 'No invoices issued yet.' }}
                                    </td>
                                </tr>
                            @endforelse
>>>>>>> origin/main
                        </tbody>
                    </table>
                </div>

<<<<<<< HEAD
                <x-pagination :currentPage="1" :totalPages="1" :totalItems="count($orders)" />
=======
                @if(is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class))
                    <x-pagination :currentPage="$orders->currentPage()" :totalPages="$orders->lastPage()" :totalItems="$orders->total()" />
                @endif
>>>>>>> origin/main
            </div>
        </div>
    </div>
</x-layouts.customer>
