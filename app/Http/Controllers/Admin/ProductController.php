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
use Illuminate\Support\Facades\Storage;
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
                    'show_on_landing' => (bool) ($p->show_on_landing ?? true),
                    'landing_order' => (int) ($p->landing_order ?? 0),
                    'landing_badge_en' => $p->landing_badge_en ?? '',
                    'landing_badge_ar' => $p->landing_badge_ar ?? '',
                    'image' => $p->primary_image_url,
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

        $landingSettings = [
            'landing_featured_products_enabled' => (bool) (\App\Models\Setting::get('landing_featured_products_enabled', true)),
            'landing_products_badge_en' => \App\Models\Setting::get('landing_products_badge_en', 'CLINICAL FORMULATIONS'),
            'landing_products_badge_ar' => \App\Models\Setting::get('landing_products_badge_ar', 'التركيبات الطبية السريرية'),
            'landing_products_title_en' => \App\Models\Setting::get('landing_products_title_en', 'Engineered for Systemic Longevity & Vitality'),
            'landing_products_title_ar' => \App\Models\Setting::get('landing_products_title_ar', 'مصممة خصيصاً للصحة الخلوية وطول العمر المديد'),
            'landing_products_subtitle_en' => \App\Models\Setting::get('landing_products_subtitle_en', 'Targeted botanical bio-compounds designed to support cellular repair, cognitive sharpness, and daily metabolic energy.'),
            'landing_products_subtitle_ar' => \App\Models\Setting::get('landing_products_subtitle_ar', 'مركبات نباتية نشطة بيولوجياً تستهدف تحفيز الترميم الخلوي، تعزيز صفاء الذهن، ودعم الطاقة الأيضية اليومية.'),
            'landing_products_limit' => (int) \App\Models\Setting::get('landing_products_limit', 6),
            'landing_products_cta_text_en' => \App\Models\Setting::get('landing_products_cta_text_en', 'VIEW ALL FORMULATIONS'),
            'landing_products_cta_text_ar' => \App\Models\Setting::get('landing_products_cta_text_ar', 'عرض جميع المستحضرات'),
        ];

        $landingProductsCount = Product::where(function ($q) {
            $q->where('status', 'active')->orWhere('is_active', true);
        })->where('show_on_landing', true)->count();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalCount' => $totalDbCount > 0 ? $dbProducts->total() : count($products),
            'trashedCount' => $trashedCount,
            'activeCount' => $activeCount,
            'isTrashed' => $isTrashed,
            'currentStatus' => $status,
            'landingSettings' => $landingSettings,
            'landingProductsCount' => $landingProductsCount,
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
        $data['show_on_landing'] = $request->has('show_on_landing') ? $request->boolean('show_on_landing') : true;
        $data['landing_order'] = (int) $request->input('landing_order', 0);
        $data['landing_badge_en'] = $request->input('landing_badge_en');
        $data['landing_badge_ar'] = $request->input('landing_badge_ar');
        $this->formatScienceInputs($data);
        $fallbackImage = $data['image'] ?? null;
        unset($data['image'], $data['images']);
        $product = Product::create($data);

        // Attach Primary Image (Dual-Engine: Spatie Media Library + Direct Storage Fallback)
        $primaryFile = $request->file('primary_image') ?? ($request->hasFile('image') ? $request->file('image') : null);
        if (is_array($primaryFile)) {
            $primaryFile = reset($primaryFile);
        }

        if ($primaryFile && $primaryFile->isValid()) {
            $savedUrl = null;
            // 1. Try Spatie Media Library
            try {
                $media = $product->addMedia($primaryFile)->toMediaCollection('primary_image');
                $savedUrl = $media->getUrl();
            } catch (\Throwable $e) {
                \Log::warning("Spatie addMedia primary_image failed on store, falling back to direct upload: " . $e->getMessage());
            }

            // 2. Direct upload fallback if Spatie failed or produced empty URL
            if (empty($savedUrl)) {
                $ext = $primaryFile->getClientOriginalExtension() ?: 'webp';
                $filename = 'product_' . $product->id . '_' . time() . '_' . Str::random(6) . '.' . $ext;
                $destDir = public_path('uploads/products');
                if (!file_exists($destDir)) {
                    @mkdir($destDir, 0755, true);
                }
                $primaryFile->move($destDir, $filename);
                $savedUrl = '/uploads/products/' . $filename;
            }

            $product->image = Product::normalizeUrl($savedUrl);
            $product->save();
        } elseif (!empty($fallbackImage) && is_string($fallbackImage)) {
            $product->image = Product::normalizeUrl($fallbackImage);
            $product->save();
        }

        // Attach Gallery Images (Dual-Engine: Spatie + Direct Storage Fallback)
        if ($request->hasFile('gallery')) {
            $galleryUrls = [];
            $galleryFiles = $request->file('gallery');
            if (!is_array($galleryFiles)) {
                $galleryFiles = [$galleryFiles];
            }
            foreach ($galleryFiles as $file) {
                if ($file && $file->isValid()) {
                    $savedUrl = null;
                    try {
                        $media = $product->addMedia($file)->toMediaCollection('gallery');
                        $savedUrl = $media->getUrl();
                    } catch (\Throwable $e) {
                        \Log::warning("Spatie addMedia gallery failed on store: " . $e->getMessage());
                    }

                    if (empty($savedUrl)) {
                        $ext = $file->getClientOriginalExtension() ?: 'webp';
                        $filename = 'gallery_' . $product->id . '_' . time() . '_' . Str::random(8) . '.' . $ext;
                        $destDir = public_path('uploads/products');
                        if (!file_exists($destDir)) {
                            @mkdir($destDir, 0755, true);
                        }
                        $file->move($destDir, $filename);
                        $savedUrl = '/uploads/products/' . $filename;
                    }

                    $galleryUrls[] = Product::normalizeUrl($savedUrl);
                }
            }
            if (!empty($galleryUrls)) {
                $product->images = array_values(array_unique($galleryUrls));
                $product->save();
            }
        }

        if ($request->hasFile('documents')) {
            $docFiles = is_array($request->file('documents')) ? $request->file('documents') : [$request->file('documents')];
            foreach ($docFiles as $file) {
                if ($file && $file->isValid()) {
                    $product->addMedia($file)->toMediaCollection('documents');
                }
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
            'location_name_en' => 'Warehouse POS',
            'location_name_ar' => 'مستودع المبيعات المباشرة',
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
                'image' => $dbProduct->primary_image_url,
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
        $dbProduct = Product::with(['category', 'inventoryItems'])->find($id);

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
                'image' => $dbProduct->primary_image_url,
                'images' => $dbProduct->gallery_urls,
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
                'show_on_landing' => (bool) ($dbProduct->show_on_landing ?? true),
                'landing_order' => (int) ($dbProduct->landing_order ?? 0),
                'landing_badge_en' => $dbProduct->landing_badge_en ?? '',
                'landing_badge_ar' => $dbProduct->landing_badge_ar ?? '',
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

        $onlineStock = (int) ($dbProduct->stock_online ?? ($product['stock_online'] ?? 0));
        $offlineStock = (int) ($dbProduct->stock_offline ?? ($product['stock_offline'] ?? 0));
        $centralStock = (int) ($dbProduct?->inventoryItems?->where('location_id', 'central_wh')->sum('current_stock') ?? 0);
        $totalStock = $onlineStock + $offlineStock + $centralStock;

        $inventoryBreakdown = [
            'online' => $onlineStock,
            'offline' => $offlineStock,
            'central' => $centralStock,
            'total' => $totalStock,
            'valuation' => $totalStock * (float) ($product['price'] ?? 68.00),
        ];

        $locations = \App\Models\Location::where('is_active', true)->get();
        if ($locations->isEmpty()) {
            $locations = collect([
                (object) ['id' => 'online', 'name_en' => 'Online Fulfillment Hub', 'name_ar' => 'مستودع المتجر الإلكتروني', 'code' => 'LOC-ONL'],
                (object) ['id' => 'offline', 'name_en' => 'POS Sales Warehouse', 'name_ar' => 'مستودع المبيعات المباشرة', 'code' => 'LOC-POS'],
                (object) ['id' => 'central_wh', 'name_en' => 'Central Quarantine Warehouse', 'name_ar' => 'المستودع المركزي الرئيسي', 'code' => 'LOC-CWH'],
            ]);
        }

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'taxInfo' => $taxInfo,
            'inventoryBreakdown' => $inventoryBreakdown,
            'locations' => $locations,
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Generate Slug
        |--------------------------------------------------------------------------
        */
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_en']);
        }

        /*
        |--------------------------------------------------------------------------
        | Boolean Fields
        |--------------------------------------------------------------------------
        */
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_new'] = $request->boolean('is_new');
        $data['enable_backorders'] = $request->boolean('enable_backorders');
        $data['show_on_landing'] = $request->boolean('show_on_landing');
        $data['landing_order'] = (int) $request->input('landing_order', 0);
        $data['landing_badge_en'] = $request->input('landing_badge_en');
        $data['landing_badge_ar'] = $request->input('landing_badge_ar');

        /*
        |--------------------------------------------------------------------------
        | Format Science Inputs
        |--------------------------------------------------------------------------
        */
        $this->formatScienceInputs($data);
        $fallbackImage = $data['image'] ?? null;
        unset($data['image'], $data['images'], $data['stock_online'], $data['stock_offline']);

        /*
        |--------------------------------------------------------------------------
        | Update Product Data
        |--------------------------------------------------------------------------
        */
        $product->update($data);

        /*
        |--------------------------------------------------------------------------
        | Primary Image (Dual-Engine: Spatie + Direct Storage Fallback)
        |--------------------------------------------------------------------------
        */
        $primaryFile = $request->file('primary_image') ?? ($request->hasFile('image') ? $request->file('image') : null);
        if (is_array($primaryFile)) {
            $primaryFile = reset($primaryFile);
        }

        if ($primaryFile && $primaryFile->isValid()) {
            $savedUrl = null;
            // 1. Try Spatie
            try {
                if (method_exists($product, 'clearMediaCollection')) {
                    $product->clearMediaCollection('primary_image');
                }
                $media = $product->addMedia($primaryFile)->toMediaCollection('primary_image');
                $savedUrl = $media->getUrl();
            } catch (\Throwable $e) {
                \Log::warning("Spatie addMedia primary_image failed on update, falling back to direct upload: " . $e->getMessage());
            }

            // 2. Direct upload fallback
            if (empty($savedUrl)) {
                $ext = $primaryFile->getClientOriginalExtension() ?: 'webp';
                $filename = 'product_' . $product->id . '_' . time() . '_' . Str::random(6) . '.' . $ext;
                $destDir = public_path('uploads/products');
                if (!file_exists($destDir)) {
                    @mkdir($destDir, 0755, true);
                }
                $primaryFile->move($destDir, $filename);
                $savedUrl = '/uploads/products/' . $filename;
            }

            $product->image = Product::normalizeUrl($savedUrl);
            $product->save();
        } elseif (!empty($fallbackImage) && is_string($fallbackImage) && !$product->hasMedia('primary_image')) {
            $product->image = Product::normalizeUrl($fallbackImage);
            $product->save();
        }

        /*
        |--------------------------------------------------------------------------
        | Gallery Images (Dual-Engine: Spatie + Direct Storage Fallback + Removal)
        |--------------------------------------------------------------------------
        */
        $existingGallery = is_array($product->images) ? $product->images : [];

        // Handle removals if requested
        if ($request->has('removed_gallery_images') && is_array($request->input('removed_gallery_images'))) {
            $toRemove = array_map(fn($u) => Product::normalizeUrl($u), $request->input('removed_gallery_images'));
            $existingGallery = array_values(array_filter($existingGallery, function($url) use ($toRemove) {
                return !in_array(Product::normalizeUrl($url), $toRemove);
            }));

            // Also remove from Spatie Media collection if found
            try {
                if (method_exists($product, 'getMedia')) {
                    foreach ($product->getMedia('gallery') as $m) {
                        if (in_array(Product::normalizeUrl($m->getUrl()), $toRemove)) {
                            $m->delete();
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning("Gallery media delete error: " . $e->getMessage());
            }
        }

        if ($request->hasFile('gallery')) {
            $gallery = $request->file('gallery');
            if (!is_array($gallery)) {
                $gallery = [$gallery];
            }

            foreach ($gallery as $index => $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                $savedUrl = null;
                // 1. Try Spatie
                try {
                    $media = $product->addMedia($file)->toMediaCollection('gallery');
                    $savedUrl = $media->getUrl();
                } catch (\Throwable $e) {
                    \Log::warning("Spatie addMedia gallery failed on update: " . $e->getMessage());
                }

                // 2. Direct upload fallback
                if (empty($savedUrl)) {
                    $ext = $file->getClientOriginalExtension() ?: 'webp';
                    $filename = 'gallery_' . $product->id . '_' . time() . '_' . Str::random(8) . '.' . $ext;
                    $destDir = public_path('uploads/products');
                    if (!file_exists($destDir)) {
                        @mkdir($destDir, 0755, true);
                    }
                    $file->move($destDir, $filename);
                    $savedUrl = '/uploads/products/' . $filename;
                }

                $existingGallery[] = Product::normalizeUrl($savedUrl);
            }
        }

        $product->images = array_values(array_unique(array_filter($existingGallery)));
        $product->save();

        /*
        |--------------------------------------------------------------------------
        | Documents
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('documents')) {
            $documents = is_array($request->file('documents'))
                ? $request->file('documents')
                : [$request->file('documents')];

            foreach ($documents as $file) {
                if ($file && $file->isValid()) {
                    $product->addMedia($file)->toMediaCollection('documents');
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sync Inventory Threshold
        |--------------------------------------------------------------------------
        */
        InventoryItem::where('product_id', $product->id)
            ->update([
                'low_stock_threshold' => $product->low_stock_threshold,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                app()->getLocale() === 'ar'
                ? "تم تحديث بيانات التركيبة [{$product->name_ar}] بنجاح!"
                : "Bioceutical formulation [{$product->name_en}] updated successfully!"
            );
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
                $data['benefits_en'] = array_values(array_filter(array_map('trim', $lines), fn($line) => $line !== ''));
            } elseif (is_array($data['benefits_en'])) {
                $data['benefits_en'] = array_values(array_filter($data['benefits_en']));
            }
        }

        if (isset($data['benefits_ar'])) {
            if (is_string($data['benefits_ar'])) {
                $lines = preg_split('/\r\n|\r|\n/', trim($data['benefits_ar']));
                $data['benefits_ar'] = array_values(array_filter(array_map('trim', $lines), fn($line) => $line !== ''));
            } elseif (is_array($data['benefits_ar'])) {
                $data['benefits_ar'] = array_values(array_filter($data['benefits_ar']));
            }
        }

        if (isset($data['ingredients']) && is_array($data['ingredients'])) {
            $data['ingredients'] = array_values(array_filter($data['ingredients'], function ($row) {
                if (!is_array($row)) {
                    return false;
                }

                return !empty($row['name_en']) || !empty($row['name_ar']) || !empty($row['dose']);
            }));
        }
    }

    /**
     * Update global landing page products showcase section settings.
     */
    public function updateLandingSectionSettings(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'landing_featured_products_enabled' => ['nullable'],
            'landing_products_badge_en' => ['nullable', 'string', 'max:150'],
            'landing_products_badge_ar' => ['nullable', 'string', 'max:150'],
            'landing_products_title_en' => ['nullable', 'string', 'max:255'],
            'landing_products_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_products_subtitle_en' => ['nullable', 'string', 'max:500'],
            'landing_products_subtitle_ar' => ['nullable', 'string', 'max:500'],
            'landing_products_limit' => ['nullable', 'integer', 'min:0'],
            'landing_products_cta_text_en' => ['nullable', 'string', 'max:100'],
            'landing_products_cta_text_ar' => ['nullable', 'string', 'max:100'],
        ]);

        $enabled = $request->boolean('landing_featured_products_enabled');
        \App\Models\Setting::set('landing_featured_products_enabled', $enabled, 'landing', 'boolean');

        if ($request->has('landing_products_badge_en')) {
            \App\Models\Setting::set('landing_products_badge_en', $request->input('landing_products_badge_en', ''), 'landing');
        }
        if ($request->has('landing_products_badge_ar')) {
            \App\Models\Setting::set('landing_products_badge_ar', $request->input('landing_products_badge_ar', ''), 'landing');
        }
        if ($request->has('landing_products_title_en')) {
            \App\Models\Setting::set('landing_products_title_en', $request->input('landing_products_title_en', ''), 'landing');
        }
        if ($request->has('landing_products_title_ar')) {
            \App\Models\Setting::set('landing_products_title_ar', $request->input('landing_products_title_ar', ''), 'landing');
        }
        if ($request->has('landing_products_subtitle_en')) {
            \App\Models\Setting::set('landing_products_subtitle_en', $request->input('landing_products_subtitle_en', ''), 'landing');
        }
        if ($request->has('landing_products_subtitle_ar')) {
            \App\Models\Setting::set('landing_products_subtitle_ar', $request->input('landing_products_subtitle_ar', ''), 'landing');
        }
        if ($request->has('landing_products_limit')) {
            \App\Models\Setting::set('landing_products_limit', (int) $request->input('landing_products_limit', 6), 'landing', 'integer');
        }
        if ($request->has('landing_products_cta_text_en')) {
            \App\Models\Setting::set('landing_products_cta_text_en', $request->input('landing_products_cta_text_en', ''), 'landing');
        }
        if ($request->has('landing_products_cta_text_ar')) {
            \App\Models\Setting::set('landing_products_cta_text_ar', $request->input('landing_products_cta_text_ar', ''), 'landing');
        }

        \Illuminate\Support\Facades\Cache::flush();
        \App\Models\Setting::flushRuntimeCache();

        $message = app()->getLocale() === 'ar'
            ? 'تم حفظ وتحديث إعدادات قسم المنتجات بالصفحة الرئيسية بنجاح!'
            : 'Landing page products section configuration updated successfully!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'enabled' => $enabled,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Quick AJAX toggle for displaying/hiding a specific product on the landing page.
     */
    public function toggleLandingVisibility(Request $request, int $id): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->show_on_landing = !$product->show_on_landing;
        $product->save();

        \Illuminate\Support\Facades\Cache::flush();
        \App\Models\Setting::flushRuntimeCache();

        $prodName = app()->getLocale() === 'ar' && !empty($product->name_ar) ? $product->name_ar : $product->name_en;
        $message = $product->show_on_landing
            ? (app()->getLocale() === 'ar' ? "تم إظهار المستحضر [{$prodName}] في قسم الصفحة الرئيسية بنجاح!" : "Formulation [{$prodName}] is now visible in the landing page products section!")
            : (app()->getLocale() === 'ar' ? "تم إخفاء المستحضر [{$prodName}] من قسم الصفحة الرئيسية!" : "Formulation [{$prodName}] is now hidden from the landing page products section!");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'show_on_landing' => (bool) $product->show_on_landing,
                'product_id' => $product->id,
                'product_name' => $prodName,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
