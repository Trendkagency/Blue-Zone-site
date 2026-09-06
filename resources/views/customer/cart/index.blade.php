<x-layouts.customer :title="__('shop.cart.title') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 1.5rem;">
            {{ __('shop.cart.title') }}
        </h1>

        <!-- Free Shipping Tier Indicator -->
        <div class="shipping-progress-bar">
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.9375rem; font-weight: 600;">
                <span>🚚 {{ __('shop.cart.shipping_unlocked') }}</span>
                <span class="badge badge-success">100% Unlocked</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: 100%;"></div>
            </div>
        </div>

        @if(count($cartItems) > 0)
            <div class="cart-layout">
                <!-- Cart Items Table -->
                <div class="cart-table-wrapper">
                    @foreach($cartItems as $item)
                        @php
                            $isAr = app()->getLocale() === 'ar';
                            $pName = $isAr ? ($item['product']['name_ar'] ?? $item['product']['name_en']) : $item['product']['name_en'];
                            $vName = $isAr ? ($item['variant']['name_ar'] ?? $item['variant']['name_en']) : $item['variant']['name_en'];
                        @endphp
                        <div class="cart-item-row">
                            <img src="{{ asset($item['product']['image']) }}" alt="{{ $pName }}" class="cart-item-img" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">

                            <div>
                                <h4 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 0.25rem 0;">
                                    <a href="{{ route('customer.product.show', $item['product']['slug']) }}" style="color: inherit; text-decoration: none;">
                                        {{ $pName }}
                                    </a>
                                </h4>
                                <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">
                                    {{ $vName }}
                                </div>
                                <div class="text-xs text-muted">
                                    SKU: {{ $item['variant']['sku'] ?? $item['product']['sku'] }}
                                </div>
                            </div>

                            <div class="font-bold text-sm">
                                ${{ number_format($item['unit_price'], 2) }}
                            </div>

                            <div class="quantity-control">
                                <button type="button" class="qty-btn" onclick="changeQty(this, -1)">-</button>
                                <input type="number" class="qty-input" value="{{ $item['quantity'] }}" min="1">
                                <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                            </div>

                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <span class="font-black text-base" style="color: var(--color-text-primary);">
                                    ${{ number_format($item['total'], 2) }}
                                </span>
                                <button type="button" class="btn btn-ghost btn-sm" style="color: var(--color-danger); padding: 0.25rem;" title="Remove">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <div style="padding: 1.25rem 1.5rem; background: var(--color-bg-subtle); display: flex; align-items: center; justify-content: space-between;">
                        <a href="{{ route('customer.shop') }}" class="btn btn-secondary btn-sm">
                            ← {{ __('app.actions.continue_shopping') }}
                        </a>
                        <button type="button" class="btn btn-ghost btn-sm text-muted">
                            {{ __('shop.cart.clear_cart') }}
                        </button>
                    </div>
                </div>

                <!-- Summary Panel -->
                <div class="cart-summary-card">
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">
                        Order Protocol Summary
                    </h3>

                    <!-- Promo Code Input -->
                    <div>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" class="form-control text-sm" placeholder="{{ __('shop.cart.coupon_placeholder') }}" value="WELCOME15">
                            <button type="button" class="btn btn-secondary btn-sm">
                                {{ __('shop.cart.apply_coupon') }}
                            </button>
                        </div>
                        <span class="text-xs font-semibold" style="color: var(--color-success); margin-top: 0.35rem; display: block;">
                            ✓ Coupon "WELCOME15" applied (-10%)
                        </span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div class="summary-row">
                            <span>{{ __('shop.cart.subtotal') }}</span>
                            <span class="font-bold">${{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="summary-row" style="color: var(--color-success);">
                            <span>{{ __('shop.cart.discount') }} (10%)</span>
                            <span class="font-bold">-${{ number_format($discount, 2) }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Complimentary Shipping</span>
                            <span class="font-bold text-success">Free ($0.00)</span>
                        </div>

                        <div class="summary-row">
                            <span>Estimated VAT (15%)</span>
                            <span>${{ number_format($tax, 2) }}</span>
                        </div>

                        <div class="summary-row total">
                            <span>{{ __('shop.cart.total') }}</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customer.checkout') }}" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 0.5rem;">
                        {{ __('app.actions.checkout') }} →
                    </a>
                </div>
            </div>
        @else
            <x-empty-state 
                :title="__('shop.cart.empty_title')" 
                :description="__('shop.cart.empty_desc')" 
                :actionLabel="__('app.actions.continue_shopping')" 
                :actionUrl="route('customer.shop')" 
            />
        @endif
    </div>
</x-layouts.customer>
