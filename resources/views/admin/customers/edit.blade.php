<x-layouts.admin 
    :pageTitle="__('admin.customers.edit_title', ['name' => $customer['name'] ?? 'Client'])" 
    pageSubtitle="Update VIP member profile, address credentials and status."
    :breadcrumbs="['Customers' => route('admin.customers.index'), ($customer['name'] ?? 'Client') => route('admin.customers.edit', $customer['id'] ?? 1)]"
>
    <form method="POST" action="{{ route('admin.customers.update', $customer['id'] ?? 1) }}">
        @csrf
        @method('PUT')

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                {{ __('app.actions.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
            </button>
        </div>

        <div class="card" style="padding: 2rem; max-width: 800px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="name" :label="__('admin.customers.name')" :value="old('name', $customer['name'] ?? '')" required />
                <x-forms.input name="phone" type="tel" :label="__('admin.customers.phone')" :value="old('phone', $customer['phone'] ?? '')" />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="email" type="email" :label="__('admin.customers.email')" :value="old('email', $customer['email'] ?? '')" required />
                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.customers.status') }}</label>
                    <select name="status" class="form-select text-sm w-full">
                        <option value="active" {{ old('status', $customer['status'] ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $customer['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="city" :label="__('admin.customers.city')" :value="old('city', $customer['city'] ?? '')" />
                <x-forms.input name="country" :label="__('admin.customers.country')" :value="old('country', $customer['country'] ?? 'Saudi Arabia')" />
            </div>

            <x-forms.input name="address" :label="__('admin.customers.address')" :value="old('address', $customer['address'] ?? '')" />
        </div>
    </form>
</x-layouts.admin>
