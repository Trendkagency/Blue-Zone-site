<x-layouts.admin 
    pageTitle="Register Customer Profile" 
    pageSubtitle="Manually create a VIP member profile or clinic account."
    :breadcrumbs="['Customers' => route('admin.customers.index'), 'Create' => route('admin.customers.create')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            {{ __('app.actions.cancel') }}
        </a>
        <button type="button" class="btn btn-primary" onclick="alert('Customer account created!')">
            💾 {{ __('app.actions.save') }}
        </button>
    </x-slot>

    <div class="card" style="padding: 2rem; max-width: 800px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="name" label="Full Name" placeholder="e.g. Dr. Zaid Al-Harbi" required />
            <x-forms.input name="phone" type="tel" label="Mobile Phone" placeholder="+966 50 123 4567" required />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="email" type="email" label="Email Address" placeholder="zaid@example.com" required />
            <x-forms.select 
                name="tier" 
                label="Member Tier" 
                :options="[
                    'Silver Protocol' => 'Silver Protocol',
                    'Gold Member' => 'Gold Member',
                    'Platinum Biohacker' => 'Platinum Biohacker (VIP)',
                ]" 
            />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="city" label="City" placeholder="Riyadh" />
            <x-forms.input name="country" label="Country" placeholder="Saudi Arabia" />
        </div>
    </div>
</x-layouts.admin>
