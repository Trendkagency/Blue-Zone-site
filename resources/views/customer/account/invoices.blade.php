<x-layouts.customer :title="__('shop.account.invoices') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.invoices') }}
        </h1>

        <div class="account-layout">
            <!-- Navigation -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link">
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
            </aside>

            <!-- Invoices List Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tax Invoices & Official Receipts</h3>
                </div>

                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
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
                        </tbody>
                    </table>
                </div>

                <x-pagination :currentPage="1" :totalPages="1" :totalItems="count($orders)" />
            </div>
        </div>
    </div>
</x-layouts.customer>
