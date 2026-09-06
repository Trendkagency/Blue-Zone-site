<x-layouts.admin 
    :pageTitle="__('admin.menu.roles')" 
    pageSubtitle="Configure enterprise role-based access control (RBAC) and granular permission matrices."
    :breadcrumbs="['Access Control' => route('admin.roles.index'), 'Roles' => route('admin.roles.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">+ New Custom Role</a>
    </x-slot>

    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Slug</th>
                        <th>Scope & Description</th>
                        <th>Assigned Staff</th>
                        <th>Permission Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $r)
                        <tr>
                            <td class="font-bold text-primary">
                                <a href="{{ route('admin.roles.edit', $r['id']) }}">
                                    {{ $r['name'] }}
                                </a>
                            </td>
                            <td class="font-mono text-xs">{{ $r['slug'] }}</td>
                            <td class="text-sm text-secondary">{{ $r['description'] }}</td>
                            <td class="font-bold">{{ $r['users_count'] }} users</td>
                            <td>
                                <span class="badge badge-neutral text-xs font-mono">
                                    {{ $r['permissions'] === '*' ? 'Full System Authority (*)' : count($r['permissions']) . ' Modules' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $r['id']) }}" class="action-btn" title="Configure Matrix">
                                    ⚙️
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
