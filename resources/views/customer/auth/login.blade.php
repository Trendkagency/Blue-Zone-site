<x-layouts.auth :title="__('app.nav.login')">
    <div class="card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: center;">
            {{ __('app.nav.login') }}
        </h2>
        <p class="text-sm text-muted text-center" style="margin-bottom: 2rem;">
            Access your personalized longevity protocols and clinical records.
        </p>

        <form action="{{ route('customer.account.dashboard') }}" method="GET">
            <x-forms.input 
                name="email" 
                type="email" 
                :label="__('shop.checkout.email')" 
                placeholder="name@example.com" 
                value="zaid.harbi@example.com" 
                required 
            />

            <x-forms.input 
                name="password" 
                type="password" 
                label="Password" 
                placeholder="••••••••" 
                value="secretpassword" 
                required 
            />

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" checked>
                    <span class="text-xs font-semibold">Remember this device</span>
                </label>

                <a href="{{ route('customer.auth.forgot-password') }}" class="text-xs font-bold" style="color: var(--color-primary);">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                {{ __('app.nav.login') }} →
            </button>
        </form>

        <div style="margin-top: 1.75rem; text-align: center; font-size: 0.875rem; color: var(--color-text-muted);">
            Don't have a protocol profile? 
            <a href="{{ route('customer.auth.register') }}" class="font-bold" style="color: var(--color-primary);">
                {{ __('app.nav.register') }}
            </a>
        </div>
    </div>
</x-layouts.auth>
