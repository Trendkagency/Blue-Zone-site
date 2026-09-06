<x-layouts.admin 
    pageTitle="Create Staff User" 
    pageSubtitle="Grant administrative or cashier console access to staff members."
    :breadcrumbs="['Users' => route('admin.users.index'), 'Create' => route('admin.users.create')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
        <button type="button" class="btn btn-primary" onclick="alert('User created!')">💾 {{ __('app.actions.save') }}</button>
    </x-slot>

    <div class="card" style="padding: 2rem; max-width: 800px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="name" label="Staff Name" placeholder="e.g. Omar Al-Mansoor" required />
            <x-forms.input name="mobile" type="tel" label="Mobile Phone" placeholder="+966 55 111 2233" required />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="email" type="email" label="Official Email" placeholder="omar.m@bluezone.com" required />
            <x-forms.select 
                name="role_id" 
                label="Assigned System Role" 
                :options="[
                    '1' => 'Super Admin',
                    '2' => 'Inventory Manager',
                    '3' => 'Sales User (POS / Retail)',
                    '4' => 'Content Manager',
                ]" 
                required 
            />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="password" type="password" label="Temporary Password" placeholder="••••••••" required />
            <x-forms.input name="password_confirmation" type="password" label="Confirm Password" placeholder="••••••••" required />
        </div>

        <x-forms.toggle name="is_active" label="Account Active Status" description="Enable access to the BZ-OS dashboard immediately." checked />
    </div>
</x-layouts.admin>
