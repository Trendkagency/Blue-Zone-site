<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\View\ViewModels\CategoryViewModel;
=======
use App\Models\Category;
use App\View\ViewModels\CategoryViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
>>>>>>> origin/main
use Illuminate\View\View;

class CategoryController extends Controller
{
<<<<<<< HEAD
    public function index(): View
    {
        $categories = CategoryViewModel::all();

        return view('admin.categories.index', [
            'categories' => $categories,
            'currentPage' => 1,
            'totalPages' => 1,
=======
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $isTrashed = $status === 'trashed';

        $query = Category::withCount('products')->orderBy('sort_order');

        if ($isTrashed) {
            $query->onlyTrashed();
        } elseif ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        $trashedCount = Category::onlyTrashed()->count();
        $activeCount = Category::count();
        $totalDbCount = Category::withTrashed()->count();

        if ($totalDbCount > 0) {
            $categories = $dbCategories->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name_en' => $cat->name_en,
                    'name_ar' => $cat->name_ar,
                    'slug' => $cat->slug,
                    'icon' => $cat->icon ?? '🧬',
                    'description_en' => $cat->description_en,
                    'description_ar' => $cat->description_ar,
                    'products_count' => $cat->products_count,
                    'is_active' => $cat->is_active,
                    'status' => $cat->is_active ? 'active' : 'inactive',
                    'sort_order' => $cat->sort_order ?? 1,
                    'deleted_at' => $cat->deleted_at,
                ];
            })->toArray();
            $currentPage = $dbCategories->currentPage();
            $totalPages = $dbCategories->lastPage();
        } else {
            $categories = CategoryViewModel::all();
            $currentPage = 1;
            $totalPages = 1;
        }

        return view('admin.categories.index', [
            'categories' => $categories,
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
        return view('admin.categories.create');
    }

<<<<<<< HEAD
    public function edit(int $id): View
    {
        $categories = CategoryViewModel::all();
        $category = null;
        foreach ($categories as $c) {
            if ($c['id'] === $id) {
                $category = $c;
                break;
=======
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'icon' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name_en']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 1;

        $category = Category::create($validated);

        if ($request->hasFile('banner_file')) {
            $category->addMediaFromRequest('banner_file')->toMediaCollection('banner');
        }

        if ($request->hasFile('icon_file')) {
            $category->addMediaFromRequest('icon_file')->toMediaCollection('icon');
        }

        return redirect()->route('admin.categories.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم إضافة التصنيف [{$category->name_ar}] بنجاح!" 
                : "Category [{$category->name_en}] created successfully!");
    }

    public function edit(int $id): View
    {
        $dbCat = Category::find($id);

        if ($dbCat) {
            $category = [
                'id' => $dbCat->id,
                'name_en' => $dbCat->name_en,
                'name_ar' => $dbCat->name_ar,
                'slug' => $dbCat->slug,
                'icon' => $dbCat->icon ?? '🧬',
                'description_en' => $dbCat->description_en,
                'description_ar' => $dbCat->description_ar,
                'is_active' => $dbCat->is_active,
                'status' => $dbCat->is_active ? 'active' : 'inactive',
                'sort_order' => $dbCat->sort_order ?? 1,
            ];
        } else {
            $categories = CategoryViewModel::all();
            $category = null;
            foreach ($categories as $c) {
                if ($c['id'] === $id) {
                    $category = $c;
                    break;
                }
>>>>>>> origin/main
            }
        }

        return view('admin.categories.edit', [
<<<<<<< HEAD
            'category' => $category ?? $categories[0],
        ]);
    }
=======
            'category' => $category ?? ($categories[0] ?? []),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'icon' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'banner_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:10240',
            'icon_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name_en']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $category->update($validated);

        if ($request->hasFile('banner_file')) {
            $category->clearMediaCollection('banner');
            $category->addMediaFromRequest('banner_file')->toMediaCollection('banner');
        }

        if ($request->hasFile('icon_file')) {
            $category->clearMediaCollection('icon');
            $category->addMediaFromRequest('icon_file')->toMediaCollection('icon');
        }

        return redirect()->route('admin.categories.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم تحديث بيانات التصنيف [{$category->name_ar}] بنجاح!" 
                : "Category [{$category->name_en}] updated successfully!");
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        $name = app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم نقل التصنيف [{$name}] إلى سلة المحذوفات بنجاح." 
                : "Category [{$name}] moved to trash successfully.");
    }

    public function restore(int $id): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $name = app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en;
        $category->restore();

        return redirect()->route('admin.categories.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم استعادة التصنيف [{$name}] بنجاح!" 
                : "Category [{$name}] restored successfully!");
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $category = Category::withTrashed()->findOrFail($id);
        $name = app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en;

        // Unlink associated products
        \App\Models\Product::where('category_id', $category->id)->update(['category_id' => null]);

        $category->forceDelete();

        return redirect()->route('admin.categories.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم حذف التصنيف [{$name}] نهائياً!" 
                : "Category [{$name}] permanently deleted!");
    }
>>>>>>> origin/main
}
