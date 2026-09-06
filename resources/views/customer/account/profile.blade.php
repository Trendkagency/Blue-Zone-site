<x-layouts.customer :title="__('shop.account.profile') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.profile') }}
        </h1>

        <div class="account-layout">
            <!-- Navigation -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link">
                    📊 {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
                    📦 {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link">
                    🧾 {{ __('shop.account.invoices') }}
                </a>
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link">
                    📍 {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link active">
                    👤 {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link">
                    ⚙️ {{ __('shop.account.settings') }}
                </a>
            </aside>

            <!-- Profile Form Card -->
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">
                    Clinical Account Dossier
                </h3>

                <form action="#" method="GET">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-forms.input 
                            name="name" 
                            :label="__('shop.checkout.full_name')" 
                            :value="$customer['name']" 
                            required 
                        />
                        <x-forms.input 
                            name="phone" 
                            type="tel" 
                            :label="__('shop.checkout.phone')" 
                            :value="$customer['phone']" 
                            required 
                        />
                    </div>

                    <x-forms.input 
                        name="email" 
                        type="email" 
                        :label="__('shop.checkout.email')" 
                        :value="$customer['email']" 
                        required 
                    />

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-forms.input 
                            name="city" 
                            :label="__('shop.checkout.city')" 
                            :value="$customer['city']" 
                        />
                        <x-forms.input 
                            name="country" 
                            :label="__('shop.checkout.country')" 
                            :value="$customer['country']" 
                        />
                    </div>

                    <div style="margin-top: 1rem;">
                        <button type="button" class="btn btn-primary">
                            {{ __('app.actions.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.customer>
