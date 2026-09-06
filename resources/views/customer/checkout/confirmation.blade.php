<x-layouts.customer :title="'Order ' . $order->order_number . ' Confirmed — ' . __('app.brand_name')">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-8">
        
        <!-- Confirmation Hero Banner -->
        <div class="text-center p-8 sm:p-12 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4">
            <div class="w-20 h-20 rounded-full bg-[#67B34A]/15 text-[#67B34A] flex items-center justify-center mx-auto text-4xl shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <span class="text-xs font-black uppercase tracking-[0.25em] text-[#67B34A]">
                {{ app()->getLocale() === 'ar' ? 'تم تأكيد وحجز البروتوكول بنجاح' : 'CLINICAL RESERVATION CONFIRMED' }}
            </span>

            <h1 class="text-3xl sm:text-4xl font-black text-[#031827] dark:text-[#F6F5EF]">
                {{ app()->getLocale() === 'ar' ? 'شكراً لثقتكم في بلوزون' : 'Thank You For Your Protocol Order' }}
            </h1>

            <p class="text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 max-w-lg mx-auto">
                {{ app()->getLocale() === 'ar'
                    ? "تم استلام طلبك برقم [{$order->order_number}] وجاري تجهيز الدفعة العلاجية ونقلها بسلسلة التبريد المعتمدة."
                    : "Order #{$order->order_number} has been placed and routed to our European compounding facility under cold-chain logistics." }}
            </p>

            <div class="pt-4 flex items-center justify-center flex-wrap gap-4 text-xs font-bold">
                <div class="px-4 py-2 rounded-xl bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2]">
                    <strong>Order #:</strong> {{ $order->order_number }}
                </div>
                <div class="px-4 py-2 rounded-xl bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2]">
                    <strong>Date:</strong> {{ $order->date ? $order->date->format('M d, Y') : now()->format('M d, Y') }}
                </div>
                <div class="px-4 py-2 rounded-xl {{ $order->payment_status === 'Paid' ? 'bg-[#67B34A]/15 text-[#67B34A]' : 'bg-amber-500/15 text-amber-600' }}">
                    <strong>Payment:</strong> {{ strtoupper($order->payment_method ?? 'COD') }} ({{ $order->payment_status ?? 'Pending' }})
                </div>
            </div>
        </div>

        <!-- Order Progress Timeline -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm p-6 sm:p-8 space-y-6">
            <h2 class="text-base sm:text-lg font-black text-[#031827] dark:text-[#F6F5EF] border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4">
                {{ app()->getLocale() === 'ar' ? 'مسار الشحن والتجهيز اللوجستي' : 'Cold-Chain Dispatch & Fulfillment Timeline' }}
            </h2>

            <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-[#0A4F78]/20 dark:before:bg-[#0A4F78]/40">
                @foreach($order->timeline as $step)
                    <div class="relative">
                        <div class="absolute -left-6 sm:-left-8 top-1 w-6 h-6 rounded-full flex items-center justify-center text-xs {{ $step['completed'] ? 'bg-[#67B34A] text-white shadow-xs' : 'bg-gray-300 dark:bg-gray-700 text-gray-500' }}">
                            <i class="fa-solid {{ $step['icon'] }}"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF]">{{ $step['status'] }}</h3>
                                <span class="text-[11px] font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50">{{ $step['timestamp'] }}</span>
                            </div>
                            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $step['note'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Items & Receipt Breakdown -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm p-6 sm:p-8 space-y-6">
            <h2 class="text-base sm:text-lg font-black text-[#031827] dark:text-[#F6F5EF] border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4">
                {{ app()->getLocale() === 'ar' ? 'التركيبات المحجوزة في هذا الطلب' : 'Reserved Formulations' }} ({{ $order->items->count() }})
            </h2>

            <div class="divide-y divide-[#0A4F78]/10 dark:divide-[#0A4F78]/25">
                @foreach($order->items as $item)
                    @php
                        $isAr = app()->getLocale() === 'ar';
                        $name = $isAr ? ($item->product_name_ar ?? $item->product_name_en) : $item->product_name_en;
                    @endphp
                    <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] p-1 flex-shrink-0 flex items-center justify-center border border-[#0A4F78]/10">
                                <img src="{{ asset($item->image ?? '/assets/logo/logo-main.png') }}" 
                                     alt="{{ $name }}" 
                                     onerror="this.onerror=null; this.src='{{ asset('/assets/logo/logo-main.png') }}';" 
                                     class="w-full h-full object-contain" />
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-black text-[#031827] dark:text-[#F6F5EF]">{{ $name }}</h4>
                                <span class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60">
                                    Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                                </span>
                            </div>
                        </div>

                        <span class="text-xs sm:text-sm font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                            ${{ number_format($item->total, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Receipt Breakdown -->
            <div class="pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 space-y-2 text-xs sm:text-sm">
                <div class="flex justify-between text-[#031827]/80 dark:text-[#F6F5EF]/80">
                    <span>{{ __('shop.cart.subtotal') }}</span>
                    <span class="font-bold text-[#031827] dark:text-[#F6F5EF]">${{ number_format($order->subtotal, 2) }}</span>
                </div>

                @if($order->discount > 0)
                    <div class="flex justify-between text-[#67B34A] font-bold">
                        <span>{{ __('shop.cart.discount') }} ({{ $order->coupon_code ?? '' }})</span>
                        <span>-${{ number_format($order->discount, 2) }}</span>
                    </div>
                @endif

                <div class="flex justify-between text-[#031827]/80 dark:text-[#F6F5EF]/80">
                    <span>Shipping</span>
                    <span class="font-bold text-[#67B34A]">{{ $order->shipping <= 0 ? 'Complimentary ($0.00)' : '$' . number_format($order->shipping, 2) }}</span>
                </div>

                @if($order->tax > 0)
                    <div class="flex justify-between text-[#031827]/80 dark:text-[#F6F5EF]/80">
                        <span>VAT</span>
                        <span class="font-bold text-[#031827] dark:text-[#F6F5EF]">${{ number_format($order->tax, 2) }}</span>
                    </div>
                @endif

                <div class="pt-3 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 flex justify-between items-center">
                    <span class="text-sm sm:text-base font-black text-[#031827] dark:text-[#F6F5EF]">{{ __('shop.cart.total') }}</span>
                    <span class="text-xl sm:text-2xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Destination Address Box -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm p-6 sm:p-8 space-y-4">
            <h2 class="text-base sm:text-lg font-black text-[#031827] dark:text-[#F6F5EF] border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4">
                {{ app()->getLocale() === 'ar' ? 'عنوان وبيانات التسليم' : 'Delivery Destination' }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                <div>
                    <span class="text-[#031827]/50 dark:text-[#F6F5EF]/50 font-bold uppercase text-[10px] block mb-1">Customer Recipient</span>
                    <p class="font-bold text-[#031827] dark:text-[#F6F5EF]">{{ $order->customer_name }}</p>
                    <p class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $order->customer_email }}</p>
                    <p class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $order->customer_phone }}</p>
                </div>

                <div>
                    <span class="text-[#031827]/50 dark:text-[#F6F5EF]/50 font-bold uppercase text-[10px] block mb-1">Destination Address</span>
                    <p class="font-bold text-[#031827] dark:text-[#F6F5EF]">{{ $order->shipping_address['street'] ?? 'Street Address' }}</p>
                    <p class="text-[#031827]/70 dark:text-[#F6F5EF]/70">
                        {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['country'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}
                    </p>
                    @if(!empty($order->notes))
                        <p class="text-[11px] text-[#0A4F78] dark:text-[#2A8FC2] mt-1 font-semibold">Note: {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action CTAs -->
        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="{{ route('customer.shop') }}" class="px-8 py-4 rounded-2xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest shadow-lg transition-all btn-sheen cursor-pointer">
                {{ __('app.actions.continue_shopping') }}
            </a>
            <button onclick="window.print()" class="px-8 py-4 rounded-2xl border border-[#0A4F78]/30 hover:bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] text-xs font-black uppercase tracking-widest transition-all cursor-pointer">
                <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> Print Protocol Receipt
            </button>
        </div>

    </div>
</x-layouts.customer>
