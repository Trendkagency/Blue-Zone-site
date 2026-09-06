<x-layouts.admin 
<<<<<<< HEAD
    pageTitle="Define Security Role" 
    pageSubtitle="Establish custom permission policies across catalog, sales, inventory, and analytics."
    :breadcrumbs="['Roles' => route('admin.roles.index'), 'Create' => route('admin.roles.create')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
        <button type="button" class="btn btn-primary" onclick="alert('Security role saved!')">💾 Save Role Matrix</button>
    </x-slot>

    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- Role Core -->
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Role Identity
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="name" label="Role Name" placeholder="e.g. Regional Fulfillment Lead" required />
                <x-forms.input name="slug" label="Role Key / Slug" placeholder="regional-fulfillment-lead" required />
            </div>

            <x-forms.textarea name="description" label="Scope & Operational Description" rows="2" placeholder="Authorized to oversee logistics transfers and packing verifications across regional hubs..." />
        </div>

        <!-- Granular Permission Matrix -->
        <div class="card" style="padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">Granular Permission Matrix</h3>
                    <p class="text-xs text-muted" style="margin: 0;">Check individual module capabilities for this role.</p>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = true);">Select All</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = false);">Deselect All</button>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                @foreach($permissionMatrix as $moduleKey => $module)
                    <div style="background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
                        <h4 style="font-size: 1rem; font-weight: 800; color: var(--color-primary); margin-bottom: 0.75rem;">
                            {{ $module['label'] }}
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            @foreach($module['actions'] as $actionKey => $actionLabel)
                                <label class="form-check">
                                    <input type="checkbox" name="permissions[{{ $moduleKey }}][{{ $actionKey }}]" class="form-check-input matrix-checkbox" value="1">
                                    <span class="text-xs font-semibold">{{ $actionLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
=======
    :pageTitle="__('admin.roles.create_title')" 
    :pageSubtitle="__('admin.roles.create_subtitle')"
    :breadcrumbs="[__('admin.menu.roles') => route('admin.roles.index'), __('app.actions.create') => route('admin.roles.create')]"
>
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
            <button type="submit" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('admin.roles.save_role') }}
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Role Core -->
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    {{ __('admin.roles.role_identity') }}
                </h3>

                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                    <x-forms.input name="name" :label="__('admin.roles.role_name')" placeholder="e.g. Regional Fulfillment Lead" :value="old('name')" required />
                </div>

                <x-forms.textarea name="description" :label="__('admin.roles.description')" rows="2" placeholder="Authorized to oversee logistics transfers and packing verifications across regional hubs..." :value="old('description')" />
            </div>

            <!-- Granular Permission Matrix -->
            <div class="card" style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">{{ __('admin.roles.permission_matrix') }}</h3>
                        <p class="text-xs text-muted" style="margin: 0;">{{ app()->getLocale() == 'ar' ? 'حدد الصلاحيات والوظائف المسموحة لهذا الدور الأمني.' : 'Check individual module capabilities for this role.' }}</p>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = true);">{{ __('admin.roles.select_all') }}</button>
                        <button type="button" class="btn btn-ghost btn-sm" onclick="document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = false);">{{ __('admin.roles.deselect_all') }}</button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    @php
                        $actions = [
                            'view' => app()->getLocale() === 'ar' ? 'عرض السجلات' : 'View Records',
                            'create' => app()->getLocale() === 'ar' ? 'إنشاء جديد' : 'Create / Add',
                            'edit' => app()->getLocale() === 'ar' ? 'تعديل وحفظ' : 'Edit / Update',
                            'delete' => app()->getLocale() === 'ar' ? 'حذف وأرشفة' : 'Delete / Archive',
                        ];
                    @endphp

                    @foreach($modules as $moduleKey => $moduleLabel)
                        <div style="background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
                            <h4 style="font-size: 1rem; font-weight: 800; color: var(--color-primary); margin-bottom: 0.75rem;">
                                {{ $moduleLabel }}
                            </h4>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                @foreach($actions as $actionKey => $actionLabel)
                                    <label class="form-check flex items-center gap-2">
                                        <input type="checkbox" name="permissions[{{ $moduleKey }}][{{ $actionKey }}]" class="form-check-input matrix-checkbox" value="1">
                                        <span class="text-xs font-semibold">{{ $actionLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
>>>>>>> origin/main
</x-layouts.admin>
