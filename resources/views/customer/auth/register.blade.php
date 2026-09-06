<x-layouts.auth :title="__('app.nav.register')">
    <div class="card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: center;">
            {{ __('app.nav.register') }}
        </h2>
        <p class="text-sm text-muted text-center" style="margin-bottom: 2rem;">
            Initiate your clinical account to track biomarker goals and stack subscriptions.
        </p>

        <form action="{{ route('customer.account.dashboard') }}" method="GET">
            <x-forms.input 
                name="name" 
                :label="__('shop.checkout.full_name')" 
                placeholder="Dr. Zaid Al-Harbi" 
                required 
            />

            <x-forms.input 
                name="email" 
                type="email" 
                :label="__('shop.checkout.email')" 
                placeholder="name@example.com" 
                required 
            />

            <x-forms.input 
                name="password" 
                type="password" 
                label="Create Password" 
                placeholder="••••••••" 
                required 
                hint="Minimum 8 characters with at least one uppercase letter and symbol."
            />

            <x-forms.input 
                name="password_confirmation" 
                type="password" 
                label="Confirm Password" 
                placeholder="••••••••" 
                required 
            />

            <div style="margin-bottom: 1.5rem;">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" checked required>
                    <span class="text-xs">
                        I agree to the <a href="{{ route('customer.pages.terms') }}" class="font-bold" style="color: var(--color-primary);">Terms of Protocol Service</a> and <a href="{{ route('customer.pages.privacy') }}" class="font-bold" style="color: var(--color-primary);">Privacy Policy</a>.
                    </span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                {{ __('app.nav.register') }} →
            </button>
        </form>

        <div style="margin-top: 1.75rem; text-align: center; font-size: 0.875rem; color: var(--color-text-muted);">
            Already registered? 
            <a href="{{ route('customer.auth.login') }}" class="font-bold" style="color: var(--color-primary);">
                {{ __('app.nav.login') }}
            </a>
        </div>
    </div>
</x-layouts.auth>
