<<<<<<< HEAD
<x-layouts.admin 
    :pageTitle="'Tax Invoice: ' . $order['invoice_number']" 
    pageSubtitle="Official corporate tax invoice and payment reconciliation document."
    :breadcrumbs="['Invoices' => route('admin.invoices.index'), $order['invoice_number'] => route('admin.invoices.show', $order['order_number'])]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            🖨️ {{ __('app.actions.print') }} Invoice
        </button>
        <button type="button" class="btn btn-secondary">
            ⬇️ {{ __('app.actions.download') }} PDF
=======
@php
    $invNum = is_array($order) ? ($order['invoice_number'] ?? $order['order_number']) : ($order->invoice_number ?? $order->order_number);
    $ordNum = is_array($order) ? ($order['order_number'] ?? $invNum) : ($order->order_number ?? $invNum);
    $orderKey = is_object($order) ? ($order->id ?? $order->order_number) : ($order['order_number'] ?? 1);
    $custName = is_array($order) ? ($order['customer_name'] ?? 'Authorized Client') : ($order->customer_name ?? 'Authorized Client');
    $custEmail = is_array($order) ? ($order['customer_email'] ?? '') : ($order->customer_email ?? '');
    $custPhone = is_array($order) ? ($order['customer_phone'] ?? '') : ($order->customer_phone ?? '');
    $ordDate = is_array($order) ? ($order['date'] ?? '') : ($order->date?->format('Y-m-d') ?? '');
    $payMethod = is_array($order) ? ($order['payment_method'] ?? 'Online') : ($order->payment_method ?? 'Online');
    $payStatus = is_array($order) ? ($order['payment_status'] ?? 'paid') : ($order->payment_status ?? 'paid');
    $subtotal = is_array($order) ? ($order['subtotal'] ?? 0) : ($order->subtotal ?? 0);
    $discount = is_array($order) ? ($order['discount'] ?? 0) : ($order->discount ?? 0);
    $tax = is_array($order) ? ($order['tax'] ?? 0) : ($order->tax ?? 0);
    $total = is_array($order) ? ($order['total'] ?? 0) : ($order->total ?? 0);
    $shippingAddr = is_array($order) ? ($order['shipping_address'] ?? []) : ($order->shipping_address ?? []);
    $items = is_array($order) ? ($order['items'] ?? []) : ($order->items ?? []);
@endphp

<x-layouts.admin 
    :pageTitle="'Tax Invoice: ' . $invNum" 
    pageSubtitle="Official corporate tax invoice and payment reconciliation document."
    :breadcrumbs="['Invoices' => route('admin.invoices.index'), $invNum => route('admin.invoices.show', $orderKey)]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.invoices.print', $orderKey) }}" target="_blank" class="btn btn-primary">
            <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> Open Special Design Print Page <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i>
        </a>
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <i class="fa-solid fa-file-invoice mr-1.5 ml-1.5"></i> Quick Print
>>>>>>> origin/main
        </button>
    </x-slot>

    <!-- Printable Invoice Card -->
    <div class="card" style="padding: 3.5rem; max-width: 860px; margin: 0 auto; background: #FFFFFF; color: #031827;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #E2E8F0; padding-bottom: 2rem; margin-bottom: 2rem;">
            <div>
                <img src="{{ asset('assets/logo/logo-main.png') }}" alt="{{ __('app.brand_name') }}" style="height: 48px; margin-bottom: 0.75rem;" onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
                <div style="font-weight: 800; font-size: 1.25rem;">BLUE ZONE Bioceuticals Inc.</div>
                <div style="font-size: 0.8125rem; color: #64748B;">Tax Registration # 31004829100003</div>
<<<<<<< HEAD
=======
                <div style="font-size: 0.8125rem; color: #64748B;">Commercial Record # CR-1010842910</div>
>>>>>>> origin/main
                <div style="font-size: 0.8125rem; color: #64748B;">King Fahd Road, Riyadh, Saudi Arabia</div>
            </div>

            <div style="text-align: end;">
                <h2 style="font-size: 1.75rem; font-weight: 900; color: var(--bz-ocean-blue); margin: 0 0 0.25rem 0;">TAX INVOICE</h2>
<<<<<<< HEAD
                <div style="font-family: monospace; font-weight: 700; font-size: 1rem;">{{ $order['invoice_number'] }}</div>
                <div style="font-size: 0.8125rem; color: #64748B; margin-top: 0.25rem;">Date: {{ $order['date'] }}</div>
                <div style="font-size: 0.8125rem; color: #64748B;">Order Ref: {{ $order['order_number'] }}</div>
=======
                <div style="font-family: monospace; font-weight: 700; font-size: 1rem;">{{ $invNum }}</div>
                <div style="font-size: 0.8125rem; color: #64748B; margin-top: 0.25rem;">Date: {{ $ordDate }}</div>
                <div style="font-size: 0.8125rem; color: #64748B;">Order Ref: {{ $ordNum }}</div>
>>>>>>> origin/main
            </div>
        </div>

        <!-- Billed To -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2.5rem; font-size: 0.875rem;">
            <div>
                <div style="font-weight: 700; text-transform: uppercase; font-size: 0.75rem; color: #64748B; margin-bottom: 0.35rem;">Billed To:</div>
<<<<<<< HEAD
                <div style="font-weight: 800; font-size: 1.05rem;">{{ $order['customer_name'] }}</div>
                <div style="color: #475569;">{{ $order['shipping_address']['street'] }}</div>
                <div style="color: #475569;">{{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['country'] }}</div>
                <div style="color: #64748B;">{{ $order['customer_email'] }} • {{ $order['customer_phone'] }}</div>
=======
                <div style="font-weight: 800; font-size: 1.05rem;">{{ $custName }}</div>
                @if(!empty($shippingAddr))
                    <div style="color: #475569;">{{ is_array($shippingAddr) ? ($shippingAddr['street'] ?? '') : '' }}</div>
                    <div style="color: #475569;">{{ is_array($shippingAddr) ? ($shippingAddr['city'] ?? '') : '' }}, {{ is_array($shippingAddr) ? ($shippingAddr['country'] ?? '') : '' }}</div>
                @endif
                <div style="color: #64748B;">{{ $custEmail }} • {{ $custPhone }}</div>
>>>>>>> origin/main
            </div>

            <div style="text-align: end;">
                <div style="font-weight: 700; text-transform: uppercase; font-size: 0.75rem; color: #64748B; margin-bottom: 0.35rem;">Payment Method:</div>
<<<<<<< HEAD
                <div style="font-weight: 700;">{{ $order['payment_method'] }}</div>
                <span class="badge badge-success" style="margin-top: 0.5rem;">STATUS: {{ $order['payment_status'] }}</span>
=======
                <div style="font-weight: 700;">{{ $payMethod }}</div>
                <span class="badge badge-success" style="margin-top: 0.5rem;">STATUS: {{ strtoupper($payStatus) }}</span>
>>>>>>> origin/main
            </div>
        </div>

        <!-- Items Table -->
        <div style="margin-bottom: 2.5rem;">
            <table class="table" style="border: 1px solid #E2E8F0;">
                <thead>
                    <tr style="background: #F8FAFC;">
                        <th>Description / Formulation</th>
                        <th>SKU</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Gross ($)</th>
                    </tr>
                </thead>
                <tbody>
<<<<<<< HEAD
                    @foreach($order['items'] as $item)
                        <tr>
                            <td>
                                <div style="font-weight: 700;">{{ $item['product_name_en'] }}</div>
                                <div style="font-size: 0.75rem; color: #64748B;">{{ $item['variant_en'] }}</div>
                            </td>
                            <td style="font-family: monospace; font-size: 0.8125rem;">{{ $item['sku'] }}</td>
                            <td>${{ number_format($item['unit_price'], 2) }}</td>
                            <td style="font-weight: 700;">{{ $item['quantity'] }}</td>
                            <td style="font-weight: 700;">${{ number_format($item['total'], 2) }}</td>
                        </tr>
                    @endforeach
=======
                    @forelse($items as $item)
                        @php
                            $iName = is_array($item) ? ($item['product_name_en'] ?? 'Longevity Protocol') : ($item->product_name_en ?? 'Longevity Protocol');
                            $iVar = is_array($item) ? ($item['variant_en'] ?? '') : ($item->variant_en ?? '');
                            $iSku = is_array($item) ? ($item['sku'] ?? 'BZ-SKU') : ($item->sku ?? 'BZ-SKU');
                            $iPrice = is_array($item) ? ($item['unit_price'] ?? 0) : ($item->unit_price ?? 0);
                            $iQty = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                            $iTot = is_array($item) ? ($item['total'] ?? ($iPrice * $iQty)) : ($item->total ?? ($iPrice * $iQty));
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight: 700;">{{ $iName }}</div>
                                @if($iVar)
                                    <div style="font-size: 0.75rem; color: #64748B;">{{ $iVar }}</div>
                                @endif
                            </td>
                            <td style="font-family: monospace; font-size: 0.8125rem;">{{ $iSku }}</td>
                            <td>${{ number_format((float)$iPrice, 2) }}</td>
                            <td style="font-weight: 700;">{{ $iQty }}</td>
                            <td style="font-weight: 700;">${{ number_format((float)$iTot, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94A3B8; padding: 1.5rem;">No items recorded.</td>
                        </tr>
                    @endforelse
>>>>>>> origin/main
                </tbody>
            </table>
        </div>

        <!-- Totals Grid -->
        <div style="display: flex; justify-content: flex-end; margin-bottom: 3rem;">
            <div style="width: 280px; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9375rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Subtotal:</span>
<<<<<<< HEAD
                    <span style="font-weight: 700;">${{ number_format($order['subtotal'], 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #16A34A;">
                    <span>Discount ({{ $order['coupon_code'] ?? 'Promo' }}):</span>
                    <span style="font-weight: 700;">-${{ number_format($order['discount'], 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Standard VAT (15%):</span>
                    <span>${{ number_format($order['tax'], 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 2px solid #031827; padding-top: 0.75rem; font-size: 1.25rem; font-weight: 900;">
                    <span>Total Due:</span>
                    <span style="color: var(--bz-ocean-blue);">${{ number_format($order['total'], 2) }}</span>
=======
                    <span style="font-weight: 700;">${{ number_format((float)$subtotal, 2) }}</span>
                </div>
                @if((float)$discount > 0)
                    <div style="display: flex; justify-content: space-between; color: #16A34A;">
                        <span>Discount:</span>
                        <span style="font-weight: 700;">-${{ number_format((float)$discount, 2) }}</span>
                    </div>
                @endif
                <div style="display: flex; justify-content: space-between;">
                    <span>Standard VAT (15%):</span>
                    <span>${{ number_format((float)$tax, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 2px solid #031827; padding-top: 0.75rem; font-size: 1.25rem; font-weight: 900;">
                    <span>Total Due:</span>
                    <span style="color: var(--bz-ocean-blue);">${{ number_format((float)$total, 2) }}</span>
>>>>>>> origin/main
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #E2E8F0; padding-top: 1.5rem; text-align: center; font-size: 0.75rem; color: #94A3B8;">
            Thank you for trusting BLUE ZONE Bioceuticals for your cellular longevity protocols.
        </div>
    </div>
</x-layouts.admin>
