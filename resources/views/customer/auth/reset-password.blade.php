<x-layouts.auth title="Set New Password — Blue Zone">
    <div class="card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: center;">
            Set New Password
        </h2>
        <p class="text-sm text-muted text-center" style="margin-bottom: 2rem;">
            Please choose a robust password for your account security.
        </p>

        <form action="{{ route('customer.auth.login') }}" method="GET">
            <x-forms.input 
                name="password" 
                type="password" 
                label="New Password" 
                placeholder="••••••••" 
                required 
            />

            <x-forms.input 
                name="password_confirmation" 
                type="password" 
                label="Confirm New Password" 
                placeholder="••••••••" 
                required 
            />

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
                Update Password & Sign In →
            </button>
        </form>
    </div>
</x-layouts.auth>
