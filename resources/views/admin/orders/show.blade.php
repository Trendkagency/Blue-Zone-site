@php
    $ordNum = is_array($order) ? ($order['order_number'] ?? 'BZ-000') : ($order->order_number ?? 'BZ-000');
    $invNum = is_array($order) ? ($order['invoice_number'] ?? ('INV-' . str_replace(['BZ-', 'ORD-', 'POS-'], '', $ordNum))) : ($order->invoice_number ?? ('INV-' . str_replace(['BZ-', 'ORD-', 'POS-'], '', $ordNum)));
    $orderKey = is_object($order) ? ($order->id ?? $order->order_number) : ($order['id'] ?? ($order['order_number'] ?? 1));
    $status = is_array($order) ? ($order['status'] ?? 'pending') : ($order->status ?? 'pending');
    $payStatus = is_array($order) ? ($order['payment_status'] ?? 'paid') : ($order->payment_status ?? 'paid');
    $payMethod = is_array($order) ? ($order['payment_method'] ?? 'Online MADA / Credit Card') : ($order->payment_method ?? 'Online MADA / Credit Card');
    $channel = is_array($order) ? ($order['channel'] ?? 'online') : ($order->channel ?? 'online');
    $ordDate = is_array($order) ? ($order['date'] ?? '') : ($order->created_at?->format('Y-m-d H:i') ?? '');
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
    $cleanPhone = preg_replace('/[^0-9]/', '', $custPhone);
@endphp

<x-layouts.admin 
    :pageTitle="(app()->getLocale() == 'ar' ? 'تفاصيل الطلب: ' : 'Order #') . $ordNum" 
    :pageSubtitle="__('admin.orders.subtitle')"
    :breadcrumbs="[__('admin.menu.sales') => route('admin.orders.index'), $ordNum => route('admin.orders.show', $orderKey)]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            <x-status-badge :status="$status" />

            <a href="{{ route('admin.invoices.show', $orderKey) }}" class="btn btn-outline font-bold shadow-sm" title="{{ app()->getLocale() == 'ar' ? 'عرض الفاتورة الضريبية الرسمية' : 'View Official Tax Invoice' }}">
                <i class="fa-solid fa-file-invoice-dollar mr-1.5 ml-1.5 text-sky-500"></i> {{ __('admin.invoices.tax_invoice') }}
            </a>

            <a href="{{ route('admin.invoices.print', $orderKey) }}" class="btn btn-primary font-bold shadow-sm" target="_blank" title="{{ app()->getLocale() == 'ar' ? 'طباعة الفاتورة الضريبية الرسمية' : 'Print Official Tax Invoice' }}">
                <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> {{ __('admin.invoices.print_action') }} <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1 text-xs opacity-70"></i>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الطلبات' : 'Orders List' }}
            </a>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Official Tax Invoice Quick Banner -->
    <div class="card mb-6 p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-sky-950 text-white border-0 shadow-lg relative overflow-hidden rounded-2xl">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-sky-500/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-sky-500/20 border border-sky-400/30 text-sky-400 flex items-center justify-center flex-shrink-0 shadow-inner">
                    <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="text-xs font-black uppercase tracking-wider text-sky-400 bg-sky-950/80 px-2.5 py-0.5 rounded-full border border-sky-500/30">
                            {{ app()->getLocale() == 'ar' ? 'فاتورة ضريبية إلكترونية معتمدة' : 'Official Tax Invoice' }}
                        </span>
                        <span class="text-xs text-slate-300 font-mono">{{ $invNum }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-white mt-1 mb-0.5">
                        {{ app()->getLocale() == 'ar' ? 'الفاتورة الضريبية للطلب رقم' : 'Tax Invoice for Order #' }} <span class="text-sky-300">{{ $ordNum }}</span>
                    </h3>
                    <p class="text-xs text-slate-300/80 m-0">
                        {{ app()->getLocale() == 'ar' ? 'الرقم الضريبي للمنشأة: 31004829100003 • نظام الفوترة متوافق مع هيئة الزكاة والضريبة' : 'ZATCA Tax Registration: 31004829100003 • Official Compliant B2C/B2B System' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap w-full md:w-auto justify-end">
                <a href="{{ route('admin.invoices.show', $orderKey) }}" class="btn btn-secondary btn-sm bg-white/10 hover:bg-white/20 text-white border-white/20 backdrop-blur-sm font-bold shadow-sm">
                    <i class="fa-solid fa-eye mr-1.5 ml-1.5 text-sky-300"></i>
                    {{ app()->getLocale() == 'ar' ? 'معاينة الفاتورة' : 'View Invoice' }}
                </a>
                <a href="{{ route('admin.invoices.print', $orderKey) }}" target="_blank" class="btn btn-primary btn-sm bg-sky-500 hover:bg-sky-400 text-slate-950 border-0 font-bold shadow-md">
                    <i class="fa-solid fa-print mr-1.5 ml-1.5"></i>
                    {{ app()->getLocale() == 'ar' ? 'طباعة / PDF' : 'Print / PDF' }}
                </a>
            </div>
        </div>
    </div>

    <!-- Responsive Dossier Grid -->
    <div class="order-dossier-grid">
        
        <!-- Left Column: Items & Timeline -->
        <div class="space-y-6">
            
            <!-- Purchased Products / Compounds Table -->
            <div class="card overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                <div class="card-header p-5 sm:p-6 bg-slate-50/70 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2.5 m-0">
                            <i class="fa-solid fa-boxes-stacked text-sky-600 dark:text-sky-400"></i>
                            {{ app()->getLocale() == 'ar' ? 'التركيبات الحيوية المشتراة' : 'Purchased Formulations' }} 
                            <span class="text-xs bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300 px-2.5 py-0.5 rounded-full font-bold">
                                {{ count($items) }}
                            </span>
                        </h3>
                    </div>
                    <span class="badge badge-secondary text-xs font-semibold py-1 px-3">
                        <i class="fa-solid fa-certificate text-emerald-500 mr-1 ml-1"></i>
                        {{ app()->getLocale() == 'ar' ? 'تغليف سريري معتمد GMP' : 'Verified GMP Packaging' }}
                    </span>
                </div>

                <div class="table-responsive" style="border: none; border-radius: 0; min-width: 0;">
                    <table class="table" style="min-width: 540px;">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/80">
                                <th class="py-3.5 px-4 text-start">{{ app()->getLocale() == 'ar' ? 'التركيبة والمنتج' : 'Formulation / Product' }}</th>
                                <th class="py-3.5 px-4 text-start">{{ __('admin.products.fields.sku') }}</th>
                                <th class="py-3.5 px-4 text-start">{{ app()->getLocale() == 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ app()->getLocale() == 'ar' ? 'الكمية' : 'Qty' }}</th>
                                <th class="py-3.5 px-4 text-end">{{ app()->getLocale() == 'ar' ? 'المجموع' : 'Line Total' }}</th>
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
                                <tr class="hover:bg-sky-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex-shrink-0 shadow-sm">
                                                <img src="{{ asset($iImg) }}" alt="{{ $iName }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $iName }}</div>
                                                @if($iVar)
                                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $iVar }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs text-slate-500 dark:text-slate-400 font-bold">{{ $iSku }}</td>
                                    <td class="py-4 px-4 font-semibold text-slate-700 dark:text-slate-300">@currency((float)$iPrice)</td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 font-black text-sm text-slate-900 dark:text-white">
                                            {{ $iQty }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-end font-black text-slate-900 dark:text-white text-base">@currency((float)$iTot)</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        <i class="fa-solid fa-box-open text-3xl mb-2 opacity-50 block"></i>
                                        {{ __('app.empty.description') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fulfillment Audit Timeline -->
            <div class="card p-6 shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-4 mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                        <i class="fa-solid fa-clock-rotate-left text-sky-600 dark:text-sky-400"></i>
                        {{ __('admin.orders.timeline') }}
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">
                        {{ app()->getLocale() == 'ar' ? 'سجل التدقيق والتنفيذ' : 'Fulfillment Audit Log' }}
                    </span>
                </div>

                <div class="timeline-track">
                    @if(!empty($timeline))
                        @foreach($timeline as $event)
                            <div class="timeline-item completed">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-title font-bold text-slate-900 dark:text-white">{{ is_array($event) ? ($event['status'] ?? '') : ($event->status ?? '') }}</div>
                                    <div class="text-xs text-slate-400 font-medium mt-0.5">{{ is_array($event) ? ($event['timestamp'] ?? '') : ($event->timestamp ?? '') }}</div>
                                    <div class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ is_array($event) ? ($event['note'] ?? '') : ($event->note ?? '') }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="timeline-item completed">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-title font-bold text-slate-900 dark:text-white">{{ app()->getLocale() == 'ar' ? 'تم إنشاء الطلب وتوثيقه بنجاح' : 'Order Created & Verified' }}</div>
                                <div class="text-xs text-slate-400 font-medium mt-0.5">{{ $ordDate ?: now()->toFormattedDateString() }}</div>
                                <div class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                    {{ app()->getLocale() == 'ar' ? 'تم تسجيل المعاملة في قاعدة بيانات ERP المركزية عبر جلسة دفع موثقة وتوليد الفاتورة الضريبية.' : 'Logged into central ERP database via authenticated checkout session and generated official tax invoice.' }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Status Update, Client, Shipping, Financials -->
        <div class="space-y-6">
            
            <!-- 1. Real-time Status Update Action -->
            <div class="card p-6 shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                <h4 class="text-base font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-sky-600"></i>
                    {{ __('admin.orders.update_status') }}
                </h4>
                <form action="{{ route('admin.orders.update-status', $orderKey) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="form-label block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">
                            {{ __('admin.orders.current_status') }}
                        </label>
                        <select name="status" class="form-select w-full font-bold text-sm rounded-xl py-2.5">
                            <option value="pending" {{ strtolower($status) === 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قيد الانتظار (Pending)' : 'Pending' }}</option>
                            <option value="processing" {{ strtolower($status) === 'processing' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قيد التجهيز (Processing)' : 'Processing' }}</option>
                            <option value="shipped" {{ strtolower($status) === 'shipped' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'تم الشحن (Shipped)' : 'Shipped' }}</option>
                            <option value="delivered" {{ strtolower($status) === 'delivered' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'تم التوصيل (Delivered)' : 'Delivered' }}</option>
                            <option value="cancelled" {{ strtolower($status) === 'cancelled' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ملغي وإرجاع للمخزون (Cancelled)' : 'Cancelled' }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-full font-bold py-2.5 shadow-md hover:shadow-lg transition-all">
                        <i class="fa-solid fa-arrows-rotate mr-1.5 ml-1.5"></i>
                        {{ __('admin.orders.sync_button') }}
                    </button>
                </form>
            </div>

            <!-- 2. Customer Dossier -->
            <div class="card p-6 shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                        <i class="fa-solid fa-address-card text-sky-600"></i>
                        {{ app()->getLocale() == 'ar' ? 'ملخص بيانات العميل' : 'Customer Overview' }}
                    </h4>
                    <span class="badge badge-accent text-xs">{{ $channel === 'online' ? 'Online' : 'POS' }}</span>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300 font-black text-sm flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($custName, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 dark:text-white truncate">{{ $custName }}</div>
                            <div class="text-xs text-slate-400">{{ app()->getLocale() == 'ar' ? 'عميل موثق' : 'Verified Client' }}</div>
                        </div>
                    </div>
                    
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                            <i class="fa-regular fa-envelope text-slate-400 w-4 text-center"></i>
                            <a href="mailto:{{ $custEmail }}" class="hover:text-sky-600 truncate">{{ $custEmail ?: '-' }}</a>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-slate-600 dark:text-slate-300">
                            <div class="flex items-center gap-2 truncate">
                                <i class="fa-solid fa-phone text-slate-400 w-4 text-center"></i>
                                <span>{{ $custPhone ?: '-' }}</span>
                            </div>
                            @if(!empty($cleanPhone))
                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold text-xs flex items-center gap-1" title="WhatsApp">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span>واتساب</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Shipping Destination -->
            <div class="card p-6 shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                <h4 class="text-base font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-sky-600"></i>
                    {{ app()->getLocale() == 'ar' ? 'عنوان ووجهة التوصيل' : 'Shipping Destination' }}
                </h4>
                <div class="text-sm space-y-1.5 text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/60 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-user text-xs text-sky-500"></i>
                        {{ is_array($shippingAddr) ? ($shippingAddr['recipient'] ?? $custName) : $custName }}
                    </div>
                    <div>{{ is_array($shippingAddr) ? ($shippingAddr['street'] ?? 'King Fahd Rd') : 'King Fahd Rd' }}</div>
                    <div>{{ is_array($shippingAddr) ? ($shippingAddr['city'] ?? 'Riyadh') : 'Riyadh' }}, {{ is_array($shippingAddr) ? ($shippingAddr['country'] ?? 'Saudi Arabia') : 'Saudi Arabia' }}</div>
                </div>
            </div>

            <!-- 4. Financial Summary & Tax Calculation -->
            <div class="card p-6 shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                <h4 class="text-base font-black text-slate-900 dark:text-white mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-sky-600"></i>
                        {{ app()->getLocale() == 'ar' ? 'الملخص المالي والضريبي' : 'Financial Breakdown' }}
                    </span>
                    <span class="badge badge-success text-[10px] uppercase font-bold tracking-wider">{{ $payStatus }}</span>
                </h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>{{ __('admin.invoices.subtotal') }}:</span>
                        <span class="font-bold text-slate-900 dark:text-white">@currency((float)$subtotal)</span>
                    </div>

                    @if((float)$discount > 0)
                        <div class="flex justify-between text-emerald-600 font-semibold">
                            <span>{{ __('admin.pos.discount') }}:</span>
                            <span>-@currency((float)$discount)</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>{{ app()->getLocale() == 'ar' ? 'الشحن والتوصيل:' : 'Shipping:' }}</span>
                        <span class="font-semibold">{{ (float)$shipping > 0 ? format_currency((float)$shipping) : (app()->getLocale() == 'ar' ? 'شحن مجاني' : 'Free Shipping') }}</span>
                    </div>

                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>{{ __('admin.invoices.vat_breakdown') }} (15%):</span>
                        <span class="font-semibold">@currency((float)$tax)</span>
                    </div>

                    <div class="pt-3 border-t-2 border-slate-900 dark:border-slate-100 flex justify-between items-center text-lg font-black text-slate-900 dark:text-white">
                        <span>{{ __('admin.invoices.grand_total') }}:</span>
                        <span class="text-sky-600 dark:text-sky-400 text-xl">@currency((float)$total)</span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex gap-2">
                    <a href="{{ route('admin.invoices.show', $orderKey) }}" class="btn btn-outline btn-sm flex-1 font-bold">
                        <i class="fa-solid fa-file-invoice mr-1 ml-1"></i>
                        {{ __('admin.invoices.tax_invoice') }}
                    </a>
                    <a href="{{ route('admin.invoices.print', $orderKey) }}" target="_blank" class="btn btn-primary btn-sm flex-1 font-bold">
                        <i class="fa-solid fa-print mr-1 ml-1"></i>
                        {{ __('admin.invoices.print_action') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
