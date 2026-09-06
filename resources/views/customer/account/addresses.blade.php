<x-layouts.customer :title="__('shop.account.addresses') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.addresses') }}
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
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link active">
                    📍 {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link">
                    👤 {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link">
                    ⚙️ {{ __('shop.account.settings') }}
                </a>
            </aside>

            <!-- Addresses Grid -->
            <div>
                <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
                    <button type="button" class="btn btn-primary btn-sm">
                        + Add New Delivery Destination
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    @foreach($addresses as $address)
                        <div class="card" style="padding: 1.5rem; position: relative;">
                            @if($address['is_default'])
                                <span class="badge badge-accent" style="position: absolute; top: 1rem; inset-inline-end: 1rem;">
                                    Default Delivery
                                </span>
                            @endif

                            <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $address['title'] }}
                            </h4>

                            <div class="text-sm text-secondary" style="display: flex; flex-direction: column; gap: 0.25rem; margin-bottom: 1.5rem;">
                                <div class="font-bold text-primary">{{ $address['recipient'] }}</div>
                                <div>{{ $address['street'] }}</div>
                                <div>{{ $address['city'] }}, {{ $address['country'] }} ({{ $address['postal_code'] }})</div>
                                <div class="text-muted">{{ $address['phone'] }}</div>
                            </div>

                            <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                                <button type="button" class="btn btn-secondary btn-sm">
                                    {{ __('app.actions.edit') }}
                                </button>
                                @if(!$address['is_default'])
                                    <button type="button" class="btn btn-ghost btn-sm text-danger">
                                        {{ __('app.actions.delete') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
