<x-layouts.customer :title="__('shop.checkout.title') . ' — ' . __('app.brand_name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        
        <!-- Header -->
        <div class="mb-8">
            <span class="text-xs font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">
                {{ app()->getLocale() === 'ar' ? 'إتمام بروتوكول الشراء الآمن' : 'SECURE CLINICAL CHECKOUT' }}
            </span>
            <h1 class="text-3xl sm:text-4xl font-black text-[#031827] dark:text-[#F6F5EF] mt-1">
                {{ __('shop.checkout.title') }}
            </h1>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-500 font-bold text-sm flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.checkout.store') }}" id="checkoutForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Multi-Step Forms -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Step 1: Customer Contact -->
                    <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm p-6 sm:p-8 space-y-6">
                        <div class="flex items-center gap-3 border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4">
                            <span class="w-8 h-8 rounded-full bg-[#0A4F78] text-white flex items-center justify-center font-black text-xs">
                                1
                            </span>
                            <h2 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">
                                {{ __('shop.checkout.step_1') }}
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] mb-2">
                                    {{ __('shop.checkout.full_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="full_name" 
                                       value="{{ old('full_name', $prefill['full_name']) }}" 
                                       placeholder="e.g. Dr. Zaid Al-Harbi"
                                       class="w-full px-4 py-3 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs" 
                                       required />
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] mb-2">
                                    {{ __('shop.checkout.phone') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" 
                                       name="phone" 
                                       value="{{ old('phone', $prefill['phone']) }}" 
                                       placeholder="+966 50 123 4567"
                                       class="w-full px-4 py-3 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs" 
                                       required />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] mb-2">
                                {{ __('shop.checkout.email') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email', $prefill['email']) }}" 
                                   placeholder="customer@example.com"
                                   class="w-full px-4 py-3 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs" 
                                   required />
                            <p class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 mt-1.5">
                                We will dispatch your clinical batch certificates, invoice, and real-time tracking links to this address.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2: Shipping Destination -->
                    <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm p-6 sm:p-8 space-y-6">
                        <div class="flex items-center gap-3 border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4">
                            <span class="w-8 h-8 rounded-full bg-[#0A4F78] text-white flex items-center justify-center font-black text-xs">
                                2
                            </span>
                            <h2 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">
                                {{ __('shop.checkout.step_2') }}
                            </h2>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] mb-2">
                                {{ __('shop.checkout.address') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="address" 
                                   value="{{ old('address', $prefill['address']) }}" 
                                   placeholder="Street name, District, Building / Villa number"
                                   class="w-full px-4 py-3 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs" 
                                   required />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] mb-2">
                                    {{ __('shop.checkout.city') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="city" 
                                       value="{{ old('city', $prefill['city']) }}" 
                                       class="w-full px-4 py-3 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs" 
                                       required />
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] mb-2">
                                    {{ __('shop.checkout.country') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="country" 
                                       value="{{ old('country', $prefill['country']) }}" 
                                       class="w-full px-4 py-3 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs" 
                                       required />
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] mb-2">
                                    {{ __('shop.checkout.postal_code') }}
                                </label>
                                <input type="text" 
                                       name="postal_code" 
                                       value="{{ old('postal_code', $prefill['postal_code']) }}" 
                                       placeholder="12213"
                                       class="w-full px-4 py-3 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] mb-2">
                                {{ __('shop.checkout.notes') }}
                            </label>
                            <textarea name="notes" 
                                      rows="2" 
                                      placeholder="e.g. Leave with building reception in climate-controlled area."
                                      class="w-full px-4 py-3 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Step 3: Payment Method Selection -->
                    <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm p-6 sm:p-8 space-y-6">
                        <div class="flex items-center gap-3 border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4">
                            <span class="w-8 h-8 rounded-full bg-[#0A4F78] text-white flex items-center justify-center font-black text-xs">
                                3
                            </span>
                            <h2 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">
                                {{ __('shop.checkout.step_3') }}
                            </h2>
                        </div>

                        <div class="space-y-3">
                            @if(isset($activeGateways['stripe']))
                                <label class="flex items-center gap-4 p-4 rounded-2xl border-2 border-[#0A4F78] bg-[#F6F5EF]/40 dark:bg-[#031827]/40 cursor-pointer transition-all hover:bg-[#F6F5EF]">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="stripe" 
                                           checked 
                                           class="w-4 h-4 text-[#0A4F78] focus:ring-[#0A4F78]" />
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-sm text-[#031827] dark:text-[#F6F5EF]">
                                                {{ __('shop.checkout.payment_methods.card') }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-[#67B34A]/15 text-[#67B34A] text-[10px] font-black uppercase">
                                                256-Bit SSL
                                            </span>
                                        </div>
                                        <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 mt-0.5">
                                            Visa, Mastercard, American Express, Mada, Apple Pay via Stripe
                                        </p>
                                    </div>
                                    <div class="text-xl text-[#0A4F78] dark:text-[#2A8FC2]">
                                        <i class="fa-solid fa-lock"></i>
                                    </div>
                                </label>
                            @endif

                            @if(isset($activeGateways['cod']))
                                <label class="flex items-center gap-4 p-4 rounded-2xl border border-[#0A4F78]/20 bg-white dark:bg-[#062B49] cursor-pointer transition-all hover:border-[#0A4F78] hover:bg-[#F6F5EF]/30">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="cod" 
                                           {{ !isset($activeGateways['stripe']) ? 'checked' : '' }}
                                           class="w-4 h-4 text-[#0A4F78] focus:ring-[#0A4F78]" />
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-sm text-[#031827] dark:text-[#F6F5EF]">
                                                {{ __('shop.checkout.payment_methods.cod') }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] text-[10px] font-black uppercase">
                                                Courier Handover
                                            </span>
                                        </div>
                                        <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 mt-0.5">
                                            Pay securely with cash or card terminal upon white-glove arrival
                                        </p>
                                    </div>
                                    <div class="text-xl text-[#10b981]">
                                        <i class="fa-solid fa-truck"></i>
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Right Column: Live Order Summary -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md p-6 sm:p-8 space-y-6">
                        
                        <div class="border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4 flex items-center justify-between">
                            <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">
                                {{ app()->getLocale() === 'ar' ? 'تركيبات الطلب' : 'Protocol Items' }} ({{ count($summary['items']) }})
                            </h3>
                            <a href="{{ route('customer.cart') }}" class="text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">
                                {{ app()->getLocale() === 'ar' ? 'تعديل السلة' : 'Edit Cart' }}
                            </a>
                        </div>

                        <!-- Item List -->
                        <div class="space-y-3 max-h-72 overflow-y-auto pr-1 divide-y divide-[#0A4F78]/10 dark:divide-[#0A4F78]/25">
                            @foreach($summary['items'] as $item)
                                @php
                                    $isAr = app()->getLocale() === 'ar';
                                    $pName = $isAr ? ($item['name_ar'] ?? $item['name_en']) : $item['name_en'];
                                @endphp
                                <div class="pt-3 first:pt-0 flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] p-1.5 flex-shrink-0 flex items-center justify-center border border-[#0A4F78]/10">
                                        <img src="{{ asset($item['image'] ?? '/assets/logo/logo-main.png') }}" 
                                             alt="{{ $pName }}" 
                                             onerror="this.onerror=null; this.src='{{ asset('/assets/logo/logo-main.png') }}';" 
                                             class="w-full h-full object-contain" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-black text-[#031827] dark:text-[#F6F5EF] truncate">
                                            {{ $pName }}
                                        </h4>
                                        <span class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60">
                                            Qty: {{ $item['quantity'] }} × ${{ number_format($item['price'], 2) }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                                        ${{ number_format($item['total'], 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Financial Totals -->
                        <div class="space-y-3 pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 text-xs sm:text-sm">
                            <div class="flex justify-between items-center text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium">
                                <span>{{ __('shop.cart.subtotal') }}</span>
                                <span class="font-bold text-[#031827] dark:text-[#F6F5EF]">${{ number_format($summary['subtotal'], 2) }}</span>
                            </div>

                            @if($summary['discount'] > 0)
                                <div class="flex justify-between items-center text-[#67B34A] font-bold">
                                    <span>{{ __('shop.cart.discount') }} ({{ $summary['coupon']['code'] ?? '' }})</span>
                                    <span>-${{ number_format($summary['discount'], 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium">
                                <span>Insured Shipping</span>
                                @if($summary['shipping'] <= 0)
                                    <span class="font-bold text-[#67B34A]">Free ($0.00)</span>
                                @else
                                    <span class="font-bold">${{ number_format($summary['shipping'], 2) }}</span>
                                @endif
                            </div>

                            @if($summary['tax'] > 0)
                                <div class="flex justify-between items-center text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium">
                                    <span>Estimated VAT ({{ $summary['tax_percentage'] }}%)</span>
                                    <span class="font-bold">${{ number_format($summary['tax'], 2) }}</span>
                                </div>
                            @endif

                            <div class="pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 flex justify-between items-center">
                                <span class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">{{ __('shop.cart.total') }}</span>
                                <span class="text-2xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">${{ number_format($summary['total'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Submit Order Button -->
                        <div class="pt-2">
                            <button type="submit" class="w-full py-4 rounded-2xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest shadow-xl transition-all cursor-pointer btn-sheen flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock text-xs"></i>
                                <span>{{ __('shop.checkout.place_order') }}</span>
                            </button>
                        </div>

                        <div class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 text-center space-y-1 pt-1">
                            <div class="flex items-center justify-center gap-1.5 text-[#67B34A] font-bold">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>30-Day Cellular Efficacy Guarantee</span>
                            </div>
                            <p>Encrypted with TLS 1.3 cryptographic protection.</p>
                        </div>

                    </div>
                </div>

            </div>
        </form>

    </div>
</x-layouts.customer>
