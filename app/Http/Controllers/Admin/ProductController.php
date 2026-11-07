<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Setting;
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
                    $query->where('stock_online', '<=', 0);
                } elseif ($status === 'low_stock') {
                    $query->where('stock_online', '>', 0)->whereColumn('stock_online', '<=', 'low_stock_threshold');
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

        $detailsSettings = $this->getGlobalDetailsSettings();
        $buttonSettings = $this->getGlobalButtonSettings();

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
            'detailsSettings' => $detailsSettings,
            'buttonSettings' => $buttonSettings,
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
            'detailsSettings' => $this->getGlobalDetailsSettings(),
            'allProducts' => Product::select('id', 'name_en', 'name_ar', 'price', 'sale_price', 'image', 'sku')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $sourceName = !empty($data['name_en']) ? $data['name_en'] : ($data['name_ar'] ?? 'product');
            $data['slug'] = Product::generateUniqueSlug($sourceName);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_new'] = $request->boolean('is_new');
        $data['enable_backorders'] = $request->boolean('enable_backorders');
        $data['show_on_landing'] = $request->has('show_on_landing') ? $request->boolean('show_on_landing') : true;
        $data['landing_order'] = (int) $request->input('landing_order', 0);
        $data['landing_badge_en'] = $request->input('landing_badge_en');
        $data['landing_badge_ar'] = $request->input('landing_badge_ar');
        $data['shelf_life_months'] = (int) ($data['shelf_life_months'] ?? 24);
        $this->formatScienceInputs($data);
        $this->formatSectionSettings($data, $request);
        $fallbackImage = $data['image'] ?? null;
        unset(
            $data['image'], 
            $data['images'], 
            $data['initial_batch_number'], 
            $data['manufacture_date'], 
            $data['expiry_date'], 
            $data['batch_status']
        );
        $product = Product::create($data);

        // Attach Primary Image (Dual-Engine: Spatie Media Library + Direct Storage Fallback)
        $primaryFile = $request->file('primary_image') ?? ($request->hasFile('image') ? $request->file('image') : null);
        if (is_array($primaryFile)) {
            $primaryFile = reset($primaryFile);
        }

        if ($primaryFile && $primaryFile->isValid()) {
            $ext = $primaryFile->getClientOriginalExtension() ?: 'webp';
            $filename = 'product_' . $product->id . '_' . time() . '_' . Str::random(6) . '.' . $ext;
            $destDir = public_path('storage/products/hero');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $primaryFile->move($destDir, $filename);
            $product->image = Product::normalizeUrl('/storage/products/hero/' . $filename);
            $product->save();
        } elseif (!empty($fallbackImage) && is_string($fallbackImage)) {
            $product->image = Product::normalizeUrl($fallbackImage);
            $product->save();
        }

        // Attach Gallery Images
        if ($request->hasFile('gallery')) {
            $galleryUrls = [];
            $galleryFiles = (array) $request->file('gallery');
            $destDir = public_path('storage/products/gallery');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            foreach ($galleryFiles as $file) {
                if ($file && $file->isValid()) {
                    $ext = $file->getClientOriginalExtension() ?: 'webp';
                    $filename = 'gallery_' . $product->id . '_' . time() . '_' . Str::random(8) . '.' . $ext;
                    $file->move($destDir, $filename);
                    $galleryUrls[] = Product::normalizeUrl('/storage/products/gallery/' . $filename);
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
            'location_id' => 'central_wh',
            'location_name_en' => 'Central Depot Buffer',
            'location_name_ar' => 'المستودع المركزي الاحتياطي',
            'current_stock' => 0,
            'available_stock' => 0,
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
            'to_location' => 'Online Hub',
            'quantity' => $product->stock_online,
            'previous_qty' => 0,
            'new_qty' => $product->stock_online,
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
            'user' => auth()->user()?->name ?? 'Clinical Admin',
            'note' => 'Initial formulation ingestion and catalog activation',
        ]);

        // Provision initial cGMP Product Batch for FIFO / FEFO tracking
        $initialBatchNumber = trim((string) $request->input('initial_batch_number', ''));
        if ((int) $product->stock_online > 0 || !empty($initialBatchNumber)) {
            $batchStatus = $request->input('batch_status', 'approved');
            $manufactureDate = $request->input('manufacture_date') ?: now()->toDateString();
            $expiryDate = $request->input('expiry_date') ?: $product->calculateDefaultExpiryDate($manufactureDate);
            $batchNumber = !empty($initialBatchNumber) ? $initialBatchNumber : ProductBatch::generateBatchNumber();
            $qty = (int) $product->stock_online;

            ProductBatch::create([
                'product_id' => $product->id,
                'batch_number' => $batchNumber,
                'location_id' => 'online',
                'initial_qty' => $qty,
                'current_qty' => $qty,
                'available_qty' => $batchStatus === 'approved' ? $qty : 0,
                'reserved_qty' => 0,
                'quarantine_qty' => $batchStatus === 'quarantine' ? $qty : 0,
                'unit_cost' => (float) ($product->cost_price ?? 0),
                'manufacture_date' => $manufactureDate,
                'expiry_date' => $expiryDate,
                'status' => $batchStatus,
            ]);
        }

            return redirect()->route('admin.products.index')
                ->with('success', app()->getLocale() === 'ar'
                    ? "تم تسجيل وإدراج التركيبة الحيوية [{$product->name_ar}] في الكتالوج بنجاح!"
                    : "Bioceutical formulation [{$product->name_en}] successfully registered in catalog!");
        } catch (\Throwable $e) {
            \Log::error("Product store failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', $this->formatUserFriendlyErrorMessage($e));
        }
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
                'shelf_life_months' => (int) ($dbProduct->shelf_life_months ?? 24),
                'cost_calculation_mode' => $dbProduct->cost_calculation_mode ?? 'manual_locked',
                'packaging_overhead_cost' => (float) ($dbProduct->packaging_overhead_cost ?? 0.0),
                'labor_overhead_cost' => (float) ($dbProduct->labor_overhead_cost ?? 0.0),
                'bom_material_cost' => (float) $dbProduct->calculateBomMaterialCost(),
                'bom_total_cost' => (float) $dbProduct->calculateTotalBomCost(),
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
        $centralStock = (int) ($dbProduct?->inventoryItems?->where('location_id', 'central_wh')->sum('current_stock') ?? 0);
        $totalStock = $onlineStock + $centralStock;

        $inventoryBreakdown = [
            'online' => $onlineStock,
            'central' => $centralStock,
            'total' => $totalStock,
            'valuation' => $totalStock * (float) ($product['price'] ?? 68.00),
        ];

        $locations = \App\Models\Location::where('is_active', true)->get();
        if ($locations->isEmpty()) {
            $locations = collect([
                (object) ['id' => 'online', 'name_en' => 'Online Fulfillment Hub', 'name_ar' => 'مستودع المتجر الإلكتروني', 'code' => 'LOC-ONL'],
                (object) ['id' => 'central_wh', 'name_en' => 'Central Quarantine Warehouse', 'name_ar' => 'المستودع المركزي الرئيسي', 'code' => 'LOC-CWH'],
            ]);
        }

        return view('admin.products.edit', [
            'product' => $product,
            'productModel' => $dbProduct,
            'recipeItems' => $dbProduct ? $dbProduct->recipeItems()->with('rawMaterial')->get() : collect(),
            'batches' => $dbProduct ? $dbProduct->batches()->orderBy('expiry_date', 'asc')->get() : collect(),
            'categories' => $categories,
            'taxInfo' => $taxInfo,
            'inventoryBreakdown' => $inventoryBreakdown,
            'locations' => $locations,
            'detailsSettings' => $this->getGlobalDetailsSettings(),
            'sectionSettings' => $dbProduct?->section_settings ?? ($product['section_settings'] ?? []),
            'allProducts' => Product::select('id', 'name_en', 'name_ar', 'price', 'sale_price', 'image', 'sku')->where('id', '!=', $id)->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        try {
            $product = Product::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Slug Management (Permanent & Auto-Locked)
        |--------------------------------------------------------------------------
        */
        if (!empty($product->slug)) {
            $data['slug'] = $product->slug;
        } else {
            $sourceName = !empty($data['name_en']) ? $data['name_en'] : ($data['name_ar'] ?? 'product');
            $data['slug'] = Product::generateUniqueSlug($sourceName, $product->id);
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
        $this->formatSectionSettings($data, $request);
        $fallbackImage = $data['image'] ?? null;
        
        $submittedStock = $request->input('stock_online');
        $hasBatches = $product->batches()->count() > 0;

        unset(
            $data['image'], 
            $data['images'], 
            $data['stock_online'],
            $data['initial_batch_number'],
            $data['manufacture_date'],
            $data['expiry_date'],
            $data['batch_status']
        );

        if (!$hasBatches && $submittedStock !== null) {
            $data['stock_online'] = (int) $submittedStock;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Product Data
        |--------------------------------------------------------------------------
        */
        $product->update($data);

        // If configured for dynamic BOM cost rollup, recalculate and sync unit cost
        if (($data['cost_calculation_mode'] ?? $product->cost_calculation_mode) === 'auto_bom') {
            $product->syncCostFromBom();
        }

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
            if (!empty($product->image)) {
                $this->deletePhysicalProductFile($product->image);
            }
            $ext = $primaryFile->getClientOriginalExtension() ?: 'webp';
            $filename = 'product_' . $product->id . '_' . time() . '_' . Str::random(6) . '.' . $ext;
            $destDir = public_path('storage/products/hero');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $primaryFile->move($destDir, $filename);
            $product->image = Product::normalizeUrl('/storage/products/hero/' . $filename);
            $product->save();
        } elseif (!empty($fallbackImage) && is_string($fallbackImage)) {
            $product->image = Product::normalizeUrl($fallbackImage);
            $product->save();
        }

        /*
        |--------------------------------------------------------------------------
        | Gallery Images (Unified Storage & Removal)
        |--------------------------------------------------------------------------
        */
        // Handle removals if requested
        if ($request->has('removed_gallery_images') && is_array($request->input('removed_gallery_images'))) {
            foreach ($request->input('removed_gallery_images') as $toRemove) {
                $this->removeGalleryImageFromProduct($product, $toRemove);
            }
        }

        if ($request->hasFile('gallery')) {
            $existingGallery = is_array($product->images) ? $product->images : [];
            $gallery = (array) $request->file('gallery');
            $destDir = public_path('storage/products/gallery');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }

            foreach ($gallery as $index => $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                $ext = $file->getClientOriginalExtension() ?: 'webp';
                $filename = 'gallery_' . $product->id . '_' . time() . '_' . Str::random(8) . '.' . $ext;
                $file->move($destDir, $filename);
                $existingGallery[] = Product::normalizeUrl('/storage/products/gallery/' . $filename);
            }

            $product->images = array_values(array_unique(array_filter($existingGallery)));
            $product->save();
        }

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
        | Sync Inventory & cGMP Batches
        |--------------------------------------------------------------------------
        */
        InventoryItem::updateOrCreate(
            ['product_id' => $product->id, 'location_id' => 'online'],
            [
                'location_name_en' => 'Online Store Buffer',
                'location_name_ar' => 'مستودع المتجر الإلكتروني',
                'current_stock' => (int) $product->stock_online,
                'available_stock' => (int) $product->stock_online,
                'low_stock_threshold' => (int) $product->low_stock_threshold,
                'unit_cost' => (float) ($product->cost_price ?? 0),
                'retail_price' => (float) ($product->price ?? 0),
            ]
        );

        $initialBatchNumber = trim((string) $request->input('initial_batch_number', ''));
        $batchCount = $product->batches()->count();

        if ($batchCount === 0 && ((int) $product->stock_online > 0 || !empty($initialBatchNumber))) {
            $batchStatus = $request->input('batch_status', 'approved');
            $manufactureDate = $request->input('manufacture_date') ?: now()->toDateString();
            $expiryDate = $request->input('expiry_date') ?: $product->calculateDefaultExpiryDate($manufactureDate);
            $batchNumber = !empty($initialBatchNumber) ? $initialBatchNumber : ProductBatch::generateBatchNumber();
            $qty = (int) $product->stock_online;

            ProductBatch::create([
                'product_id' => $product->id,
                'batch_number' => $batchNumber,
                'location_id' => 'online',
                'initial_qty' => $qty,
                'current_qty' => $qty,
                'available_qty' => $batchStatus === 'approved' ? $qty : 0,
                'reserved_qty' => 0,
                'quarantine_qty' => $batchStatus === 'quarantine' ? $qty : 0,
                'unit_cost' => (float) ($product->cost_price ?? 0),
                'manufacture_date' => $manufactureDate,
                'expiry_date' => $expiryDate,
                'status' => $batchStatus,
            ]);
        } elseif ($batchCount > 0 && ($request->filled('expiry_date') || $request->filled('manufacture_date') || !empty($initialBatchNumber))) {
            $latestBatch = $product->batches()->latest('id')->first();
            if ($latestBatch) {
                if (!empty($initialBatchNumber)) $latestBatch->batch_number = $initialBatchNumber;
                if ($request->filled('manufacture_date')) $latestBatch->manufacture_date = $request->input('manufacture_date');
                if ($request->filled('expiry_date')) $latestBatch->expiry_date = $request->input('expiry_date');
                if ($request->filled('batch_status')) {
                    $latestBatch->status = $request->input('batch_status');
                    if ($latestBatch->status === 'approved') {
                        $latestBatch->available_qty = $latestBatch->current_qty;
                        $latestBatch->quarantine_qty = 0;
                    } elseif ($latestBatch->status === 'quarantine') {
                        $latestBatch->available_qty = 0;
                        $latestBatch->quarantine_qty = $latestBatch->current_qty;
                    }
                }
                $latestBatch->save();
            }
        }

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
        } catch (\Throwable $e) {
            \Log::error("Product update failed for product {$id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', $this->formatUserFriendlyErrorMessage($e));
        }
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
     * Normalize and sanitize per-product section settings (viewport visibility, titles, badges, bespoke content).
     */
    protected function formatSectionSettings(array &$data, Request $request): void
    {
        if ($request->has('section_settings') || isset($data['section_settings'])) {
            $raw = $request->input('section_settings', $data['section_settings'] ?? []);
            $clean = [];
            $allKeys = ['dossier', 'sec1', 'sec2', 'sec3', 'sec4', 'sec5', 'sec6', 'buy_box', 'related'];

            foreach ($allKeys as $key) {
                $sec = is_array($raw) ? ($raw[$key] ?? []) : [];
                $contentScreenMode = $sec['content_screen_mode'] ?? 'unified';
                $clean[$key] = [
                    'enabled_desktop' => isset($sec['enabled_desktop']) && ($sec['enabled_desktop'] === '1' || $sec['enabled_desktop'] === 1 || $sec['enabled_desktop'] === true),
                    'enabled_mobile' => isset($sec['enabled_mobile']) && ($sec['enabled_mobile'] === '1' || $sec['enabled_mobile'] === 1 || $sec['enabled_mobile'] === true),
                    'default_open' => isset($sec['default_open']) && ($sec['default_open'] === '1' || $sec['default_open'] === 1 || $sec['default_open'] === true),
                    'custom_content_enabled' => isset($sec['custom_content_enabled']) && ($sec['custom_content_enabled'] === '1' || $sec['custom_content_enabled'] === 1 || $sec['custom_content_enabled'] === true),
                    'content_screen_mode' => in_array($contentScreenMode, ['unified', 'per_screen']) ? $contentScreenMode : 'unified',
                    'title_en' => trim($sec['title_en'] ?? ''),
                    'title_ar' => trim($sec['title_ar'] ?? ''),
                    'badge_en' => trim($sec['badge_en'] ?? ''),
                    'badge_ar' => trim($sec['badge_ar'] ?? ''),
                    'custom_content_en' => $sec['custom_content_en'] ?? '',
                    'custom_content_ar' => $sec['custom_content_ar'] ?? '',
                    'custom_content_mobile_en' => $sec['custom_content_mobile_en'] ?? '',
                    'custom_content_mobile_ar' => $sec['custom_content_mobile_ar'] ?? '',
                ];

                if ($key === 'related' && isset($sec['bundle'])) {
                    $bRaw = $sec['bundle'];
                    $clean[$key]['bundle'] = [
                        'enabled' => !empty($bRaw['enabled']),
                        'discount_type' => in_array($bRaw['discount_type'] ?? 'percentage', ['percentage', 'fixed']) ? $bRaw['discount_type'] : 'percentage',
                        'discount_value' => (float) ($bRaw['discount_value'] ?? 15.0),
                        'badge_en' => trim($bRaw['badge_en'] ?? 'Optimal Synergistic Protocol'),
                        'badge_ar' => trim($bRaw['badge_ar'] ?? 'البروتوكول التكاملي الموصى به'),
                        'product_ids' => array_values(array_filter(array_map('intval', (array) ($bRaw['product_ids'] ?? [])))),
                    ];
                }
            }

            if (isset($raw['section_order']) && is_array($raw['section_order'])) {
                $clean['section_order'] = array_values(array_intersect($raw['section_order'], $allKeys));
            }

            $data['section_settings'] = $clean;
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

    /**
     * Update product details page sections customization settings.
     */
    public function updateDetailsSectionSettings(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $fields = [
            'pdetail_dossier_badge_en', 'pdetail_dossier_badge_ar',
            'pdetail_dossier_title_en', 'pdetail_dossier_title_ar',
            'pdetail_sec1_badge_en', 'pdetail_sec1_badge_ar',
            'pdetail_sec1_title_en', 'pdetail_sec1_title_ar',
            'pdetail_sec2_badge_en', 'pdetail_sec2_badge_ar',
            'pdetail_sec2_title_en', 'pdetail_sec2_title_ar',
            'pdetail_sec3_badge_en', 'pdetail_sec3_badge_ar',
            'pdetail_sec3_title_en', 'pdetail_sec3_title_ar',
            'pdetail_sec4_badge_en', 'pdetail_sec4_badge_ar',
            'pdetail_sec4_title_en', 'pdetail_sec4_title_ar',
            'pdetail_sec5_badge_en', 'pdetail_sec5_badge_ar',
            'pdetail_sec5_title_en', 'pdetail_sec5_title_ar',
            'pdetail_sec6_badge_en', 'pdetail_sec6_badge_ar',
            'pdetail_sec6_title_en', 'pdetail_sec6_title_ar',
            'pdetail_related_badge_en', 'pdetail_related_badge_ar',
            'pdetail_related_title_en', 'pdetail_related_title_ar',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                \App\Models\Setting::set($field, $request->input($field, ''), 'product_details');
            }
        }

        \Illuminate\Support\Facades\Cache::flush();
        \App\Models\Setting::flushRuntimeCache();

        $message = app()->getLocale() === 'ar'
            ? 'تم حفظ وتحديث إعدادات وعناوين أقسام صفحة تفاصيل المنتج بنجاح!'
            : 'Product details page sections configuration updated successfully!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Update product action buttons customization settings (Add to Cart, Buy Now, Clinical Protocol CTAs).
     */
    public function updateButtonSettings(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $fields = [
            'btn_buy_now_text_en', 'btn_buy_now_text_ar', 'btn_buy_now_icon',
            'btn_buy_now_subtext_en', 'btn_buy_now_subtext_ar',
            'btn_cart_text_en', 'btn_cart_text_ar',
            'btn_quick_add_text_en', 'btn_quick_add_text_ar',
            'btn_view_details_text_en', 'btn_view_details_text_ar',
            'btn_out_of_stock_text_en', 'btn_out_of_stock_text_ar',
            'btn_preorder_text_en', 'btn_preorder_text_ar',
            'btn_bundle_buy_text_en', 'btn_bundle_buy_text_ar',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, trim((string) $request->input($field, '')), 'product_buttons');
            }
        }

        \Illuminate\Support\Facades\Cache::flush();
        Setting::flushRuntimeCache();

        $message = app()->getLocale() === 'ar'
            ? 'تم حفظ وتحديث نصوص وأيقونات أزرار المنتجات بنجاح!'
            : 'Product action buttons and protocol CTAs updated successfully!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get baseline global product button settings from Setting model.
     */
    protected function getGlobalButtonSettings(): array
    {
        return [
            'btn_buy_now_text_en' => Setting::get('btn_buy_now_text_en', 'ADD TO CLINICAL PROTOCOL'),
            'btn_buy_now_text_ar' => Setting::get('btn_buy_now_text_ar', 'إضافة إلى السلة والبروتوكول'),
            'btn_buy_now_icon' => Setting::get('btn_buy_now_icon', 'fa-cart-shopping'),
            'btn_buy_now_subtext_en' => Setting::get('btn_buy_now_subtext_en', 'Clinically Verified Formulation • 100% Purity Guaranteed'),
            'btn_buy_now_subtext_ar' => Setting::get('btn_buy_now_subtext_ar', 'تركيبة سريرية موثقة • نقاء ومطابقة دوائية 100%'),
            'btn_cart_text_en' => Setting::get('btn_cart_text_en', 'ADD TO CART'),
            'btn_cart_text_ar' => Setting::get('btn_cart_text_ar', 'أضف للسلة'),
            'btn_quick_add_text_en' => Setting::get('btn_quick_add_text_en', 'QUICK ADD'),
            'btn_quick_add_text_ar' => Setting::get('btn_quick_add_text_ar', 'إضافة سريعة'),
            'btn_view_details_text_en' => Setting::get('btn_view_details_text_en', 'VIEW DOSSIER'),
            'btn_view_details_text_ar' => Setting::get('btn_view_details_text_ar', 'عرض الملف السريري'),
            'btn_out_of_stock_text_en' => Setting::get('btn_out_of_stock_text_en', 'CURRENTLY OUT OF STOCK'),
            'btn_out_of_stock_text_ar' => Setting::get('btn_out_of_stock_text_ar', 'نفذت الكمية حالياً'),
            'btn_preorder_text_en' => Setting::get('btn_preorder_text_en', 'PRE-ORDER PROTOCOL'),
            'btn_preorder_text_ar' => Setting::get('btn_preorder_text_ar', 'طلب حجز مسبق'),
            'btn_bundle_buy_text_en' => Setting::get('btn_bundle_buy_text_en', 'ADD COMPLETE PROTOCOL TO CART'),
            'btn_bundle_buy_text_ar' => Setting::get('btn_bundle_buy_text_ar', 'إضافة البروتوكول التكاملي بالكامل للسلة'),
        ];
    }

    /**
     * Get baseline global product detail page section settings from Setting model.
     */
    protected function getGlobalDetailsSettings(): array
    {
        return [
            'pdetail_dossier_enabled' => (bool) \App\Models\Setting::get('pdetail_dossier_enabled', true),
            'pdetail_dossier_badge_en' => \App\Models\Setting::get('pdetail_dossier_badge_en', 'CLINICAL DOSSIER & BIO-MOLECULAR MONOGRAPH'),
            'pdetail_dossier_badge_ar' => \App\Models\Setting::get('pdetail_dossier_badge_ar', 'الملف الطبي والموا مواصفات السريرية المعتمدة'),
            'pdetail_dossier_title_en' => \App\Models\Setting::get('pdetail_dossier_title_en', 'Comprehensive Formulation Specifications'),
            'pdetail_dossier_title_ar' => \App\Models\Setting::get('pdetail_dossier_title_ar', 'التفاصيل الدقيقة والخصائص الدوائية للمستحضر'),

            'pdetail_sec1_badge_en' => \App\Models\Setting::get('pdetail_sec1_badge_en', 'Standardized Monograph'),
            'pdetail_sec1_badge_ar' => \App\Models\Setting::get('pdetail_sec1_badge_ar', 'النقاء والجرعات القياسية'),
            'pdetail_sec1_title_en' => \App\Models\Setting::get('pdetail_sec1_title_en', 'Active Bio-Compounds & Potencies'),
            'pdetail_sec1_title_ar' => \App\Models\Setting::get('pdetail_sec1_title_ar', 'المكونات الفعالة والجرعات المعيارية'),

            'pdetail_sec2_badge_en' => \App\Models\Setting::get('pdetail_sec2_badge_en', 'Physiological Biomarkers'),
            'pdetail_sec2_badge_ar' => \App\Models\Setting::get('pdetail_sec2_badge_ar', 'الأثر الفسيولوجي المباشر والمستدام'),
            'pdetail_sec2_title_en' => \App\Models\Setting::get('pdetail_sec2_title_en', 'Clinical Benefits & Physiological Targets'),
            'pdetail_sec2_title_ar' => \App\Models\Setting::get('pdetail_sec2_title_ar', 'الفوائد السريرية والمسارات البيولوجية'),

            'pdetail_sec3_badge_en' => \App\Models\Setting::get('pdetail_sec3_badge_en', 'Synergistic Protocol'),
            'pdetail_sec3_badge_ar' => \App\Models\Setting::get('pdetail_sec3_badge_ar', 'الجرعات والتكامل الحيوي'),
            'pdetail_sec3_title_en' => \App\Models\Setting::get('pdetail_sec3_title_en', 'Clinical Protocol & Usage Guidelines'),
            'pdetail_sec3_title_ar' => \App\Models\Setting::get('pdetail_sec3_title_ar', 'بروتوكول الاستخدام والتوجيهات السريرية'),

            'pdetail_sec4_badge_en' => \App\Models\Setting::get('pdetail_sec4_badge_en', 'Molecular Pharmacokinetics'),
            'pdetail_sec4_badge_ar' => \App\Models\Setting::get('pdetail_sec4_badge_ar', 'الأبحاث الصيدلانية وطول العمر'),
            'pdetail_sec4_title_en' => \App\Models\Setting::get('pdetail_sec4_title_en', 'Cellular Science & Mechanism of Action'),
            'pdetail_sec4_title_ar' => \App\Models\Setting::get('pdetail_sec4_title_ar', 'الآلية العلمية الدوائية المتقدمة'),

            'pdetail_sec5_badge_en' => \App\Models\Setting::get('pdetail_sec5_badge_en', 'Clinical Safety & Warnings'),
            'pdetail_sec5_badge_ar' => \App\Models\Setting::get('pdetail_sec5_badge_ar', 'الأمان والنزاهة الصيدلانية'),
            'pdetail_sec5_title_en' => \App\Models\Setting::get('pdetail_sec5_title_en', 'Safety Profile, Warnings & Precautions'),
            'pdetail_sec5_title_ar' => \App\Models\Setting::get('pdetail_sec5_title_ar', 'إرشادات السلامة، التحذيرات وموانع الاستعمال'),

            'pdetail_sec6_badge_en' => \App\Models\Setting::get('pdetail_sec6_badge_en', 'Batch Certification'),
            'pdetail_sec6_badge_ar' => \App\Models\Setting::get('pdetail_sec6_badge_ar', 'بيانات التوثيق والضمان'),
            'pdetail_sec6_title_en' => \App\Models\Setting::get('pdetail_sec6_title_en', 'Monograph & Supply Specifications'),
            'pdetail_sec6_title_ar' => \App\Models\Setting::get('pdetail_sec6_title_ar', 'المواصفات الفنية، التعبئة وضمان النقاء'),

            'pdetail_related_badge_en' => \App\Models\Setting::get('pdetail_related_badge_en', 'SYNERGISTIC LONGEVITY PROTOCOLS'),
            'pdetail_related_badge_ar' => \App\Models\Setting::get('pdetail_related_badge_ar', 'التكامل الحيوي'),
            'pdetail_related_title_en' => \App\Models\Setting::get('pdetail_related_title_en', 'Complementary Formulations'),
            'pdetail_related_title_ar' => \App\Models\Setting::get('pdetail_related_title_ar', 'تركيبات متكاملة تعزز النتائج'),
        ];
    }

    /**
     * Auto-create a draft product during Step 1 creation to convert the wizard into live auto-saving mode.
     */
    public function autoCreateDraft(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $nameEn = trim($request->input('name_en', ''));
            if (empty($nameEn)) {
                $nameEn = 'Bioceutical Draft ' . now()->format('Y-m-d H:i');
            }
            $nameAr = trim($request->input('name_ar', ''));
            if (empty($nameAr)) {
                $nameAr = 'مسودة تركيبة حيوية ' . now()->format('Y-m-d H:i');
            }

            $sku = trim($request->input('sku', ''));
            if (empty($sku)) {
                $sku = 'BZ-DFT-' . strtoupper(\Illuminate\Support\Str::random(6));
            } else {
                $count = Product::where('sku', $sku)->count();
                if ($count > 0) {
                    $sku .= '-' . rand(100, 999);
                }
            }

            $categoryId = $request->input('category_id');
            if (!$categoryId || !Category::where('id', $categoryId)->exists()) {
                $categoryId = Category::first()?->id ?? 1;
            }

            $product = new Product();
            $product->name_en = $nameEn;
            $product->name_ar = $nameAr;
            $product->sku = $sku;
            $product->category_id = $categoryId;
            $product->slug = \Illuminate\Support\Str::slug($nameEn) . '-' . strtolower(\Illuminate\Support\Str::random(5));
            $product->barcode = $request->input('barcode') ?: ('628' . rand(1000000, 9999999));
            
            $brand = trim((string) $request->input('brand', ''));
            $product->brand = !empty($brand) ? $brand : 'Blue Zone Bioceuticals';

            $productSize = trim((string) $request->input('product_size', ''));
            $product->product_size = !empty($productSize) ? $productSize : '60 Vegetable Capsules';

            $targetGender = trim((string) $request->input('target_gender', ''));
            $product->target_gender = !empty($targetGender) ? $targetGender : 'Unisex';

            $ageGroup = trim((string) $request->input('age_group', ''));
            $product->age_group = !empty($ageGroup) ? $ageGroup : '18+';

            $costPrice = $request->input('cost_price');
            $product->cost_price = ($costPrice !== null && $costPrice !== '') ? (float) $costPrice : 20.00;

            $price = $request->input('price');
            $product->price = ($price !== null && $price !== '') ? (float) $price : 60.00;

            $product->sale_price = $request->input('sale_price') ? (float) $request->input('sale_price') : null;
            $product->stock_online = 0;
            $product->shelf_life_months = (int) $request->input('shelf_life_months', 24);
            $product->status = 'inactive';
            $product->save();

            // Ensure linked InventoryItem exists for central inventory tracking
            InventoryItem::firstOrCreate(
                ['product_id' => $product->id, 'location_id' => 'online'],
                [
                    'location_name_en' => 'Online Store Buffer',
                    'location_name_ar' => 'مستودع المتجر الإلكتروني',
                    'current_stock' => 0,
                    'available_stock' => 0,
                    'reserved_stock' => 0,
                    'low_stock_threshold' => 10,
                    'unit_cost' => $product->cost_price,
                    'retail_price' => $product->price,
                ]
            );

            return response()->json([
                'success' => true,
                'product_id' => $product->id,
                'edit_url' => route('admin.products.edit', $product->id),
                'message' => app()->getLocale() === 'ar' ? 'تم إنشاء مسودة المنتج بنجاح!' : 'Product draft initiated successfully!',
            ]);
        } catch (\Throwable $e) {
            \Log::error('autoCreateDraft error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $this->formatUserFriendlyErrorMessage($e),
            ], 422);
        }
    }

    /**
     * Real-time step-by-step autosave endpoint for wizard.
     */
    public function autoSaveStep(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $product = Product::findOrFail($id);
        $step = (int) $request->input('step', 1);

        try {
            $data = $request->all();

            switch ($step) {
                case 1:
                    // Core Identifiers
                    if (!empty($data['sku'])) {
                        $exists = Product::where('sku', $data['sku'])->where('id', '!=', $product->id)->exists();
                        if (!$exists) $product->sku = $data['sku'];
                    }
                    if (isset($data['barcode'])) $product->barcode = $data['barcode'];
                    if (!empty($data['category_id'])) $product->category_id = $data['category_id'];
                    if (isset($data['subcategory_en'])) $product->subcategory_en = $data['subcategory_en'];
                    if (!empty($data['brand'])) $product->brand = trim($data['brand']);
                    if (!empty($data['target_gender'])) $product->target_gender = trim($data['target_gender']);
                    if (!empty($data['age_group'])) $product->age_group = trim($data['age_group']);
                    if (!empty($data['product_size'])) $product->product_size = trim($data['product_size']);
                    // Slug is locked and protected against manual modification
                    if (empty($product->slug)) {
                        $source = !empty($product->name_en) ? $product->name_en : ($product->name_ar ?? 'product');
                        $product->slug = Product::generateUniqueSlug($source, $product->id);
                    }
                    break;

                case 2:
                    // Bilingual Content
                    if (!empty($data['name_en'])) $product->name_en = $data['name_en'];
                    if (!empty($data['name_ar'])) $product->name_ar = $data['name_ar'];
                    if (empty($product->slug)) {
                        $source = !empty($data['name_en']) ? $data['name_en'] : ($data['name_ar'] ?? '');
                        if (!empty($source)) {
                            $product->slug = Product::generateUniqueSlug($source, $product->id);
                        }
                    }
                    if (isset($data['tagline_en'])) $product->tagline_en = $data['tagline_en'];
                    if (isset($data['tagline_ar'])) $product->tagline_ar = $data['tagline_ar'];
                    if (isset($data['description_en'])) $product->description_en = $data['description_en'];
                    if (isset($data['description_ar'])) $product->description_ar = $data['description_ar'];
                    if (isset($data['usage_en'])) $product->usage_en = $data['usage_en'];
                    if (isset($data['usage_ar'])) $product->usage_ar = $data['usage_ar'];
                    break;

                case 3:
                    // Pricing & Taxes
                    if (isset($data['cost_price'])) $product->cost_price = (float) $data['cost_price'];
                    if (isset($data['price'])) $product->price = (float) $data['price'];
                    $product->sale_price = (!empty($data['sale_price']) && (float) $data['sale_price'] > 0) ? (float) $data['sale_price'] : null;
                    break;

                case 4:
                    // Media (Primary Hero & Multi-Angle Gallery)
                    if ($request->hasFile('primary_image') || $request->hasFile('image')) {
                        $file = $request->file('primary_image') ?: $request->file('image');
                        if (is_array($file)) $file = reset($file);
                        if ($file && $file->isValid()) {
                            if (!empty($product->image)) {
                                $this->deletePhysicalProductFile($product->image);
                            }
                            $ext = $file->getClientOriginalExtension() ?: 'webp';
                            $filename = 'product_' . $product->id . '_' . time() . '.' . $ext;
                            $dest = public_path('storage/products/hero');
                            if (!file_exists($dest)) @mkdir($dest, 0755, true);
                            $file->move($dest, $filename);
                            $product->image = Product::normalizeUrl('/storage/products/hero/' . $filename);
                        }
                    } elseif ($request->filled('image') && is_string($request->input('image'))) {
                        $product->image = Product::normalizeUrl($request->input('image'));
                    }

                    // Process removals from gallery (database + Spatie + disk)
                    if ($request->has('removed_gallery_images')) {
                        $remList = (array) $request->input('removed_gallery_images');
                        foreach ($remList as $remUrl) {
                            $this->removeGalleryImageFromProduct($product, $remUrl);
                        }
                    }

                    // Process new gallery file uploads
                    if ($request->hasFile('gallery')) {
                        $currentImages = is_array($product->images) ? $product->images : [];
                        $files = (array) $request->file('gallery');
                        $dest = public_path('storage/products/gallery');
                        if (!file_exists($dest)) @mkdir($dest, 0755, true);

                        foreach ($files as $f) {
                            if ($f && $f->isValid()) {
                                $ext = $f->getClientOriginalExtension() ?: 'webp';
                                $fname = 'gallery_' . $product->id . '_' . time() . '_' . \Illuminate\Support\Str::random(6) . '.' . $ext;
                                $f->move($dest, $fname);
                                $currentImages[] = Product::normalizeUrl('/storage/products/gallery/' . $fname);
                            }
                        }
                        $product->images = array_values(array_unique(array_filter($currentImages)));
                    }
                    break;

                case 5:
                    // Clinical Data
                    $clinicalData = $data;
                    $this->formatScienceInputs($clinicalData);
                    foreach (['science_en', 'science_ar', 'benefits_en', 'benefits_ar', 'ingredients', 'clinical_mechanism', 'formula_details', 'contraindications', 'warnings'] as $fld) {
                        if (isset($clinicalData[$fld])) {
                            $product->{$fld} = $clinicalData[$fld];
                        }
                    }
                    break;

                case 6:
                    // Page & Sections Customizer & Synergistic Bundle
                    $secData = [];
                    $this->formatSectionSettings($secData, $request);
                    if (isset($secData['section_settings'])) {
                        $product->section_settings = $secData['section_settings'];
                    }
                    break;

                case 7:
                    // Inventory & Controls
                    if (isset($data['low_stock_threshold'])) $product->low_stock_threshold = (int) $data['low_stock_threshold'];
                    if (isset($data['shelf_life_months'])) $product->shelf_life_months = (int) $data['shelf_life_months'];
                    if (isset($data['stock_online'])) $product->stock_online = (int) $data['stock_online'];
                    $product->enable_backorders = $request->boolean('enable_backorders');
                    $product->is_featured = $request->boolean('is_featured');
                    $product->is_best_seller = $request->boolean('is_best_seller');
                    $product->is_new = $request->boolean('is_new');
                    $product->show_on_landing = $request->boolean('show_on_landing');
                    if (isset($data['landing_order'])) $product->landing_order = (int) $data['landing_order'];
                    if (isset($data['landing_badge_en'])) $product->landing_badge_en = $data['landing_badge_en'];
                    if (isset($data['landing_badge_ar'])) $product->landing_badge_ar = $data['landing_badge_ar'];
                    if (isset($data['status'])) $product->status = $data['status'];

                    // Sync InventoryItem
                    InventoryItem::updateOrCreate(
                        ['product_id' => $product->id, 'location_id' => 'online'],
                        [
                            'available_stock' => (int) $product->stock_online,
                            'low_stock_threshold' => (int) $product->low_stock_threshold,
                            'unit_cost' => (float) ($product->cost_price ?? 0),
                            'retail_price' => (float) ($product->price ?? 0),
                        ]
                    );

                    // Provision or sync initial ProductBatch
                    $initialBatchNumber = trim((string) $request->input('initial_batch_number', ''));
                    $batchStatus = $request->input('batch_status', 'approved');
                    $manufactureDate = $request->input('manufacture_date') ?: now()->toDateString();
                    $expiryDate = $request->input('expiry_date') ?: $product->calculateDefaultExpiryDate($manufactureDate);
                    $existingBatch = $product->batches()->first();

                    if (!$existingBatch && ((int) $product->stock_online > 0 || !empty($initialBatchNumber))) {
                        $qty = (int) $product->stock_online;
                        ProductBatch::create([
                            'product_id' => $product->id,
                            'batch_number' => !empty($initialBatchNumber) ? $initialBatchNumber : ProductBatch::generateBatchNumber(),
                            'location_id' => 'online',
                            'initial_qty' => $qty,
                            'current_qty' => $qty,
                            'available_qty' => $batchStatus === 'approved' ? $qty : 0,
                            'reserved_qty' => 0,
                            'quarantine_qty' => $batchStatus === 'quarantine' ? $qty : 0,
                            'unit_cost' => (float) ($product->cost_price ?? 0),
                            'manufacture_date' => $manufactureDate,
                            'expiry_date' => $expiryDate,
                            'status' => $batchStatus,
                        ]);
                    } elseif ($existingBatch) {
                        if (!empty($initialBatchNumber)) $existingBatch->batch_number = $initialBatchNumber;
                        if ($request->filled('manufacture_date')) $existingBatch->manufacture_date = $manufactureDate;
                        if ($request->filled('expiry_date')) $existingBatch->expiry_date = $expiryDate;
                        if ($request->filled('batch_status')) $existingBatch->status = $batchStatus;
                        $existingBatch->save();
                    }
                    break;
            }

            $product->save();

            $taxInfo = null;
            if ($step === 3) {
                $taxInfo = TaxService::breakdownPrice(
                    (float) ($product->price ?? 68.00),
                    (float) ($product->cost_price ?? 22.00),
                    $product->sale_price ? (float) $product->sale_price : null
                );
            }

            return response()->json([
                'success' => true,
                'step' => $step,
                'message' => app()->getLocale() === 'ar' ? "تم حفظ الخطوة ({$step}) بنجاح!" : "Step {$step} auto-saved successfully!",
                'product_id' => $product->id,
                'product_name' => $product->name_en,
                'taxInfo' => $taxInfo,
                'primary_image_url' => $product->primary_image_url,
                'gallery_urls' => $product->gallery_urls,
                'gallery_count' => count($product->gallery_urls),
                'saved_at' => now()->format('H:i:s'),
            ]);
        } catch (\Throwable $e) {
            \Log::error("autoSaveStep error for product {$id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $this->formatUserFriendlyErrorMessage($e),
            ], 422);
        }
    }

    /**
     * Convert low-level exceptions into professional, customer-friendly messages without exposing database internals.
     */
    protected function formatUserFriendlyErrorMessage(\Throwable $e): string
    {
        $msg = $e->getMessage();
        $isAr = app()->getLocale() === 'ar';

        // Check for SQL / Database integrity violations
        if (str_contains($msg, 'SQLSTATE') || str_contains($msg, 'Integrity constraint') || $e instanceof \Illuminate\Database\QueryException) {
            if (preg_match("/Column '([^']+)' cannot be null/i", $msg, $matches)) {
                $col = $matches[1];
                $colMap = [
                    'brand' => $isAr ? 'العلامة التجارية' : 'Brand',
                    'name_en' => $isAr ? 'اسم المنتج بالإنجليزي' : 'English Name',
                    'name_ar' => $isAr ? 'اسم المنتج بالعربي' : 'Arabic Name',
                    'sku' => $isAr ? 'رمز المنتج (SKU)' : 'SKU',
                    'category_id' => $isAr ? 'التصنيف' : 'Category',
                    'price' => $isAr ? 'سعر البيع' : 'Retail Price',
                    'cost_price' => $isAr ? 'سعر التكلفة' : 'Cost Price',
                ];
                $colLabel = $colMap[$col] ?? $col;
                return $isAr
                    ? "يرجى تحديد حقل «{$colLabel}» لاستكمال حفظ البيانات بنجاح."
                    : "Please specify the required field [{$colLabel}] to save formulation data.";
            }

            if (str_contains($msg, 'Duplicate entry') || str_contains($msg, '1062 Duplicate')) {
                return $isAr
                    ? 'رمز المنتج (SKU) أو الباركود مستخدم بالفعل لمنتج آخر. يرجى استخدام رمز فريد.'
                    : 'The SKU or Barcode is already assigned to another formulation. Please use a unique value.';
            }

            return $isAr
                ? 'تعذر حفظ بيانات المستحضر مؤقتاً بسبب عدم اكتمال بعض الحقول الأساسية. يرجى مراجعة المدخلات والمتابعة.'
                : 'Unable to save formulation draft. Please ensure all mandatory core fields are completed.';
        }

        return $msg;
    }

    /**
     * Safely delete an uploaded product image file from physical disk.
     */
    public function deletePhysicalProductFile(?string $url): void
    {
        if (empty($url) || !is_string($url)) {
            return;
        }

        $norm = Product::normalizeUrl($url);

        // Never delete seeded or static theme assets
        if (str_starts_with($norm, '/assets/') || str_starts_with($norm, 'assets/')) {
            return;
        }

        // Check storage path
        if (str_starts_with($norm, '/storage/')) {
            $relativePath = substr($norm, 9);
            $diskPath = storage_path('app/public/' . $relativePath);
            if (file_exists($diskPath) && is_file($diskPath)) {
                @unlink($diskPath);
            }
            $publicPath = public_path('storage/' . $relativePath);
            if (file_exists($publicPath) && is_file($publicPath)) {
                @unlink($publicPath);
            }
        }

        // Check uploads path
        if (str_starts_with($norm, '/uploads/')) {
            $publicPath = public_path(ltrim($norm, '/'));
            if (file_exists($publicPath) && is_file($publicPath)) {
                @unlink($publicPath);
            }
        }
    }

    /**
     * Remove a gallery image from both database array, Spatie media collection, and physical disk.
     */
    public function removeGalleryImageFromProduct(Product $product, string $urlToRemove): bool
    {
        $normTarget = Product::normalizeUrl($urlToRemove);
        $removed = false;

        // 1. Remove from database JSON array
        $currentImages = is_array($product->images) ? $product->images : [];
        $filteredImages = array_values(array_filter($currentImages, function ($img) use ($normTarget) {
            return Product::normalizeUrl($img) !== $normTarget;
        }));

        if (count($filteredImages) !== count($currentImages)) {
            $product->images = $filteredImages;
            $removed = true;
        }

        // 2. Remove from Spatie Media collection if found
        try {
            if (method_exists($product, 'getMedia')) {
                foreach ($product->getMedia('gallery') as $m) {
                    $mediaNorm = Product::normalizeUrl($m->getUrl());
                    if ($mediaNorm === $normTarget || Product::normalizeUrl($m->getPath()) === $normTarget) {
                        $m->delete();
                        $removed = true;
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning("Error deleting media from Spatie: " . $e->getMessage());
        }

        // 3. Remove physical disk file
        $this->deletePhysicalProductFile($urlToRemove);

        if ($removed) {
            $product->save();
        }

        return $removed;
    }

    /**
     * 1-Click Ajax endpoint to delete a specific gallery image permanently.
     */
    public function deleteGalleryImage(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $product = Product::findOrFail($id);
        $imageUrl = $request->input('image_url');

        if (empty($imageUrl)) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar' ? 'مسار الصورة غير محدد.' : 'Image URL not specified.',
            ], 422);
        }

        $this->removeGalleryImageFromProduct($product, $imageUrl);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' ? 'تم حذف الصورة من المعرض وقرص التخزين بنجاح.' : 'Image permanently removed from gallery and storage.',
            'remaining_images' => $product->gallery_urls,
            'count' => count($product->gallery_urls),
        ]);
    }

    /**
     * 1-Click Ajax endpoint to set any gallery photo as the primary product hero image.
     */
    public function setPrimaryImage(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $product = Product::findOrFail($id);
        $imageUrl = $request->input('image_url');

        if (empty($imageUrl)) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar' ? 'مسار الصورة غير صالح.' : 'Invalid image URL.',
            ], 422);
        }

        $normUrl = Product::normalizeUrl($imageUrl);
        $product->image = $normUrl;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' ? 'تم تعيين الصورة كصورة رئيسية للمنتج بنجاح.' : 'Image set as primary hero image successfully.',
            'primary_image_url' => $product->primary_image_url,
        ]);
    }
}
