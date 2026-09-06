<x-layouts.admin 
    :pageTitle="'Edit Role: ' . $role['name']" 
    pageSubtitle="Adjust system rights, operational boundaries, and matrix checkboxes."
    :breadcrumbs="['Roles' => route('admin.roles.index'), $role['name'] => route('admin.roles.edit', $role['id'])]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
        <button type="button" class="btn btn-primary" onclick="alert('Role updated!')">💾 Save Role Matrix</button>
    </x-slot>

    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- Role Core -->
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Role Identity
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="name" label="Role Name" :value="$role['name']" required />
                <x-forms.input name="slug" label="Role Key / Slug" :value="$role['slug']" required />
            </div>

            <x-forms.textarea name="description" label="Scope & Operational Description" rows="2" :value="$role['description']" />
        </div>

        <!-- Matrix -->
        <div class="card" style="padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">Granular Permission Matrix</h3>
                    <p class="text-xs text-muted" style="margin: 0;">Assigned privileges for {{ $role['name'] }}.</p>
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
                                    <input 
                                        type="checkbox" 
                                        name="permissions[{{ $moduleKey }}][{{ $actionKey }}]" 
                                        class="form-check-input matrix-checkbox" 
                                        value="1" 
                                        {{ ($role['permissions'] === '*' || in_array($moduleKey . '.' . $actionKey, (array)$role['permissions']) || in_array($moduleKey, (array)$role['permissions'])) ? 'checked' : '' }}
                                    >
                                    <span class="text-xs font-semibold">{{ $actionLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
