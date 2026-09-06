<x-layouts.customer :title="__('shop.checkout.title') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.checkout.title') }}
        </h1>

        <div class="checkout-layout">
            <!-- Left: Checkout Multi-Step Forms -->
            <div>
                <!-- Step 1: Customer Contact -->
                <div class="checkout-step-card">
                    <div class="checkout-step-header">
                        <span class="step-number">1</span>
                        <span>{{ __('shop.checkout.step_1') }}</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-forms.input 
                            name="full_name" 
                            :label="__('shop.checkout.full_name')" 
                            value="Dr. Zaid Al-Harbi" 
                            required 
                        />
                        <x-forms.input 
                            name="phone" 
                            type="tel" 
                            :label="__('shop.checkout.phone')" 
                            value="+966 50 123 4567" 
                            required 
                        />
                    </div>

                    <x-forms.input 
                        name="email" 
                        type="email" 
                        :label="__('shop.checkout.email')" 
                        value="zaid.harbi@example.com" 
                        required 
                        hint="We will dispatch your clinical batch certificates and tracking links here."
                    />
                </div>

                <!-- Step 2: Destination Address -->
                <div class="checkout-step-card">
                    <div class="checkout-step-header">
                        <span class="step-number">2</span>
                        <span>{{ __('shop.checkout.step_2') }}</span>
                    </div>

                    <x-forms.input 
                        name="address" 
                        :label="__('shop.checkout.address')" 
                        value="King Fahd Road, Al Olaya, Villa 42" 
                        required 
                    />

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <x-forms.input 
                            name="city" 
                            :label="__('shop.checkout.city')" 
                            value="Riyadh" 
                            required 
                        />
                        <x-forms.input 
                            name="country" 
                            :label="__('shop.checkout.country')" 
                            value="Saudi Arabia" 
                            required 
                        />
                        <x-forms.input 
                            name="postal_code" 
                            :label="__('shop.checkout.postal_code')" 
                            value="12213" 
                        />
                    </div>

                    <x-forms.textarea 
                        name="notes" 
                        :label="__('shop.checkout.notes')" 
                        rows="2" 
                        placeholder="e.g. Leave with building security in climate-controlled lobby."
                    />
                </div>

                <!-- Step 3: Payment Method -->
                <div class="checkout-step-card">
                    <div class="checkout-step-header">
                        <span class="step-number">3</span>
                        <span>{{ __('shop.checkout.step_3') }}</span>
                    </div>

                    <div class="payment-options-group">
                        <label class="payment-card-option selected">
                            <input type="radio" name="payment_method" value="card" checked class="form-check-input">
                            <div style="flex-grow: 1;">
                                <div class="font-bold text-sm">
                                    {{ __('shop.checkout.payment_methods.card') }}
                                </div>
                                <div class="text-xs text-muted">
                                    Visa, Mastercard, American Express, Mada (256-bit SSL encrypted)
                                </div>
                            </div>
                            <span class="badge badge-accent">Encrypted</span>
                        </label>

                        <label class="payment-card-option">
                            <input type="radio" name="payment_method" value="apple_pay" class="form-check-input">
                            <div style="flex-grow: 1;">
                                <div class="font-bold text-sm">
                                    {{ __('shop.checkout.payment_methods.apple_pay') }}
                                </div>
                                <div class="text-xs text-muted">
                                    1-Touch biometric authentication
                                </div>
                            </div>
                        </label>

                        <label class="payment-card-option">
                            <input type="radio" name="payment_method" value="cod" class="form-check-input">
                            <div style="flex-grow: 1;">
                                <div class="font-bold text-sm">
                                    {{ __('shop.checkout.payment_methods.cod') }}
                                </div>
                                <div class="text-xs text-muted">
                                    Pay securely upon white-glove arrival
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="cart-summary-card">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">
                    Protocol Items ({{ count($summary['items']) }})
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1rem; max-height: 280px; overflow-y: auto;">
                    @foreach($summary['items'] as $item)
                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name_en'] }}" style="width: 50px; height: 50px; border-radius: var(--radius-sm); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                            <div style="flex-grow: 1;">
                                <div class="font-bold text-xs">
                                    {{ app()->getLocale() === 'ar' ? ($item['name_ar'] ?? $item['name_en']) : $item['name_en'] }}
                                </div>
                                <div class="text-xs text-muted">
                                    Qty: {{ $item['quantity'] }} × ${{ number_format($item['price'], 2) }}
                                </div>
                            </div>
                            <div class="font-bold text-sm">
                                ${{ number_format($item['total'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                    <div class="summary-row">
                        <span>{{ __('shop.cart.subtotal') }}</span>
                        <span class="font-bold">${{ number_format($summary['subtotal'], 2) }}</span>
                    </div>

                    <div class="summary-row" style="color: var(--color-success);">
                        <span>{{ __('shop.cart.discount') }} (WELCOME15)</span>
                        <span class="font-bold">-${{ number_format($summary['discount'], 2) }}</span>
                    </div>

                    <div class="summary-row">
                        <span>Insured Shipping</span>
                        <span class="font-bold text-success">Complimentary</span>
                    </div>

                    <div class="summary-row">
                        <span>Estimated VAT (15%)</span>
                        <span>${{ number_format($summary['tax'], 2) }}</span>
                    </div>

                    <div class="summary-row total">
                        <span>{{ __('shop.cart.total') }}</span>
                        <span>${{ number_format($summary['total'], 2) }}</span>
                    </div>
                </div>

                <a href="{{ route('customer.account.orders.show', 'BZ-10492') }}" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 0.5rem;">
<<<<<<< HEAD
                    🔒 {{ __('shop.checkout.place_order') }}
                </a>

                <div class="text-xs text-muted text-center" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <span>🛡️ 30-Day Cellular Efficacy Guarantee</span>
=======
                    <i class="fa-solid fa-lock mr-1.5 ml-1.5"></i> {{ __('shop.checkout.place_order') }}
                </a>

                <div class="text-xs text-muted text-center" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <span><i class="fa-solid fa-shield-halved text-success mr-1 ml-1"></i> 30-Day Cellular Efficacy Guarantee</span>
>>>>>>> origin/main
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
