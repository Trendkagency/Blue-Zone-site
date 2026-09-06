<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\TaxService;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $query = Product::with('category')->latest();

        $status = $request->query('status');
        $isTrashed = $status === 'trashed';

        if ($isTrashed) {
            $query->onlyTrashed();
        } else {
            if ($status && in_array($status, ['active', 'inactive', 'out_of_stock', 'low_stock'])) {
                if ($status === 'out_of_stock') {
                    $query->whereRaw('(stock_online + stock_offline) <= 0');
                } elseif ($status === 'low_stock') {
                    $query->whereRaw('(stock_online + stock_offline) > 0 AND (stock_online + stock_offline) <= low_stock_threshold');
                } else {
                    $query->where('status', $status);
                }
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $trashedCount = Product::onlyTrashed()->count();
        $activeCount = Product::count();
        $dbProducts = $query->paginate(15)->withQueryString();

        $totalDbCount = Product::withTrashed()->count();

        if ($totalDbCount > 0) {
            $products = $dbProducts->map(function ($p) {
                return [
                    'id' => $p->id,
                    'slug' => $p->slug,
                    'name_en' => $p->name_en,
                    'name_ar' => $p->name_ar,
                    'sku' => $p->sku,
                    'barcode' => $p->barcode ?? '6281100' . rand(1000, 9999),
                    'category_en' => $p->category?->name_en ?? 'Cellular Longevity',
                    'category_name_en' => $p->category?->name_en ?? 'Cellular Longevity',
                    'category_id' => $p->category_id,
                    'price' => (float) $p->price,
                    'sale_price' => $p->sale_price ? (float) $p->sale_price : null,
                    'cost_price' => (float) $p->cost_price,
                    'stock_online' => $p->stock_online,
                    'stock_offline' => $p->stock_offline,
                    'low_stock_threshold' => $p->low_stock_threshold ?? 10,
                    'status' => $p->status ?? 'active',
                    'image' => $p->image ?? 'assets/products/blue-mind.jpg',
                    'deleted_at' => $p->deleted_at,
                ];
            })->toArray();
            $currentPage = $dbProducts->currentPage();
            $totalPages = $dbProducts->lastPage();
        } else {
            $products = ProductViewModel::all();
            $currentPage = 1;
            $totalPages = 1;
        }

        $dbCategories = Category::all();
        $categories = $dbCategories->isNotEmpty() ? $dbCategories->toArray() : CategoryViewModel::all();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
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
        $dbCategories = Category::all();
        $categories = $dbCategories->isNotEmpty() ? $dbCategories->toArray() : CategoryViewModel::all();

        // Default dynamic tax calculation for a starting price
        $taxInfo = TaxService::breakdownPrice(68.00, 22.00);

        return view('admin.products.create', [
            'categories' => $categories,
            'taxInfo' => $taxInfo,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_en']);
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_new'] = $request->boolean('is_new');
        $data['enable_backorders'] = $request->boolean('enable_backorders');
        $this->formatScienceInputs($data);
        $product = Product::create($data);

        // Attach Spatie Media if uploaded
        if ($request->hasFile('primary_image')) {
            $product->addMediaFromRequest('primary_image')->toMediaCollection('primary_image');
            $product->image = $product->getFirstMediaUrl('primary_image');
            $product->save();
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $product->addMedia($file)->toMediaCollection('gallery');
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $product->addMedia($file)->toMediaCollection('documents');
            }
        }

        // Provision inventory records for online and offline hubs
        InventoryItem::create([
            'product_id' => $product->id,
            'location_id' => 'online',
            'location_name_en' => 'Online Store Buffer',
            'location_name_ar' => 'مستودع المتجر الإلكتروني',
            'current_stock' => $product->stock_online,
            'available_stock' => $product->stock_online,
            'reserved_stock' => 0,
            'low_stock_threshold' => $product->low_stock_threshold,
            'unit_cost' => $product->cost_price,
            'retail_price' => $product->price,
        ]);

        InventoryItem::create([
            'product_id' => $product->id,
            'location_id' => 'offline',
            'location_name_en' => 'Flagship Boutique POS',
            'location_name_ar' => 'معرض البوتيك المباشر',
            'current_stock' => $product->stock_offline,
            'available_stock' => $product->stock_offline,
            'reserved_stock' => 0,
            'low_stock_threshold' => $product->low_stock_threshold,
            'unit_cost' => $product->cost_price,
            'retail_price' => $product->price,
        ]);

        // Record initial inventory movement
        InventoryMovement::create([
            'product_id' => $product->id,
            'product_name_en' => $product->name_en,
            'product_name_ar' => $product->name_ar,
            'sku' => $product->sku,
            'movement_type' => 'Stock In',
            'from_location' => 'Supplier Lab',
            'to_location' => 'Central Hub',
            'quantity' => $product->stock_online + $product->stock_offline,
            'previous_qty' => 0,
            'new_qty' => $product->stock_online + $product->stock_offline,
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
            'user' => auth()->user()?->name ?? 'Clinical Admin',
            'note' => 'Initial formulation ingestion and catalog activation',
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم تسجيل وإدراج التركيبة الحيوية [{$product->name_ar}] في الكتالوج بنجاح!" 
                : "Bioceutical formulation [{$product->name_en}] successfully registered in catalog!");
    }

    public function show(int $id): View
    {
        $dbProduct = Product::with(['category', 'inventoryItems', 'inventoryMovements'])->find($id);

        if ($dbProduct) {
            $product = [
                'id' => $dbProduct->id,
                'slug' => $dbProduct->slug,
                'name_en' => $dbProduct->name_en,
                'name_ar' => $dbProduct->name_ar,
                'sku' => $dbProduct->sku,
                'barcode' => $dbProduct->barcode ?? '6281100' . rand(1000, 9999),
                'category_en' => $dbProduct->category?->name_en ?? 'Cellular Longevity',
                'category_name_en' => $dbProduct->category?->name_en ?? 'Cellular Longevity',
                'category_id' => $dbProduct->category_id,
                'price' => (float) $dbProduct->price,
                'sale_price' => $dbProduct->sale_price ? (float) $dbProduct->sale_price : null,
                'cost_price' => (float) $dbProduct->cost_price,
                'stock_online' => $dbProduct->stock_online,
                'stock_offline' => $dbProduct->stock_offline,
                'low_stock_threshold' => $dbProduct->low_stock_threshold ?? 10,
                'status' => $dbProduct->status ?? 'active',
                'image' => $dbProduct->image ?? 'assets/products/blue-mind.jpg',
                'short_description_en' => $dbProduct->short_description_en,
                'description_en' => $dbProduct->description_en,
            ];
        } else {
            $product = ProductViewModel::find($id);
        }

        if (!$product) {
            abort(404);
        }

        return view('admin.products.show', [
            'product' => $product,
        ]);
    }

    public function edit(int $id): View
    {
        $dbProduct = Product::find($id);

        if ($dbProduct) {
            $product = [
                'id' => $dbProduct->id,
                'slug' => $dbProduct->slug,
                'name_en' => $dbProduct->name_en,
                'name_ar' => $dbProduct->name_ar,
                'tagline_en' => $dbProduct->tagline_en,
                'tagline_ar' => $dbProduct->tagline_ar,
                'sku' => $dbProduct->sku,
                'barcode' => $dbProduct->barcode ?? '6281100' . rand(1000, 9999),
                'category_en' => $dbProduct->category?->name_en ?? 'Cellular Longevity',
                'category_id' => $dbProduct->category_id,
                'subcategory_en' => $dbProduct->subcategory_en,
                'subcategory_ar' => $dbProduct->subcategory_ar,
                'brand' => $dbProduct->brand ?? 'Blue Zone Bioceuticals',
                'target_gender' => $dbProduct->target_gender ?? 'Unisex',
                'age_group' => $dbProduct->age_group ?? '18+',
                'product_size' => $dbProduct->product_size ?? '60 Vegetable Capsules',
                'price' => (float) $dbProduct->price,
                'sale_price' => $dbProduct->sale_price ? (float) $dbProduct->sale_price : null,
                'cost_price' => (float) $dbProduct->cost_price,
                'stock_online' => $dbProduct->stock_online,
                'stock_offline' => $dbProduct->stock_offline,
                'low_stock_threshold' => $dbProduct->low_stock_threshold ?? 10,
                'status' => $dbProduct->status ?? 'active',
                'image' => $dbProduct->image ?? 'assets/products/blue-mind.jpg',
                'short_description_en' => $dbProduct->short_description_en,
                'short_description_ar' => $dbProduct->short_description_ar,
                'description_en' => $dbProduct->description_en,
                'description_ar' => $dbProduct->description_ar,
                'usage_en' => $dbProduct->usage_en,
                'usage_ar' => $dbProduct->usage_ar,
                'clinical_mechanism' => $dbProduct->clinical_mechanism,
                'formula_details' => $dbProduct->formula_details,
                'science_en' => $dbProduct->science_en,
                'science_ar' => $dbProduct->science_ar,
                'benefits_en' => is_array($dbProduct->benefits_en) ? implode("\n", $dbProduct->benefits_en) : ($dbProduct->benefits_en ?? ''),
                'benefits_ar' => is_array($dbProduct->benefits_ar) ? implode("\n", $dbProduct->benefits_ar) : ($dbProduct->benefits_ar ?? ''),
                'ingredients' => is_array($dbProduct->ingredients) ? $dbProduct->ingredients : [],
                'contraindications' => $dbProduct->contraindications,
                'warnings' => $dbProduct->warnings,
                'is_featured' => (bool) $dbProduct->is_featured,
                'is_best_seller' => (bool) $dbProduct->is_best_seller,
                'is_new' => (bool) $dbProduct->is_new,
                'enable_backorders' => (bool) $dbProduct->enable_backorders,
            ];
        } else {
            $product = ProductViewModel::find($id);
            if ($product) {
                $product['science_en'] = $product['science_en'] ?? ($product['description_en'] ?? '');
                $product['science_ar'] = $product['science_ar'] ?? ($product['description_ar'] ?? '');
                $product['benefits_en'] = isset($product['benefits_en']) && is_array($product['benefits_en']) ? implode("\n", $product['benefits_en']) : ($product['benefits_en'] ?? '');
                $product['benefits_ar'] = isset($product['benefits_ar']) && is_array($product['benefits_ar']) ? implode("\n", $product['benefits_ar']) : ($product['benefits_ar'] ?? '');
                $product['ingredients'] = $product['ingredients'] ?? [];
                $product['clinical_mechanism'] = $product['clinical_mechanism'] ?? ($product['professional_info']['clinical_mechanism'] ?? '');
                $product['formula_details'] = $product['formula_details'] ?? ($product['professional_info']['formula_details'] ?? '');
                $product['contraindications'] = $product['contraindications'] ?? ($product['professional_info']['contraindications'] ?? '');
                $product['warnings'] = $product['warnings'] ?? ($product['professional_info']['warnings'] ?? '');
            }
        }

        if (!$product) {
            abort(404);
        }

        $dbCategories = Category::all();
        $categories = $dbCategories->isNotEmpty() ? $dbCategories->toArray() : CategoryViewModel::all();

        $taxInfo = TaxService::breakdownPrice(
            (float) ($product['price'] ?? 68.00),
            (float) ($product['cost_price'] ?? 22.00),
            isset($product['sale_price']) && $product['sale_price'] ? (float) $product['sale_price'] : null
        );

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'taxInfo' => $taxInfo,
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_en']);
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_new'] = $request->boolean('is_new');
        $data['enable_backorders'] = $request->boolean('enable_backorders');
        $this->formatScienceInputs($data);

        $product->update($data);

        // Attach Spatie Media if uploaded
        if ($request->hasFile('primary_image')) {
            $product->clearMediaCollection('primary_image');
            $product->addMediaFromRequest('primary_image')->toMediaCollection('primary_image');
            $product->image = $product->getFirstMediaUrl('primary_image');
            $product->save();
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $product->addMedia($file)->toMediaCollection('gallery');
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $product->addMedia($file)->toMediaCollection('documents');
            }
        }

        // Sync inventory threshold adjustments
        InventoryItem::where('product_id', $product->id)->update([
            'low_stock_threshold' => $product->low_stock_threshold,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم تحديث بيانات التركيبة [{$product->name_ar}] بنجاح!" 
                : "Bioceutical formulation [{$product->name_en}] updated successfully!");
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم نقل التركيبة [{$name}] إلى سلة المحذوفات بنجاح." 
                : "Formulation [{$name}] moved to trash successfully.");
    }

    public function restore(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $name = $product->name;
        $product->restore();

        return redirect()->route('admin.products.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم استعادة التركيبة [{$name}] وإعادتها للكتالوج بنجاح!" 
                : "Formulation [{$name}] restored to active catalog successfully!");
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $product = Product::withTrashed()->findOrFail($id);
        $name = $product->name;

        // Clean up linked inventory items
        InventoryItem::where('product_id', $product->id)->delete();
        InventoryMovement::where('product_id', $product->id)->delete();

        $product->forceDelete();

        return redirect()->route('admin.products.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم الحذف النهائي للتركيبة [{$name}] وجميع سجلاتها المرتبطة نهائياً!" 
                : "Formulation [{$name}] and all associated records permanently erased!");
    }

    /**
     * Parse and sanitize Our Science & Clinical fields before saving.
     */
    protected function formatScienceInputs(array &$data): void
    {
        if (isset($data['benefits_en'])) {
            if (is_string($data['benefits_en'])) {
                $lines = preg_split('/\r\n|\r|\n/', trim($data['benefits_en']));
                $data['benefits_en'] = array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
            } elseif (is_array($data['benefits_en'])) {
                $data['benefits_en'] = array_values(array_filter($data['benefits_en']));
            }
        }

        if (isset($data['benefits_ar'])) {
            if (is_string($data['benefits_ar'])) {
                $lines = preg_split('/\r\n|\r|\n/', trim($data['benefits_ar']));
                $data['benefits_ar'] = array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
            } elseif (is_array($data['benefits_ar'])) {
                $data['benefits_ar'] = array_values(array_filter($data['benefits_ar']));
            }
        }

        if (isset($data['ingredients']) && is_array($data['ingredients'])) {
            $data['ingredients'] = array_values(array_filter($data['ingredients'], function ($row) {
                if (! is_array($row)) {
                    return false;
                }

                return ! empty($row['name_en']) || ! empty($row['name_ar']) || ! empty($row['dose']);
            }));
        }
    }
}
