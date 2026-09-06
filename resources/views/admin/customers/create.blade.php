<x-layouts.admin 
    :pageTitle="__('admin.customers.create_title')" 
    pageSubtitle="Manually create a VIP member profile or clinic account."
    :breadcrumbs="['Customers' => route('admin.customers.index'), 'Create' => route('admin.customers.create')]"
>
    <form method="POST" action="{{ route('admin.customers.store') }}">
        @csrf

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
                <x-forms.input name="name" :label="__('admin.customers.name')" placeholder="e.g. Dr. Zaid Al-Harbi" :value="old('name')" required />
                <x-forms.input name="phone" type="tel" :label="__('admin.customers.phone')" placeholder="+966 50 123 4567" :value="old('phone')" />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="email" type="email" :label="__('admin.customers.email')" placeholder="zaid@example.com" :value="old('email')" required />
                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.customers.status') }}</label>
                    <select name="status" class="form-select text-sm w-full">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="city" :label="__('admin.customers.city')" placeholder="Riyadh" :value="old('city')" />
                <x-forms.input name="country" :label="__('admin.customers.country')" placeholder="Saudi Arabia" :value="old('country', 'Saudi Arabia')" />
            </div>

            <x-forms.input name="address" :label="__('admin.customers.address')" placeholder="Al-Olaya District, Building 42" :value="old('address')" />
        </div>
    </form>
</x-layouts.admin>
