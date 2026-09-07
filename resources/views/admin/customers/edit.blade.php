<x-layouts.admin 
    :pageTitle="__('admin.customers.edit_title', ['name' => $customer['name'] ?? 'Client'])" 
    :pageSubtitle="__('admin.customers.edit_subtitle')"
    :breadcrumbs="[__('admin.menu.customers') => route('admin.customers.index'), ($customer['name'] ?? 'Client') => route('admin.customers.show', $customer['id'] ?? 1), __('app.actions.edit') => route('admin.customers.edit', $customer['id'] ?? 1)]"
>
    <form method="POST" action="{{ route('admin.customers.update', $customer['id'] ?? 1) }}">
        @csrf
        @method('PUT')

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.customers.show', $customer['id'] ?? 1) }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-eye mr-1.5 ml-1.5"></i> {{ __('admin.customers.view_profile') }}
            </a>
            <button type="submit" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
            </button>
        </div>

        <div class="card" style="padding: 2rem; max-width: 850px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="name" :label="__('admin.customers.name')" :value="old('name', $customer['name'] ?? '')" required />
                <x-forms.input name="email" type="email" :label="__('admin.customers.email')" :value="old('email', $customer['email'] ?? '')" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="phone" type="tel" :label="__('admin.customers.phone')" :value="old('phone', $customer['phone'] ?? '')" />

                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.customers.member_tier') }}</label>
                    <select name="tier" class="form-select text-sm w-full">
                        @foreach(['Member', 'Bronze', 'Silver', 'Gold', 'VIP'] as $t)
                            <option value="{{ $t }}" {{ old('tier', $customer['tier'] ?? 'Member') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.customers.status') }}</label>
                    <select name="status" class="form-select text-sm w-full">
                        <option value="active" {{ old('status', $customer['status'] ?? 'active') === 'active' ? 'selected' : '' }}>{{ __('app.fields.status') . ': Active' }}</option>
                        <option value="inactive" {{ old('status', $customer['status'] ?? '') === 'inactive' ? 'selected' : '' }}>{{ __('app.fields.status') . ': Inactive' }}</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="city" :label="__('admin.customers.city')" :value="old('city', $customer['city'] ?? '')" />
                <x-forms.input name="country" :label="__('admin.customers.country')" :value="old('country', $customer['country'] ?? 'Saudi Arabia')" />
                <x-forms.input name="postal_code" :label="__('admin.customers.postal_code')" :value="old('postal_code', $customer['postal_code'] ?? '')" />
            </div>

            <x-forms.input name="address" :label="__('admin.customers.address')" :value="old('address', $customer['address'] ?? '')" />

            <div class="pt-4 mt-2 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-bold mb-3 text-primary flex items-center gap-2">
                    <i class="fa-solid fa-key"></i>
                    {{ __('admin.customers.password_help') }}
                </h4>
                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                    <x-forms.input name="password" type="password" :label="__('admin.customers.new_password')" placeholder="{{ __('admin.customers.leave_blank_password') }}" />
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>
