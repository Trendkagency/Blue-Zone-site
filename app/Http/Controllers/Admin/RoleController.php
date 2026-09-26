<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
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
            'totalCount' => $totalDbCount > 0 ? $dbRoles->total() : count($roles),
            'trashedCount' => $trashedCount,
            'activeCount' => $activeCount,
            'isTrashed' => $isTrashed,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Granular Permission Matrix across all roles and system sections.
     */
    public function matrix(Request $request): View
    {
        $roles = Role::withCount('users')->orderBy('id')->get();
        $categorizedModules = RoleViewModel::categorizedModules();
        $actions = RoleViewModel::actions();

        $totalModulesCount = 0;
        foreach ($categorizedModules as $domainMeta) {
            $totalModulesCount += count($domainMeta['modules']);
        }

        $matrixData = [];
        foreach ($roles as $role) {
            $rawPerms = (array)($role->permissions ?? []);
            if (is_string($rawPerms)) {
                $rawPerms = json_decode($rawPerms, true) ?: [$rawPerms];
            }

            $isWildcard = in_array('*', $rawPerms, true) || in_array('all', $rawPerms, true) || isset($rawPerms['*']);
            $roleMatrix = [];
            $grantedCount = 0;

            foreach ($categorizedModules as $domainKey => $domainMeta) {
                foreach ($domainMeta['modules'] as $modKey => $modMeta) {
                    $modPerms = [];
                    $domainWildcard = false;

                    if (str_starts_with($modKey, 'mr_') && (in_array('mr.*', $rawPerms, true) || in_array('mr', $rawPerms, true))) {
                        $domainWildcard = true;
                    }
                    if (str_starts_with($modKey, 'crm_') && (in_array('crm.*', $rawPerms, true) || in_array('crm', $rawPerms, true))) {
                        $domainWildcard = true;
                    }
                    if (str_starts_with($modKey, 'hr_') && (in_array('hr.*', $rawPerms, true) || in_array('hr', $rawPerms, true))) {
                        $domainWildcard = true;
                    }

                    $aliases = [$modKey];
                    if (str_starts_with($modKey, 'hr_')) {
                        $aliases[] = substr($modKey, 3);
                    }
                    if (str_starts_with($modKey, 'crm_')) {
                        $aliases[] = substr($modKey, 4);
                    }
                    if (str_starts_with($modKey, 'mr_')) {
                        $aliases[] = substr($modKey, 3);
                    }
                    if ($modKey === 'hr_departments') {
                        $aliases[] = 'positions';
                    }
                    if ($modKey === 'offline_sales') {
                        $aliases[] = 'pos';
                    }
                    if ($modKey === 'content') {
                        $aliases[] = 'cms';
                    }

                    $moduleWildcard = false;
                    foreach ($aliases as $alias) {
                        if (in_array("{$alias}.*", $rawPerms, true) || in_array($alias, $rawPerms, true)) {
                            $moduleWildcard = true;
                            break;
                        }
                    }

                    foreach ($actions as $actKey => $actMeta) {
                        $isGranted = $isWildcard || $domainWildcard || $moduleWildcard;

                        if (!$isGranted) {
                            foreach ($aliases as $alias) {
                                if (in_array("{$alias}.{$actKey}", $rawPerms, true)) {
                                    $isGranted = true;
                                    break;
                                }
                                if ($actKey === 'edit' && (in_array("{$alias}.update", $rawPerms, true) || in_array("{$alias}.manage", $rawPerms, true))) {
                                    $isGranted = true;
                                    break;
                                }
                                if (isset($rawPerms[$alias][$actKey]) && $rawPerms[$alias][$actKey]) {
                                    $isGranted = true;
                                    break;
                                }
                            }
                        }

                        $modPerms[$actKey] = $isGranted;
                        if ($isGranted) {
                            $grantedCount++;
                        }
                    }
                    $roleMatrix[$modKey] = $modPerms;
                }
            }

            $maxPossible = max(1, $totalModulesCount * count($actions));
            $matrixData[$role->id] = [
                'role' => $role,
                'matrix' => $roleMatrix,
                'is_wildcard' => $isWildcard,
                'granted_count' => $isWildcard ? $maxPossible : $grantedCount,
                'coverage_pct' => $isWildcard ? 100 : round(($grantedCount / $maxPossible) * 100),
            ];
        }

        return view('admin.roles.matrix', [
            'roles' => $roles,
            'categorizedModules' => $categorizedModules,
            'actions' => $actions,
            'matrixData' => $matrixData,
            'totalModulesCount' => $totalModulesCount,
            'totalRolesCount' => $roles->count(),
            'totalStaffCount' => $roles->sum('users_count'),
            'templates' => RoleViewModel::templates(),
            'activeRoleFilter' => $request->query('role_id'),
            'activeDomainFilter' => $request->query('domain'),
        ]);
    }

    /**
     * Bulk or single role update from the Granular Permission Matrix.
     */
    public function updateMatrix(Request $request): RedirectResponse
    {
        $roleId = $request->input('role_id');
        $role = Role::findOrFail($roleId);

        if ($request->has('apply_template') && !empty($request->input('template_key'))) {
            $templates = RoleViewModel::templates();
            $tmplKey = $request->input('template_key');
            if (isset($templates[$tmplKey])) {
                $role->update(['permissions' => $templates[$tmplKey]['permissions']]);
                return redirect()->back()->with('success', app()->getLocale() === 'ar'
                    ? "تم تطبيق قالب الصلاحيات [{$templates[$tmplKey]['name_ar']}] على الدور [{$role->name}] بنجاح!"
                    : "Template permissions applied to role [{$role->name}] successfully!");
            }
        }

        $submittedPermissions = $request->input('permissions', []);

        // If wildcard toggle requested
        if ($request->boolean('is_wildcard')) {
            $submittedPermissions = ['*'];
        }

        $role->update(['permissions' => $submittedPermissions]);

        return redirect()->back()->with('success', app()->getLocale() === 'ar'
            ? "تم حفظ وتحديث مصفوفة الصلاحيات للدور [{$role->name}] بنجاح!"
            : "Granular permissions for role [{$role->name}] saved and updated successfully!");
    }

    public function create(): View
    {
        $categorizedModules = RoleViewModel::categorizedModules();
        $actions = RoleViewModel::actions();
        $templates = RoleViewModel::templates();

        return view('admin.roles.create', [
            'categorizedModules' => $categorizedModules,
            'actions' => $actions,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'is_wildcard' => 'nullable|boolean',
        ]);

        $permissions = $validated['permissions'] ?? [];
        if (!empty($validated['is_wildcard'])) {
            $permissions = ['*'];
        }

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.roles.matrix')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم إنشاء الدور وتعيين مصفوفة الصلاحيات [{$role->name}] بنجاح!" 
                : "Role [{$role->name}] with granular permissions created successfully!");
    }

    public function edit(int $id): View
    {
        $dbRole = Role::findOrFail($id);

        $role = [
            'id' => $dbRole->id,
            'name' => $dbRole->name,
            'description' => $dbRole->description,
            'permissions' => (array) ($dbRole->permissions ?? []),
        ];

        $categorizedModules = RoleViewModel::categorizedModules();
        $actions = RoleViewModel::actions();
        $templates = RoleViewModel::templates();

        return view('admin.roles.edit', [
            'role' => $role,
            'dbRole' => $dbRole,
            'categorizedModules' => $categorizedModules,
            'actions' => $actions,
            'templates' => $templates,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'is_wildcard' => 'nullable|boolean',
        ]);

        $permissions = $validated['permissions'] ?? [];
        if (!empty($validated['is_wildcard'])) {
            $permissions = ['*'];
        }

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.roles.matrix')
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
