<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\RoleViewModel;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = RoleViewModel::all();

        return view('admin.roles.index', [
            'roles' => $roles,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }

    public function create(): View
    {
        $modules = RoleViewModel::modules();
        return view('admin.roles.create', ['modules' => $modules]);
    }

    public function edit(int $id): View
    {
        $roles = RoleViewModel::all();
        $modules = RoleViewModel::modules();
        $role = null;
        foreach ($roles as $r) {
            if ($r['id'] === $id) {
                $role = $r;
                break;
            }
        }

        return view('admin.roles.edit', [
            'role' => $role ?? $roles[0],
            'modules' => $modules,
        ]);
    }
}
