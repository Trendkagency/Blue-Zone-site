<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\RoleViewModel;
use App\View\ViewModels\UserViewModel;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = UserViewModel::all();
        $roles = RoleViewModel::all();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }

    public function create(): View
    {
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
}
