<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\View\ViewModels\RoleViewModel;
use App\View\ViewModels\UserViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $roleFilter = $request->query('role_id');
        $isTrashed = $status === 'trashed';

        $query = User::with('role');

        if ($isTrashed) {
            $query->onlyTrashed();
        } elseif ($status && in_array($status, ['active', 'inactive', 'suspended'])) {
            $query->where('status', $status);
        }

        if ($roleFilter) {
            $query->where('role_id', $roleFilter);
        }

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $trashedCount = User::onlyTrashed()->count();
        $activeCount = User::where('status', 'active')->count();
        $totalDbCount = User::withTrashed()->count();
        $dbUsers = $query->latest()->paginate(15)->withQueryString();

        if ($totalDbCount > 0) {
            $users = $dbUsers->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'phone' => $u->phone,
                    'role' => $u->role?->name ?? 'Admin',
                    'role_id' => $u->role_id,
                    'status' => $u->status ?? 'active',
                    'avatar' => $u->avatar ?? null,
                    'avatar_url' => $u->avatar_url,
                    'last_login' => $u->updated_at?->diffForHumans() ?? 'Recently',
                    'deleted_at' => $u->deleted_at,
                ];
            })->toArray();
            $currentPage = $dbUsers->currentPage();
            $totalPages = $dbUsers->lastPage();
        } else {
            $users = UserViewModel::all();
            $currentPage = 1;
            $totalPages = 1;
        }

        $dbRoles = Role::all();
        $roles = $dbRoles->isNotEmpty() ? $dbRoles->toArray() : RoleViewModel::all();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'trashedCount' => $trashedCount,
            'activeCount' => $activeCount,
            'isTrashed' => $isTrashed,
            'currentStatus' => $status,
            'currentRole' => $roleFilter,
        ]);
    }

    public function create(): View
    {
        $dbRoles = Role::all();
        $roles = $dbRoles->isNotEmpty() ? $dbRoles->toArray() : RoleViewModel::all();

        return view('admin.users.create', ['roles' => $roles]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|integer|exists:roles,id',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم إضافة المستخدم [{$user->name}] وتعيين صلاحياته بنجاح!" 
                : "User [{$user->name}] added successfully!");
    }

    public function show(int $id): View
    {
        $dbUser = User::with('role')->find($id);

        if ($dbUser) {
            $user = [
                'id' => $dbUser->id,
                'name' => $dbUser->name,
                'email' => $dbUser->email,
                'phone' => $dbUser->phone ?? null,
                'bio' => $dbUser->bio ?? null,
                'role' => $dbUser->role?->name ?? 'Admin',
                'role_id' => $dbUser->role_id,
                'role_description' => $dbUser->role?->description ?? '',
                'status' => $dbUser->status ?? 'active',
                'avatar' => $dbUser->avatar ?? null,
                'avatar_url' => $dbUser->avatar_url,
                'email_verified_at' => $dbUser->email_verified_at,
                'has_fcm' => !empty($dbUser->fcm_token),
                'fcm_device' => $dbUser->fcm_device_info['device'] ?? ($dbUser->fcm_device_info['platform'] ?? null),
                'created_at' => $dbUser->created_at?->format('d M Y, h:i A') ?? 'N/A',
                'updated_at' => $dbUser->updated_at?->diffForHumans() ?? 'Recently',
                'deleted_at' => $dbUser->deleted_at,
            ];

            // Evaluate granular permissions for all known modules
            $modules = RoleViewModel::modules();
            $actions = ['view', 'create', 'edit', 'delete'];
            $evaluatedPermissions = [];

            foreach ($modules as $modKey => $modLabel) {
                $evaluatedPermissions[$modKey] = [
                    'label' => $modLabel,
                    'actions' => [],
                ];
                foreach ($actions as $act) {
                    $evaluatedPermissions[$modKey]['actions'][$act] = $dbUser->hasPermission("{$modKey}.{$act}");
                }
            }
        } else {
            $users = UserViewModel::all();
            $found = null;
            foreach ($users as $u) {
                if ($u['id'] === $id) {
                    $found = $u;
                    break;
                }
            }
            $user = $found ?? ($users[0] ?? [
                'id' => 1,
                'name' => 'Administrator',
                'email' => 'admin@bluezone.com',
                'phone' => '+966 50 123 4567',
                'bio' => 'System Director',
                'role' => 'Super Admin',
                'status' => 'active',
                'created_at' => now()->format('d M Y'),
                'updated_at' => 'Recently',
            ]);
            $modules = RoleViewModel::modules();
            $evaluatedPermissions = [];
            foreach ($modules as $modKey => $modLabel) {
                $evaluatedPermissions[$modKey] = [
                    'label' => $modLabel,
                    'actions' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                ];
            }
        }

        return view('admin.users.show', [
            'user' => $user,
            'permissionsMatrix' => $evaluatedPermissions,
        ]);
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->back()
                ->with('error', app()->getLocale() === 'ar'
                    ? 'لا يمكنك تعطيل أو تغيير حالة حسابك الشخصي الحالي أثناء تسجيل الدخول.'
                    : 'You cannot suspend or modify your own currently logged-in account.');
        }

        $newStatus = ($user->status === 'active') ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        $msgAr = $newStatus === 'active' 
            ? "تم تنشيط وتفعيل حساب المستخدم [{$user->name}] بنجاح." 
            : "تم إيقاف وتعليق حساب المستخدم [{$user->name}] بنجاح.";
        $msgEn = $newStatus === 'active'
            ? "User [{$user->name}] account has been activated successfully."
            : "User [{$user->name}] account has been suspended.";

        return redirect()->back()
            ->with('success', app()->getLocale() === 'ar' ? $msgAr : $msgEn);
    }

    public function edit(int $id): View
    {
        $dbUser = User::find($id);

        if ($dbUser) {
            $user = [
                'id' => $dbUser->id,
                'name' => $dbUser->name,
                'email' => $dbUser->email,
                'phone' => $dbUser->phone ?? '',
                'bio' => $dbUser->bio ?? '',
                'role_id' => $dbUser->role_id,
                'status' => $dbUser->status ?? 'active',
                'avatar' => $dbUser->avatar ?? 'assets/avatars/user-1.jpg',
            ];
        } else {
            $users = UserViewModel::all();
            $user = null;
            foreach ($users as $u) {
                if ($u['id'] === $id) {
                    $user = $u;
                    break;
                }
            }
        }

        $dbRoles = Role::all();
        $roles = $dbRoles->isNotEmpty() ? $dbRoles->toArray() : RoleViewModel::all();

        return view('admin.users.edit', [
            'user' => $user ?? ($users[0] ?? []),
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|integer|exists:roles,id',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'role_id' => $validated['role_id'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم تحديث بيانات المستخدم [{$user->name}] بنجاح!" 
                : "User [{$user->name}] updated successfully!");
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Protect super admin self-deletion
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', app()->getLocale() === 'ar'
                    ? 'لا يمكنك حذف حسابك الشخصي الحالي أثناء تسجيل الدخول.'
                    : 'You cannot delete your own logged-in account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم نقل المستخدم [{$name}] إلى سلة المحذوفات بنجاح." 
                : "User [{$name}] moved to trash successfully.");
    }

    public function restore(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $name = $user->name;
        $user->restore();

        return redirect()->route('admin.users.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم استعادة حساب المستخدم [{$name}] بنجاح!" 
                : "User [{$name}] restored successfully!");
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', app()->getLocale() === 'ar'
                    ? 'لا يمكنك حذف حسابك الشخصي نهائياً.'
                    : 'You cannot permanently delete your own account.');
        }

        $name = $user->name;
        $user->forceDelete();

        return redirect()->route('admin.users.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم الحذف النهائي للمستخدم [{$name}] نهائياً!" 
                : "User [{$name}] permanently deleted!");
    }
}
