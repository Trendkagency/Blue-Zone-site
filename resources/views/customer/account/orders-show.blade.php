@php
    $oNum = is_array($order) ? $order['order_number'] : $order->order_number;
    $oDate = is_array($order) ? $order['date'] : ($order->date?->format('Y-m-d') ?? now()->toDateString());
    $oStatus = is_array($order) ? $order['status'] : $order->status;
    $oPayMethod = is_array($order) ? $order['payment_method'] : $order->payment_method;
    $oSubtotal = is_array($order) ? $order['subtotal'] : $order->subtotal;
    $oDiscount = is_array($order) ? ($order['discount'] ?? 0) : ($order->discount ?? 0);
    $oTax = is_array($order) ? $order['tax'] : $order->tax;
    $oTotal = is_array($order) ? $order['total'] : $order->total;
    $oInvNum = is_array($order) ? $order['invoice_number'] : $order->invoice_number;
    $items = is_array($order) ? ($order['items'] ?? []) : $order->items;
    $shippingAddr = is_array($order) ? ($order['shipping_address'] ?? []) : ($order->shipping_address ?? []);
@endphp

<x-layouts.customer :title="__('shop.account.order_details', ['number' => $oNum]) . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <div class="breadcrumbs" style="margin-bottom: 1.5rem;">
            <a href="{{ route('customer.account.dashboard') }}" class="breadcrumb-link">{{ __('shop.account.dashboard') }}</a>
            <span class="breadcrumb-separator">›</span>
            <a href="{{ route('customer.account.orders') }}" class="breadcrumb-link">{{ __('shop.account.orders') }}</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">{{ $oNum }}</span>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0 0 0.5rem 0;">
                    {{ app()->getLocale() === 'ar' ? 'طلب رقم: ' : 'Order #' }}{{ $oNum }}
                </h1>
                <div class="text-sm text-muted">
                    {{ app()->getLocale() === 'ar' ? 'تاريخ الطلب: ' : 'Placed on ' }}{{ $oDate }} • {{ $oPayMethod }}
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <x-status-badge :status="$oStatus" />
                <a href="{{ route('customer.account.orders.invoice', $oNum) }}" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'الفاتورة الضريبية الرسمية' : 'Print Official Tax Invoice' }}
                </a>
                <form action="{{ route('customer.account.orders.reorder', $oNum) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-rotate mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'إعادة طلب هذا البروتوكول' : '1-Click Re-order' }}
                    </button>
                </form>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start;">
            <!-- Left Column: Visual Timeline & Formulations -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Visual Order Milestone Tracker (Requirement 12: Order Status) -->
                <div class="card" style="padding: 1.75rem;">
                    <div class="card-header" style="padding: 0 0 1.25rem 0; border-bottom: 1px solid var(--color-border); margin-bottom: 1.5rem;">
                        <h3 class="card-title" style="font-size: 1.15rem; font-weight: 800; margin: 0;">
                            <i class="fa-solid fa-timeline text-primary mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'مسار التنفيذ ومراحل التجهيز اللوجستي' : 'Cold-Chain Fulfillment & Milestone Progress' }}
                        </h3>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1.5rem; position: relative;">
                        @foreach($timeline as $step)
                            <div style="display: flex; gap: 1.25rem; align-items: flex-start; position: relative;">
                                <!-- Icon Circle -->
                                <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: {{ $step['completed'] ? '#10B981' : 'var(--color-bg-subtle)' }}; color: {{ $step['completed'] ? '#FFFFFF' : 'var(--color-text-muted)' }}; border: 2px solid {{ $step['completed'] ? '#10B981' : 'var(--color-border)' }};">
                                    <i class="fa-solid {{ $step['icon'] ?? 'fa-circle' }} text-sm"></i>
                                </div>

                                <!-- Text Details -->
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="font-bold text-sm" style="color: {{ $step['completed'] ? 'var(--color-text-primary)' : 'var(--color-text-muted)' }};">
                                            {{ $step['status'] }}
                                        </div>
                                        <span class="text-xs text-muted font-mono">{{ $step['timestamp'] }}</span>
                                    </div>
                                    <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                                        {{ $step['note'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Items Table Card -->
                <div class="card">
                    <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border);">
                        <h3 class="card-title" style="font-size: 1.15rem; font-weight: 800; margin: 0;">
                            <i class="fa-solid fa-pills text-primary mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'التركيبات الطبية المعتمدة في الطلب' : 'Formulations & Protocols in Order' }}
                        </h3>
                    </div>

                    <div class="table-responsive" style="border: none; border-radius: 0;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ app()->getLocale() === 'ar' ? 'التركيبة' : 'Formulation' }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Qty' }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    @php
                                        $iName = is_array($item) ? (app()->getLocale() === 'ar' ? ($item['product_name_ar'] ?? $item['product_name_en']) : $item['product_name_en']) : (app()->getLocale() === 'ar' ? ($item->product_name_ar ?? $item->product_name_en) : $item->product_name_en);
                                        $iVar = is_array($item) ? ($item['variant_en'] ?? 'Standard Pack') : ($item->variant_en ?? 'Standard Pack');
                                        $iPrice = is_array($item) ? $item['unit_price'] : $item->unit_price;
                                        $iQty = is_array($item) ? $item['quantity'] : $item->quantity;
                                        $iTotal = is_array($item) ? $item['total'] : $item->total;
                                        $iImg = is_array($item) ? ($item['image'] ?? 'assets/products/blue-mind.jpg') : ($item->image ?? 'assets/products/blue-mind.jpg');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <img src="{{ asset($iImg) }}" alt="{{ $iName }}" style="width: 48px; height: 48px; border-radius: var(--radius-sm); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                                <div>
                                                    <div class="font-bold text-sm">{{ $iName }}</div>
                                                    <div class="text-xs text-muted">{{ $iVar }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format((float)$iPrice, 2) }}</td>
                                        <td class="font-bold">{{ $iQty }}</td>
                                        <td class="font-bold">${{ number_format((float)$iTotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Shipping & Financial Summary -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Destination Card -->
                <div class="card" style="padding: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 800; margin: 0 0 1rem 0; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                        <i class="fa-solid fa-location-dot text-primary mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'وجهة التسليم والشحن' : 'Delivery Destination' }}
                    </h4>
                    <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.35rem;">
                        <div class="font-bold text-primary">{{ $shippingAddr['recipient'] ?? ($order['customer_name'] ?? 'Recipient') }}</div>
                        <div class="text-secondary">{{ $shippingAddr['street'] ?? 'King Fahd District, Level 24' }}</div>
                        <div class="text-secondary">{{ $shippingAddr['city'] ?? 'Riyadh' }}, {{ $shippingAddr['country'] ?? 'Saudi Arabia' }}</div>
                        <div class="text-muted text-xs font-mono">{{ $shippingAddr['phone'] ?? ($order['customer_phone'] ?? '+966 50 123 4567') }}</div>
                    </div>
                </div>

                <!-- Financial Breakdown Card -->
                <div class="card" style="padding: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 800; margin: 0 0 1rem 0; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                        <i class="fa-solid fa-receipt text-primary mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'الملخص المالي والضريبي' : 'Financial Breakdown' }}
                    </h4>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">{{ __('admin.pos.subtotal') }}:</span>
                            <span class="font-bold">${{ number_format((float)$oSubtotal, 2) }}</span>
                        </div>

                        @if((float)$oDiscount > 0)
                            <div style="display: flex; justify-content: space-between; color: #10B981;">
                                <span>{{ app()->getLocale() === 'ar' ? 'خصم العضوية:' : 'Member Discount:' }}</span>
                                <span class="font-bold">-${{ number_format((float)$oDiscount, 2) }}</span>
                            </div>
                        @endif

                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">{{ __('admin.invoices.vat_breakdown') }}:</span>
                            <span>${{ number_format((float)$oTax, 2) }}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'الشحن المبرد:' : 'Cold-Chain Shipping:' }}</span>
                            <span class="text-success font-bold">{{ app()->getLocale() === 'ar' ? 'مجاني' : 'Complimentary' }}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--color-border); padding-top: 0.75rem; font-size: 1.1rem; font-weight: 800;">
                            <span>{{ __('admin.pos.total_payable') }}:</span>
                            <span class="text-primary font-black">${{ number_format((float)$oTotal, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customer.account.orders.invoice', $oNum) }}" target="_blank" class="btn btn-secondary btn-sm" style="width: 100%; margin-top: 1.25rem;">
                        <i class="fa-solid fa-file-invoice mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'عرض وطباعة الفاتورة' : 'View Printable Tax Invoice' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
