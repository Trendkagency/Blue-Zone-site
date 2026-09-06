<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\View\ViewModels\RoleViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $isTrashed = $status === 'trashed';

        $query = Role::withCount('users');

        if ($isTrashed) {
            $query->onlyTrashed();
        }

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $trashedCount = Role::onlyTrashed()->count();
        $activeCount = Role::count();
        $totalDbCount = Role::withTrashed()->count();
        $dbRoles = $query->paginate(15)->withQueryString();

        if ($totalDbCount > 0) {
            $roles = $dbRoles->map(function ($r) {
                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'description' => $r->description,
                    'users_count' => $r->users_count,
                    'permissions' => (array) ($r->permissions ?? []),
                    'is_system' => in_array(strtolower($r->name), ['super admin', 'admin']),
                    'deleted_at' => $r->deleted_at,
                ];
            })->toArray();
            $currentPage = $dbRoles->currentPage();
            $totalPages = $dbRoles->lastPage();
        } else {
            $roles = RoleViewModel::all();
            $currentPage = 1;
            $totalPages = 1;
        }

        return view('admin.roles.index', [
            'roles' => $roles,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'trashedCount' => $trashedCount,
            'activeCount' => $activeCount,
            'isTrashed' => $isTrashed,
            'currentStatus' => $status,
        ]);
    }

    public function create(): View
    {
        $modules = RoleViewModel::modules();
        return view('admin.roles.create', ['modules' => $modules]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم إنشاء الدور والصلاحية [{$role->name}] بنجاح!" 
                : "Role [{$role->name}] created successfully!");
    }

    public function edit(int $id): View
    {
        $dbRole = Role::find($id);

        if ($dbRole) {
            $role = [
                'id' => $dbRole->id,
                'name' => $dbRole->name,
                'description' => $dbRole->description,
                'permissions' => (array) ($dbRole->permissions ?? []),
            ];
        } else {
            $roles = RoleViewModel::all();
            $role = null;
            foreach ($roles as $r) {
                if ($r['id'] === $id) {
                    $role = $r;
                    break;
                }
            }
        }

        $modules = RoleViewModel::modules();

        return view('admin.roles.edit', [
            'role' => $role ?? ($roles[0] ?? []),
            'modules' => $modules,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم تحديث مصفوفة صلاحيات الدور [{$role->name}] بنجاح!" 
                : "Role permissions for [{$role->name}] updated successfully!");
    }

    public function destroy(int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        if (in_array(strtolower($role->name), ['super admin', 'admin'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', app()->getLocale() === 'ar'
                    ? "لا يمكن حذف الأدوار القيادية الرئيسية للنظام."
                    : "System protected roles cannot be deleted.");
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم نقل الدور [{$name}] إلى سلة المحذوفات بنجاح." 
                : "Role [{$name}] moved to trash successfully.");
    }

    public function restore(int $id): RedirectResponse
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $name = $role->name;
        $role->restore();

        return redirect()->route('admin.roles.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم استعادة الدور والصلاحيات [{$name}] بنجاح!" 
                : "Role [{$name}] restored successfully!");
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $role = Role::withTrashed()->findOrFail($id);

        if (in_array(strtolower($role->name), ['super admin', 'admin'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', app()->getLocale() === 'ar'
                    ? "لا يمكن حذف الأدوار القيادية الرئيسية للنظام نهائياً."
                    : "System protected roles cannot be permanently deleted.");
        }

        $name = $role->name;

        // Unlink users attached to this role
        User::where('role_id', $role->id)->update(['role_id' => null]);

        $role->forceDelete();

        return redirect()->route('admin.roles.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم الحذف النهائي للدور [{$name}] نهائياً!" 
                : "Role [{$name}] permanently deleted!");
    }
}
