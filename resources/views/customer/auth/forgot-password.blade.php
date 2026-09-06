<x-layouts.auth title="Reset Password — Blue Zone">
    <div class="card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: center;">
            Recover Password
        </h2>
        <p class="text-sm text-muted text-center" style="margin-bottom: 2rem;">
            Provide your registered email address to receive a secure one-time cryptographic reset token.
        </p>

        <form action="{{ route('customer.auth.reset-password') }}" method="GET">
            <x-forms.input 
                name="email" 
                type="email" 
                :label="__('shop.checkout.email')" 
                placeholder="name@example.com" 
                required 
            />

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
                Send Recovery Instructions →
            </button>
        </form>

        <div style="margin-top: 1.75rem; text-align: center; font-size: 0.875rem; color: var(--color-text-muted);">
            Remembered your credentials? 
            <a href="{{ route('customer.auth.login') }}" class="font-bold" style="color: var(--color-primary);">
                Back to Sign In
            </a>
        </div>
    </div>
</x-layouts.auth>
