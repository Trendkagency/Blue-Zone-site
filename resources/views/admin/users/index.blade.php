<x-layouts.admin 
    :pageTitle="__('admin.menu.users')" 
    pageSubtitle="Manage administrative users, store specialists, inventory leads, and assigned roles."
    :breadcrumbs="['Users' => route('admin.users.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ New Staff User</a>
    </x-slot>

    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Mobile</th>
                        <th>Assigned Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>
                                <div class="font-bold text-sm text-primary">{{ $u['name'] }}</div>
                                <div class="text-xs text-muted">{{ $u['email'] }}</div>
                            </td>
                            <td class="text-xs">{{ $u['mobile'] }}</td>
                            <td>
                                <span class="badge badge-accent text-xs">{{ $u['role'] }}</span>
                            </td>
                            <td><x-status-badge :status="$u['status']" /></td>
                            <td class="text-xs text-muted">{{ $u['last_login_at'] }}</td>
                            <td class="text-xs text-muted">{{ $u['created_at'] }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.users.edit', $u['id']) }}" class="action-btn" title="Edit">✏️</a>
                                    <button type="button" class="action-btn action-danger" title="Suspend">🔒</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
