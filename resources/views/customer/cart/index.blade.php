<x-layouts.customer :title="__('shop.cart.title') . ' — ' . __('app.brand_name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        
        <!-- Header & Breadcrumbs -->
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="text-xs font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">
                    {{ app()->getLocale() === 'ar' ? 'سلة البروتوكول العلاجي' : 'CLINICAL FORMULATION BATCH' }}
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-[#031827] dark:text-[#F6F5EF] mt-1">
                    {{ __('shop.cart.title') }}
                </h1>
            </div>
            
            <a href="{{ route('customer.shop') }}" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">
                <i class="fa-solid fa-arrow-left"></i>
                <span>{{ __('app.actions.continue_shopping') }}</span>
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-[#67B34A]/10 border border-[#67B34A]/30 text-[#67B34A] font-bold text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-500 font-bold text-sm flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(count($cartItems) > 0)
            <!-- Free Shipping Tier Indicator -->
            <div class="mb-8 p-5 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm">
                <div class="flex items-center justify-between text-xs sm:text-sm font-extrabold mb-2.5">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-[#0A4F78] dark:text-[#2A8FC2]"></i>
                        @if($freeShippingUnlocked)
                            <span class="text-[#67B34A]">{{ app()->getLocale() === 'ar' ? '✓ تم تفعيل الشحن المبرد المجاني لطلبك!' : '✓ Compliments of BLUE ZONE: Free Cold-Chain Shipping Unlocked!' }}</span>
                        @else
                            <span class="text-[#031827] dark:text-[#F6F5EF]">
                                {{ app()->getLocale() === 'ar' 
                                    ? "أضف ما قيمته $" . number_format($neededForFreeShipping, 2) . " إضافية للحصول على شحن مجاني" 
                                    : "Add $" . number_format($neededForFreeShipping, 2) . " more to unlock complimentary cold-chain delivery" }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[11px] font-black uppercase px-2.5 py-1 rounded-full {{ $freeShippingUnlocked ? 'bg-[#67B34A]/15 text-[#67B34A]' : 'bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2]' }}">
                        {{ $freeShippingUnlocked ? '100% UNLOCKED' : number_format(min(100, ($subtotal / $freeShippingThreshold) * 100), 0) . '%' }}
                    </span>
                </div>
                <div class="w-full h-2 rounded-full bg-[#0A4F78]/10 dark:bg-[#031827] overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $freeShippingUnlocked ? 'bg-[#67B34A]' : 'bg-gradient-to-r from-[#0A4F78] to-[#2A8FC2]' }}" 
                         style="width: {{ min(100, ($subtotal / $freeShippingThreshold) * 100) }}%;">
                    </div>
                </div>
            </div>

            <!-- Cart Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Line Items Table -->
                <div class="lg:col-span-8 space-y-4">
                    <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm overflow-hidden">
                        
                        <!-- Table Header (Desktop) -->
                        <div class="hidden sm:grid grid-cols-12 gap-4 px-6 py-4 bg-[#F6F5EF] dark:bg-[#031827] border-b border-[#0A4F78]/10 dark:border-[#0A4F78]/25 text-[11px] font-black uppercase tracking-wider text-[#031827]/60 dark:text-[#F6F5EF]/60">
                            <div class="col-span-6">{{ app()->getLocale() === 'ar' ? 'التركيبة والبروتوكول' : 'FORMULATION' }}</div>
                            <div class="col-span-2 text-center">{{ app()->getLocale() === 'ar' ? 'السعر' : 'PRICE' }}</div>
                            <div class="col-span-2 text-center">{{ app()->getLocale() === 'ar' ? 'الكمية' : 'QUANTITY' }}</div>
                            <div class="col-span-2 text-right">{{ app()->getLocale() === 'ar' ? 'المجموع' : 'TOTAL' }}</div>
                        </div>

                        <!-- Table Rows -->
                        <div class="divide-y divide-[#0A4F78]/10 dark:divide-[#0A4F78]/25">
                            @foreach($cartItems as $item)
                                @php
                                    $isAr = app()->getLocale() === 'ar';
                                    $pName = $isAr ? ($item['name_ar'] ?? $item['name_en']) : $item['name_en'];
                                @endphp
                                <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-12 gap-4 items-center hover:bg-[#F6F5EF]/40 dark:hover:bg-[#031827]/40 transition-colors">
                                    
                                    <!-- Product Info & Image -->
                                    <div class="sm:col-span-6 flex items-center gap-4">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] p-2 flex-shrink-0 flex items-center justify-center border border-[#0A4F78]/10 dark:border-[#0A4F78]/25">
                                            <img src="{{ asset($item['image'] ?? '/assets/logo/logo-main.png') }}" 
                                                 alt="{{ $pName }}" 
                                                 onerror="this.onerror=null; this.src='{{ asset('/assets/logo/logo-main.png') }}';" 
                                                 class="w-full h-full object-contain hover:scale-105 transition-transform" />
                                        </div>
                                        <div class="min-w-0 flex-1 space-y-1">
                                            <span class="inline-block text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2]">
                                                SKU: {{ $item['sku'] ?? 'BZ-MED' }}
                                            </span>
                                            <h3 class="text-sm sm:text-base font-black text-[#031827] dark:text-[#F6F5EF] truncate">
                                                <a href="{{ route('customer.product.show', $item['slug'] ?? $item['id']) }}" class="hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-colors">
                                                    {{ $pName }}
                                                </a>
                                            </h3>
                                            <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60">
                                                30-Day Clinical Protocol Unit
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="sm:col-span-2 sm:text-center text-xs sm:text-sm font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                                        <span class="sm:hidden text-muted font-normal text-xs">{{ app()->getLocale() === 'ar' ? 'السعر:' : 'Price:' }} </span>
                                        ${{ number_format($item['price'], 2) }}
                                    </div>

                                    <!-- Quantity Stepper -->
                                    <div class="sm:col-span-2 flex sm:justify-center items-center">
                                        <div class="flex items-center border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] overflow-hidden shadow-xs">
                                            <button type="button" onclick="BLUEZONE_CART.updateQty('{{ $item['id'] }}', -1)" aria-label="Decrease quantity" class="px-3 py-1.5 text-xs font-black hover:bg-[#0A4F78]/15 text-[#031827] dark:text-white transition-colors cursor-pointer">-</button>
                                            <span class="px-3 text-xs font-bold text-[#031827] dark:text-white min-w-[24px] text-center">{{ $item['quantity'] }}</span>
                                            <button type="button" onclick="BLUEZONE_CART.updateQty('{{ $item['id'] }}', 1)" aria-label="Increase quantity" class="px-3 py-1.5 text-xs font-black hover:bg-[#0A4F78]/15 text-[#031827] dark:text-white transition-colors cursor-pointer">+</button>
                                        </div>
                                    </div>

                                    <!-- Line Total & Delete Action -->
                                    <div class="sm:col-span-2 flex items-center justify-between sm:justify-end gap-3">
                                        <span class="text-sm sm:text-base font-black text-[#031827] dark:text-[#F6F5EF]">
                                            ${{ number_format($item['total'], 2) }}
                                        </span>
                                        <button type="button" onclick="BLUEZONE_CART.remove('{{ $item['id'] }}')" aria-label="Remove item" class="p-2 text-red-500/70 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-500/10 rounded-xl transition-colors cursor-pointer" title="{{ __('shop.cart.remove') ?? 'Remove' }}">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        <!-- Table Footer Actions -->
                        <div class="p-4 sm:p-6 bg-[#F6F5EF]/60 dark:bg-[#031827]/60 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/25 flex items-center justify-between flex-wrap gap-4">
                            <a href="{{ route('customer.shop') }}" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>{{ __('app.actions.continue_shopping') }}</span>
                            </a>
                            <button type="button" onclick="BLUEZONE_CART.clear()" class="text-xs font-bold text-red-500 hover:text-red-600 transition-colors cursor-pointer">
                                <i class="fa-solid fa-broom mr-1 ml-1"></i> {{ __('shop.cart.clear_cart') }}
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Right Column: Order Protocol Summary -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md p-6 sm:p-8 space-y-6">
                        
                        <div class="border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4">
                            <h2 class="text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                                {{ app()->getLocale() === 'ar' ? 'ملخص الطلب المالي' : 'Order Protocol Summary' }}
                            </h2>
                            <span class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60">
                                {{ count($cartItems) }} {{ app()->getLocale() === 'ar' ? 'عناصر محجوزة' : 'formulations reserved' }}
                            </span>
                        </div>

                        <!-- Promo Coupon Input Form -->
                        <div>
                            <form method="POST" action="{{ route('customer.cart.coupon') }}" class="flex gap-2">
                                @csrf
                                <input type="text" 
                                       name="code" 
                                       placeholder="{{ __('shop.cart.coupon_placeholder') }}" 
                                       value="{{ $coupon['code'] ?? '' }}"
                                       class="flex-1 px-4 py-2.5 rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 bg-white dark:bg-[#031827] text-xs font-bold uppercase text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#0A4F78] shadow-xs" 
                                       required />
                                <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-extrabold uppercase tracking-wider transition-all shadow-xs cursor-pointer btn-sheen">
                                    {{ __('shop.cart.apply_coupon') }}
                                </button>
                            </form>

                            @if($coupon)
                                <div class="mt-2.5 flex items-center justify-between p-2.5 rounded-xl bg-[#67B34A]/15 border border-[#67B34A]/30 text-[#67B34A] text-xs font-bold">
                                    <span>✓ {{ $coupon['code'] }} (-{{ $coupon['percent'] }}%)</span>
                                    <form method="POST" action="{{ route('customer.cart.coupon.remove') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-600 font-bold ml-2 cursor-pointer" title="Remove coupon">✕</button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- Itemized Price Breakdown -->
                        <div class="space-y-3 pt-2 text-sm border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/25">
                            
                            <div class="flex justify-between items-center text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium">
                                <span>{{ __('shop.cart.subtotal') }}</span>
                                <span class="font-bold text-[#031827] dark:text-[#F6F5EF]">${{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if($discount > 0)
                                <div class="flex justify-between items-center text-[#67B34A] font-bold">
                                    <span>{{ __('shop.cart.discount') }} ({{ $coupon['percent'] ?? 0 }}%)</span>
                                    <span>-${{ number_format($discount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? 'الشحن المبرد المضمون' : 'Insured Cold-Chain Shipping' }}</span>
                                @if($shipping <= 0)
                                    <span class="font-bold text-[#67B34A]">{{ app()->getLocale() === 'ar' ? 'مجاني ($0.00)' : 'Complimentary ($0.00)' }}</span>
                                @else
                                    <span class="font-bold text-[#031827] dark:text-[#F6F5EF]">${{ number_format($shipping, 2) }}</span>
                                @endif
                            </div>

                            @if($tax > 0)
                                <div class="flex justify-between items-center text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium">
                                    <span>{{ app()->getLocale() === 'ar' ? 'ضريبة القيمة المضافة المقدرة' : 'Estimated VAT' }} ({{ $taxPercentage }}%)</span>
                                    <span class="font-bold text-[#031827] dark:text-[#F6F5EF]">${{ number_format($tax, 2) }}</span>
                                </div>
                            @endif

                            <div class="pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 flex justify-between items-center">
                                <span class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">{{ __('shop.cart.total') }}</span>
                                <span class="text-2xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">${{ number_format($total, 2) }}</span>
                            </div>

                        </div>

                        <!-- Checkout Button -->
                        <div class="pt-2">
                            <a href="{{ route('customer.checkout') }}" class="w-full block text-center py-4 rounded-2xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest shadow-xl transition-all cursor-pointer btn-sheen">
                                {{ __('app.actions.checkout') }} →
                            </a>
                        </div>

                        <div class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 text-center space-y-1 pt-1">
                            <div class="flex items-center justify-center gap-1.5 text-[#67B34A] font-bold">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>30-Day Cellular Longevity Guarantee</span>
                            </div>
                            <p>All formulations manufactured under European cGMP cleanroom protocols.</p>
                        </div>

                    </div>
                </div>

            </div>

        @else
            <!-- Empty State -->
            <div class="py-20 text-center bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm max-w-2xl mx-auto p-8 space-y-6">
                <div class="w-20 h-20 rounded-full bg-[#0A4F78]/10 dark:bg-[#0A4F78]/30 flex items-center justify-center mx-auto text-3xl text-[#0A4F78] dark:text-[#2A8FC2]">
                    🛒
                </div>
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-[#031827] dark:text-[#F6F5EF]">
                        {{ __('shop.cart.empty_title') }}
                    </h2>
                    <p class="text-sm text-[#031827]/60 dark:text-[#F6F5EF]/60 max-w-md mx-auto">
                        {{ __('shop.cart.empty_desc') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('customer.shop') }}" class="inline-block px-8 py-4 rounded-2xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest shadow-xl transition-all btn-sheen cursor-pointer">
                        {{ __('app.actions.continue_shopping') }}
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-layouts.customer>
