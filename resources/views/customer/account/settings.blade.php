<x-layouts.customer :title="__('shop.account.settings') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.settings') }}
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
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link">
                    👤 {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link active">
                    ⚙️ {{ __('shop.account.settings') }}
                </a>
            </aside>

            <!-- Settings Sections -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="card" style="padding: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                        Security & Authentication
                    </h3>

                    <form action="#" method="GET">
                        <x-forms.input 
                            name="current_password" 
                            type="password" 
                            label="Current Password" 
                            placeholder="••••••••" 
                        />
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <x-forms.input 
                                name="new_password" 
                                type="password" 
                                label="New Password" 
                                placeholder="••••••••" 
                            />
                            <x-forms.input 
                                name="new_password_confirmation" 
                                type="password" 
                                label="Confirm New Password" 
                                placeholder="••••••••" 
                            />
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;">
                            Update Password
                        </button>
                    </form>
                </div>

                <div class="card" style="padding: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                        Communications & Clinical Alerts
                    </h3>

                    <x-forms.toggle 
                        name="email_orders" 
                        label="Fulfillment & Cold-Chain Tracking Updates" 
                        description="Receive real-time courier checkpoints and dispatch alerts." 
                        checked 
                    />
                    <x-forms.toggle 
                        name="email_science" 
                        label="Biochemical Research & Longevity Journal Dispatches" 
                        description="Periodic clinical summaries from our Scientific Advisory Board." 
                        checked 
                    />
                    <x-forms.toggle 
                        name="sms_orders" 
                        label="Instant SMS Dispatch Notifications" 
                        description="Delivery SMS alert with courier PIN code." 
                    />
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
