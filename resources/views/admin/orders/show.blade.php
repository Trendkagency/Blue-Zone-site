@php
    $ordNum = is_array($order) ? ($order['order_number'] ?? 'BZ-000') : ($order->order_number ?? 'BZ-000');
    $orderKey = is_object($order) ? ($order->id ?? $order->order_number) : ($order['order_number'] ?? 1);
    $status = is_array($order) ? ($order['status'] ?? 'pending') : ($order->status ?? 'pending');
    $custName = is_array($order) ? ($order['customer_name'] ?? 'Authorized Client') : ($order->customer_name ?? 'Authorized Client');
    $custEmail = is_array($order) ? ($order['customer_email'] ?? '') : ($order->customer_email ?? '');
    $custPhone = is_array($order) ? ($order['customer_phone'] ?? '') : ($order->customer_phone ?? '');
    $subtotal = is_array($order) ? ($order['subtotal'] ?? 0) : ($order->subtotal ?? 0);
    $discount = is_array($order) ? ($order['discount'] ?? 0) : ($order->discount ?? 0);
    $shipping = is_array($order) ? ($order['shipping'] ?? 0) : ($order->shipping ?? 0);
    $tax = is_array($order) ? ($order['tax'] ?? 0) : ($order->tax ?? 0);
    $total = is_array($order) ? ($order['total'] ?? 0) : ($order->total ?? 0);
    $shippingAddr = is_array($order) ? ($order['shipping_address'] ?? []) : ($order->shipping_address ?? []);
    $items = is_array($order) ? ($order['items'] ?? []) : ($order->items ?? []);
    $timeline = is_array($order) ? ($order['timeline'] ?? []) : ($order->timeline ?? []);
@endphp

<x-layouts.admin 
    :pageTitle="(app()->getLocale() == 'ar' ? 'تفاصيل الطلب: ' : 'Order #') . $ordNum" 
    :pageSubtitle="__('admin.orders.subtitle')"
    :breadcrumbs="[__('admin.menu.sales') => route('admin.orders.index'), $ordNum => route('admin.orders.show', $orderKey)]"
>
    <x-slot name="actions">
        <x-status-badge :status="$status" />
        <a href="{{ route('admin.invoices.print', $orderKey) }}" class="btn btn-primary" target="_blank">
            <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> {{ __('admin.invoices.print_action') }} <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i>
        </a>
        <a href="{{ route('admin.invoices.show', $orderKey) }}" class="btn btn-outline" target="_blank">
            <i class="fa-solid fa-file-invoice-dollar mr-1.5 ml-1.5"></i> {{ __('admin.invoices.tax_invoice') }}
        </a>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-circle-check mr-1 text-success"></i> {{ session('status') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem;">
        <!-- Left: Items & Timeline -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Items Table -->
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">{{ app()->getLocale() == 'ar' ? 'التركيبات الحيوية المشتراة' : 'Purchased Formulations' }} ({{ count($items) }})</h3>
                    <span class="badge badge-secondary" style="font-size: 0.75rem;">
                        {{ app()->getLocale() == 'ar' ? 'تغليف سريري معتمد GMP' : 'Verified GMP Packaging' }}
                    </span>
                </div>
                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'التركيبة' : 'Formulation' }}</th>
                                <th>{{ __('admin.products.fields.sku') }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الكمية' : 'Qty' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'المجموع' : 'Total' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                @php
                                    $iName = is_array($item) 
                                        ? (app()->getLocale() == 'ar' ? ($item['product_name_ar'] ?? $item['product_name_en'] ?? 'تركيبة حيوية') : ($item['product_name_en'] ?? 'Cellular Compound'))
                                        : (app()->getLocale() == 'ar' ? ($item->product_name_ar ?? $item->product_name_en ?? 'تركيبة حيوية') : ($item->product_name_en ?? 'Cellular Compound'));
                                    $iVar = is_array($item) ? ($item['variant_en'] ?? '') : ($item->variant_en ?? '');
                                    $iSku = is_array($item) ? ($item['sku'] ?? 'BZ-SKU') : ($item->sku ?? 'BZ-SKU');
                                    $iPrice = is_array($item) ? ($item['unit_price'] ?? 0) : ($item->unit_price ?? 0);
                                    $iQty = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                                    $iTot = is_array($item) ? ($item['total'] ?? ($iPrice * $iQty)) : ($item->total ?? ($iPrice * $iQty));
                                    $iImg = is_array($item) ? ($item['image'] ?? 'image.jpg') : ($item->image ?? 'image.jpg');
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <img src="{{ asset($iImg) }}" alt="{{ $iName }}" style="width: 44px; height: 44px; border-radius: var(--radius-sm); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                            <div>
                                                <div class="font-bold text-sm">{{ $iName }}</div>
                                                <div class="text-xs text-muted">{{ $iVar }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-mono text-xs">{{ $iSku }}</td>
                                    <td>${{ number_format((float)$iPrice, 2) }}</td>
                                    <td class="font-bold">{{ $iQty }}</td>
                                    <td class="font-bold">${{ number_format((float)$iTot, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #94A3B8; padding: 2rem;">
                                        {{ __('app.empty.description') }}
                                    </td>
                                </tr>
                            @endforelse
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
                        @if(!empty($timeline))
                            @foreach($timeline as $event)
                                <div class="timeline-item completed">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">{{ is_array($event) ? ($event['status'] ?? '') : ($event->status ?? '') }}</div>
                                        <div class="text-xs text-muted">{{ is_array($event) ? ($event['timestamp'] ?? '') : ($event->timestamp ?? '') }}</div>
                                        <div class="text-sm text-secondary">{{ is_array($event) ? ($event['note'] ?? '') : ($event->note ?? '') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="timeline-item completed">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-title">{{ app()->getLocale() == 'ar' ? 'تم إنشاء الطلب وتوثيقه بنجاح' : 'Order Created & Verified' }}</div>
                                    <div class="text-xs text-muted">{{ now()->toFormattedDateString() }}</div>
                                    <div class="text-sm text-secondary">
                                        {{ app()->getLocale() == 'ar' ? 'تم تسجيل المعاملة في قاعدة بيانات ERP المركزية عبر جلسة دفع موثقة.' : 'Logged into central ERP database via authenticated checkout session.' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Actions, Customer & Financials -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Real-time Status Update Action -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">
                    {{ __('admin.orders.update_status') }}
                </h4>
                <form action="{{ route('admin.orders.update-status', $orderKey) }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label text-xs font-bold" style="text-transform: uppercase; color: var(--color-text-muted);">
                            {{ __('admin.orders.current_status') }}
                        </label>
                        <select name="status" class="form-control" style="width: 100%;">
                            <option value="pending" {{ strtolower($status) === 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قيد الانتظار (Pending)' : 'Pending' }}</option>
                            <option value="processing" {{ strtolower($status) === 'processing' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قيد التجهيز (Processing)' : 'Processing' }}</option>
                            <option value="shipped" {{ strtolower($status) === 'shipped' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'تم الشحن (Shipped)' : 'Shipped' }}</option>
                            <option value="delivered" {{ strtolower($status) === 'delivered' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'تم التوصيل (Delivered)' : 'Delivered' }}</option>
                            <option value="cancelled" {{ strtolower($status) === 'cancelled' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ملغي وإرجاع للمخزون (Cancelled)' : 'Cancelled' }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        {{ __('admin.orders.sync_button') }}
                    </button>
                </form>
            </div>

            <!-- Customer Dossier -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">
                    {{ app()->getLocale() == 'ar' ? 'ملخص بيانات العميل' : 'Customer Overview' }}
                </h4>
                <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <div class="font-bold text-primary">{{ $custName }}</div>
                    <div>{{ $custEmail }}</div>
                    <div class="text-muted">{{ $custPhone }}</div>
                </div>
            </div>

            <!-- Shipping Destination -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">
                    {{ app()->getLocale() == 'ar' ? 'عنوان ووجهة التوصيل' : 'Shipping Destination' }}
                </h4>
                <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <div class="font-bold">{{ is_array($shippingAddr) ? ($shippingAddr['recipient'] ?? $custName) : $custName }}</div>
                    <div>{{ is_array($shippingAddr) ? ($shippingAddr['street'] ?? 'King Fahd Rd') : '' }}</div>
                    <div>{{ is_array($shippingAddr) ? ($shippingAddr['city'] ?? 'Riyadh') : 'Riyadh' }}, {{ is_array($shippingAddr) ? ($shippingAddr['country'] ?? 'Saudi Arabia') : 'Saudi Arabia' }}</div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">
                    {{ app()->getLocale() == 'ar' ? 'الملخص المالي والضريبي' : 'Financial Breakdown' }}
                </h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div class="summary-row" style="display: flex; justify-content: space-between;">
                        <span>{{ __('admin.invoices.subtotal') }}:</span>
                        <span class="font-bold">${{ number_format((float)$subtotal, 2) }}</span>
                    </div>
                    @if((float)$discount > 0)
                        <div class="summary-row text-success" style="display: flex; justify-content: space-between; color: var(--color-success);">
                            <span>{{ __('admin.pos.discount') }}:</span>
                            <span class="font-bold">-${{ number_format((float)$discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="summary-row" style="display: flex; justify-content: space-between;">
                        <span>{{ app()->getLocale() == 'ar' ? 'الشحن والتوصيل:' : 'Shipping:' }}</span>
                        <span>{{ (float)$shipping > 0 ? '$' . number_format((float)$shipping, 2) : (app()->getLocale() == 'ar' ? 'شحن مجاني' : 'Free Shipping') }}</span>
                    </div>
                    <div class="summary-row" style="display: flex; justify-content: space-between;">
                        <span>{{ __('admin.invoices.vat_breakdown') }}:</span>
                        <span>${{ number_format((float)$tax, 2) }}</span>
                    </div>
                    <div class="summary-row total" style="display: flex; justify-content: space-between; border-top: 1px solid var(--color-border); padding-top: 0.5rem; font-weight: 900; font-size: 1.1rem; color: var(--color-primary);">
                        <span>{{ __('admin.invoices.grand_total') }}:</span>
                        <span>${{ number_format((float)$total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
