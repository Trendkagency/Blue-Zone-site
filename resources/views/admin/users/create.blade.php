<x-layouts.admin 
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
                <x-forms.input name="name" :label="__('admin.users.name')" placeholder="e.g. Dr. Rayan Al-Ghamdi" :value="old('name')" required />
                <x-forms.input name="email" type="email" :label="__('admin.users.email')" placeholder="rayan@bluezone.com" :value="old('email')" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="phone" :label="app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number'" placeholder="+966 50 123 4567" :value="old('phone')" />
                
                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.users.role') }}</label>
                    <select name="role_id" class="form-select text-sm w-full">
                        <option value="">{{ app()->getLocale() === 'ar' ? '-- حدد الدور الأمني --' : '-- Select Security Role --' }}</option>
                        @foreach($roles as $r)
                            <option value="{{ $r['id'] ?? $r->id }}" {{ old('role_id') == ($r['id'] ?? $r->id) ? 'selected' : '' }}>
                                {{ $r['name'] ?? $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.users.status') }}</label>
                    <select name="status" class="form-select text-sm w-full">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'نشط ومصرح (Active)' : 'Active' }}</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'معلق وموقوف (Suspended)' : 'Suspended' }}</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'غير نشط (Inactive)' : 'Inactive' }}</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="form-label font-bold text-sm mb-1.5 block">{{ app()->getLocale() === 'ar' ? 'نبذة تعريفية وملاحظات الموظف' : 'Staff Bio & Description' }}</label>
                <textarea name="bio" rows="2" class="form-control text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'أخصائي التغذية العلاجية وإطالة العمر الافتراضي...' : 'Clinical nutritionist & longevity formulations specialist...' }}">{{ old('bio') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="password" type="password" :label="__('admin.users.password')" placeholder="••••••••" required />
                <x-forms.input name="password_confirmation" type="password" :label="__('admin.users.password_confirmation')" placeholder="••••••••" required />
            </div>
        </div>
    </form>
</x-layouts.admin>
