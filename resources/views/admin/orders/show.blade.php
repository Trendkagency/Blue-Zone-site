<x-layouts.admin 
    :pageTitle="'Order #' . $order['order_number']" 
    pageSubtitle="Detailed sales transaction review, packing status, courier AWB tracking, and invoice link."
    :breadcrumbs="['Orders' => route('admin.orders.index'), $order['order_number'] => route('admin.orders.show', $order['order_number'])]"
>
    <x-slot name="actions">
        <x-status-badge :status="$order['status']" />
        <a href="{{ route('admin.invoices.show', $order['order_number']) }}" class="btn btn-outline" target="_blank">
            🧾 Official Invoice ↗
        </a>
    </x-slot>

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem;">
        <!-- Left: Items & Timeline -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Items Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchased Items</h3>
                </div>
                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Formulation</th>
                                <th>SKU</th>
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
                                            <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name_en'] }}" style="width: 44px; height: 44px; border-radius: var(--radius-sm); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                            <div>
                                                <div class="font-bold text-sm">{{ $item['product_name_en'] }}</div>
                                                <div class="text-xs text-muted">{{ $item['variant_en'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-mono text-xs">{{ $item['sku'] }}</td>
                                    <td>${{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="font-bold">{{ $item['quantity'] }}</td>
                                    <td class="font-bold">${{ number_format($item['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fulfillment Audit Timeline -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.orders.timeline') }}</h3>
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
        </div>

        <!-- Right: Customer & Financials -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Customer Dossier -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Customer Overview</h4>
                <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <div class="font-bold text-primary">{{ $order['customer_name'] }}</div>
                    <div>{{ $order['customer_email'] }}</div>
                    <div class="text-muted">{{ $order['customer_phone'] }}</div>
                </div>
            </div>

            <!-- Shipping Destination -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Shipping Destination</h4>
                <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <div class="font-bold">{{ $order['shipping_address']['recipient'] }}</div>
                    <div>{{ $order['shipping_address']['street'] }}</div>
                    <div>{{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['country'] }}</div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Financial Breakdown</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span class="font-bold">${{ number_format($order['subtotal'], 2) }}</span>
                    </div>
                    <div class="summary-row text-success">
                        <span>Discount:</span>
                        <span class="font-bold">-${{ number_format($order['discount'], 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>${{ number_format($order['shipping'], 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>VAT (15%):</span>
                        <span>${{ number_format($order['tax'], 2) }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Gross Total:</span>
                        <span>${{ number_format($order['total'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
