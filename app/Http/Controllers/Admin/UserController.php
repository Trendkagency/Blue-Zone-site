<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\View\ViewModels\RoleViewModel;
use App\View\ViewModels\UserViewModel;
=======
use App\Models\Role;
use App\Models\User;
use App\View\ViewModels\RoleViewModel;
use App\View\ViewModels\UserViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
>>>>>>> origin/main
use Illuminate\View\View;

class UserController extends Controller
{
<<<<<<< HEAD
    public function index(): View
    {
        $users = UserViewModel::all();
        $roles = RoleViewModel::all();
=======
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $isTrashed = $status === 'trashed';

        $query = User::with('role')->latest();

        if ($isTrashed) {
            $query->onlyTrashed();
        } elseif ($status && in_array($status, ['active', 'inactive', 'suspended'])) {
            $query->where('status', $status);
        }

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $trashedCount = User::onlyTrashed()->count();
        $activeCount = User::count();
        $dbUsers = $query->paginate(15)->withQueryString();

        if ($dbUsers->isNotEmpty()) {
            $users = $dbUsers->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role?->name ?? 'Admin',
                    'role_id' => $u->role_id,
                    'status' => $u->status ?? 'active',
                    'avatar' => $u->avatar ?? 'assets/avatars/user-1.jpg',
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
>>>>>>> origin/main

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
<<<<<<< HEAD
            'currentPage' => 1,
            'totalPages' => 1,
=======
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'trashedCount' => $trashedCount,
            'activeCount' => $activeCount,
            'isTrashed' => $isTrashed,
            'currentStatus' => $status,
>>>>>>> origin/main
        ]);
    }

    public function create(): View
    {
<<<<<<< HEAD
        $roles = RoleViewModel::all();
        return view('admin.users.create', ['roles' => $roles]);
    }

    public function edit(int $id): View
    {
        $users = UserViewModel::all();
        $roles = RoleViewModel::all();
        $user = null;
        foreach ($users as $u) {
            if ($u['id'] === $id) {
                $user = $u;
                break;
            }
        }

        return view('admin.users.edit', [
            'user' => $user ?? $users[0],
            'roles' => $roles,
        ]);
    }
=======
        $dbRoles = Role::all();
        $roles = $dbRoles->isNotEmpty() ? $dbRoles->toArray() : RoleViewModel::all();

        return view('admin.users.create', ['roles' => $roles]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|integer|exists:roles,id',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم إضافة المستخدم [{$user->name}] وتعيين صلاحياته بنجاح!" 
                : "User [{$user->name}] added successfully!");
    }

    public function edit(int $id): View
    {
        $dbUser = User::find($id);

        if ($dbUser) {
            $user = [
                'id' => $dbUser->id,
                'name' => $dbUser->name,
                'email' => $dbUser->email,
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
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|integer|exists:roles,id',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
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
                    ? "لا يمكنك حذف حسابك الشخصي الحالي أثناء تسجيل الدخول."
                    : "You cannot delete your own logged-in account.");
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
                    ? "لا يمكنك حذف حسابك الشخصي نهائياً."
                    : "You cannot permanently delete your own account.");
        }

        $name = $user->name;
        $user->forceDelete();

        return redirect()->route('admin.users.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم الحذف النهائي للمستخدم [{$name}] نهائياً!" 
                : "User [{$name}] permanently deleted!");
    }
>>>>>>> origin/main
}
