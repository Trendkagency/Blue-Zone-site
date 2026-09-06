<x-layouts.admin 
<<<<<<< HEAD
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
=======
    :pageTitle="__('admin.users.create_title')" 
    :pageSubtitle="__('admin.users.create_subtitle')"
    :breadcrumbs="[__('admin.menu.users') => route('admin.users.index'), __('app.actions.create') => route('admin.users.create')]"
>
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
            <button type="submit" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
            </button>
        </div>

        <div class="card" style="padding: 2rem; max-width: 800px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="name" :label="__('admin.users.name')" placeholder="e.g. Omar Al-Mansoor" :value="old('name')" required />
                <x-forms.input name="email" type="email" :label="__('admin.users.email')" placeholder="omar.m@bluezone.com" :value="old('email')" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.users.role') }}</label>
                    <select name="role_id" class="form-select text-sm w-full">
                        @foreach($roles as $r)
                            <option value="{{ $r['id'] ?? $r->id }}" {{ old('role_id') == ($r['id'] ?? $r->id) ? 'selected' : '' }}>
                                {{ $r['name'] ?? $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.users.status') }}</label>
                    <select name="status" class="form-select text-sm w-full">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="password" type="password" :label="__('admin.users.password')" placeholder="••••••••" required />
                <x-forms.input name="password_confirmation" type="password" :label="__('admin.users.password_confirmation')" placeholder="••••••••" required />
            </div>
        </div>
    </form>
>>>>>>> origin/main
</x-layouts.admin>
