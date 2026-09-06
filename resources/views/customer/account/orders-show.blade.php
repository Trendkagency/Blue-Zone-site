<x-layouts.customer :title="__('shop.account.order_details', ['number' => $order['order_number']]) . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <div class="breadcrumbs" style="margin-bottom: 1.5rem;">
            <a href="{{ route('customer.account.dashboard') }}" class="breadcrumb-link">{{ __('shop.account.dashboard') }}</a>
            <span class="breadcrumb-separator">›</span>
            <a href="{{ route('customer.account.orders') }}" class="breadcrumb-link">{{ __('shop.account.orders') }}</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">{{ $order['order_number'] }}</span>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0 0 0.5rem 0;">
                    Order #{{ $order['order_number'] }}
                </h1>
                <div class="text-sm text-muted">
                    Placed on {{ $order['date'] }} • Paid via {{ $order['payment_method'] }}
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <x-status-badge :status="$order['status']" />
                <a href="{{ route('customer.account.invoices') }}" class="btn btn-outline btn-sm">
                    🧾 View Tax Invoice
                </a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start;">
            <!-- Left Column: Items & Timeline -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Timeline Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('shop.account.timeline') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline-track">
                            @foreach($order['timeline'] as $event)
                                <div class="timeline-item completed">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">{{ $event['status'] }}</div>
                                        <div class="text-xs text-muted">{{ $event['timestamp'] }}</div>
                                        <div class="text-sm text-secondary">{{ $event['note'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Items Table Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order Items</h3>
                    </div>

                    <div class="table-responsive" style="border: none; border-radius: 0;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order['items'] as $item)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name_en'] }}" style="width: 48px; height: 48px; border-radius: var(--radius-sm); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                                <div>
                                                    <div class="font-bold text-sm">
                                                        {{ app()->getLocale() === 'ar' ? ($item['product_name_ar'] ?? $item['product_name_en']) : $item['product_name_en'] }}
                                                    </div>
                                                    <div class="text-xs text-muted">
                                                        {{ app()->getLocale() === 'ar' ? ($item['variant_ar'] ?? $item['variant_en']) : $item['variant_en'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item['unit_price'], 2) }}</td>
                                        <td class="font-bold">{{ $item['quantity'] }}</td>
                                        <td class="font-bold">${{ number_format($item['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Shipping & Financial Summary -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-sm">Delivery Destination</h4>
                    </div>
                    <div class="card-body text-sm" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <div class="font-bold">{{ $order['shipping_address']['recipient'] }}</div>
                        <div class="text-secondary">{{ $order['shipping_address']['street'] }}</div>
                        <div class="text-secondary">{{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['country'] }}</div>
                        <div class="text-muted">Postal: {{ $order['shipping_address']['postal_code'] }}</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-sm">Payment Breakdown</h4>
                    </div>
                    <div class="card-body" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>${{ number_format($order['subtotal'], 2) }}</span>
                        </div>
                        <div class="summary-row text-success">
                            <span>Discount ({{ $order['coupon_code'] ?? 'Promo' }})</span>
                            <span>-${{ number_format($order['discount'], 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Insured Shipping</span>
                            <span>${{ number_format($order['shipping'], 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>VAT (15%)</span>
                            <span>${{ number_format($order['tax'], 2) }}</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total Paid</span>
                            <span>${{ number_format($order['total'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
