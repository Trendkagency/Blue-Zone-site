<x-layouts.admin 
    :pageTitle="'Edit: ' . $user['name']" 
    pageSubtitle="Update staff permissions, status, or reset authentication credentials."
    :breadcrumbs="['Users' => route('admin.users.index'), $user['name'] => route('admin.users.edit', $user['id'])]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
        <button type="button" class="btn btn-primary" onclick="alert('User updated!')">💾 {{ __('app.actions.save') }}</button>
    </x-slot>

    <div class="card" style="padding: 2rem; max-width: 800px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="name" label="Staff Name" :value="$user['name']" required />
            <x-forms.input name="mobile" type="tel" label="Mobile Phone" :value="$user['mobile']" required />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="email" type="email" label="Official Email" :value="$user['email']" required />
            <x-forms.select 
                name="role_id" 
                label="Assigned System Role" 
                :selected="$user['role_id']"
                :options="[
                    '1' => 'Super Admin',
                    '2' => 'Inventory Manager',
                    '3' => 'Sales User (POS / Retail)',
                    '4' => 'Content Manager',
                ]" 
                required 
            />
        </div>

        <x-forms.toggle name="is_active" label="Account Active Status" description="Toggle to suspend or grant access." :checked="$user['status'] === 'active'" />
    </div>
</x-layouts.admin>
