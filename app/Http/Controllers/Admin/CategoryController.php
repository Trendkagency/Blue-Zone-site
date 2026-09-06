<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\CategoryViewModel;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = CategoryViewModel::all();

        return view('admin.categories.index', [
            'categories' => $categories,
            'currentPage' => 1,
            'totalPages' => 1,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function edit(int $id): View
    {
        $categories = CategoryViewModel::all();
        $category = null;
        foreach ($categories as $c) {
            if ($c['id'] === $id) {
                $category = $c;
                break;
            }
        }

        return view('admin.categories.edit', [
            'category' => $category ?? $categories[0],
        ]);
    }
}
