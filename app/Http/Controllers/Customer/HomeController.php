<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\ContentViewModel;
use App\View\ViewModels\ProductViewModel;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Render the Blue Zone flagship homepage.
     */
    public function index(): View
    {
        $allProducts = ProductViewModel::all();
        $featuredProducts = array_values(array_filter($allProducts, fn($p) => $p['is_featured'] ?? false));
        $bestSellers = array_values(array_filter($allProducts, fn($p) => $p['is_best_seller'] ?? false));
        $newArrivals = array_values(array_filter($allProducts, fn($p) => ($p['is_new'] ?? false) || in_array($p['slug'], ['blue-cell', 'blue-metabolic', 'blue-defense', 'blue-vitality'])));
        if (empty($newArrivals)) {
            $newArrivals = array_slice($allProducts, 0, 4);
        }
        $categories = CategoryViewModel::all();
        $content = ContentViewModel::all();

        try {
            $dbProducts = \App\Models\Product::orderBy('id', 'desc')->with('category')->where(function ($q) {
                $q->where('status', 'active')->orWhere('is_active', true);
            })->get();
        } catch (\Throwable) {
            $dbProducts = collect();
        }
        $scienceProducts = $dbProducts->isNotEmpty() ? $dbProducts : collect(ProductViewModel::all());

        if ($dbProducts->isNotEmpty()) {
            $dbNew = $dbProducts->filter(fn($p) => ($p->is_new ?? false) || in_array($p->slug, ['blue-cell', 'blue-metabolic', 'blue-defense', 'blue-vitality', 'blue-mind', 'blue-gut']));
            if ($dbNew->count() < 4) {
                $dbNew = $dbProducts;
            }
            if ($dbNew->isNotEmpty()) {
                $newArrivals = $dbNew->values()->all();
            }
        } elseif (count($newArrivals) < 6) {
            $newArrivals = $allProducts;
        }

        $defaults = \App\View\ViewModels\SettingViewModel::all();
        $saved = \App\Models\Setting::getAll();
        $settings = array_merge($defaults, $saved);

        $defaultOrder = $defaults['landing_sections_order'] ?? array_keys(\App\View\ViewModels\SettingViewModel::landingSections());
        $configuredOrder = $settings['landing_sections_order'] ?? $defaultOrder;
        if (is_string($configuredOrder)) {
            $decoded = json_decode($configuredOrder, true);
            if (is_array($decoded) && !empty($decoded)) {
                $configuredOrder = $decoded;
            }
        }
        if (!is_array($configuredOrder) || empty($configuredOrder)) {
            $configuredOrder = $defaultOrder;
        }

        // Ensure all valid sections exist in the order list
        $allKnownSections = array_keys(\App\View\ViewModels\SettingViewModel::landingSections());
        $landingSectionsOrder = array_values(array_intersect($configuredOrder, $allKnownSections));
        $missingSections = array_diff($allKnownSections, $landingSectionsOrder);
        foreach ($missingSections as $missing) {
            $landingSectionsOrder[] = $missing;
        }

        // Dynamic Featured Products showcase based on per-product landing setting
        try {
            $dbLandingProducts = \App\Models\Product::with('category')->forLanding()->get();
            if ($dbLandingProducts->isNotEmpty()) {
                $featuredProducts = $dbLandingProducts->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'slug' => $p->slug,
                        'name_en' => $p->name_en,
                        'name_ar' => $p->name_ar,
                        'tagline_en' => $p->tagline_en,
                        'tagline_ar' => $p->tagline_ar,
                        'category_en' => $p->category?->name_en ?? 'Cellular Longevity',
                        'category_ar' => $p->category?->name_ar ?? 'الصحة الخلوية',
                        'price' => (float) $p->price,
                        'sale_price' => $p->sale_price ? (float) $p->sale_price : null,
                        'rating' => (float) ($p->rating ?? 4.9),
                        'reviews_count' => (int) ($p->reviews_count ?? 128),
                        'image' => $p->primary_image_url ?? $p->image,
                        'images' => $p->images ?? [],
                        'short_description_en' => $p->short_description_en,
                        'short_description_ar' => $p->short_description_ar,
                        'is_featured' => (bool) $p->is_featured,
                        'is_best_seller' => (bool) $p->is_best_seller,
                        'is_new' => (bool) $p->is_new,
                        'show_on_landing' => (bool) $p->show_on_landing,
                        'landing_order' => (int) $p->landing_order,
                        'landing_badge_en' => $p->landing_badge_en,
                        'landing_badge_ar' => $p->landing_badge_ar,
                        'benefits_en' => $p->benefits_en ?? [],
                        'benefits_ar' => $p->benefits_ar ?? [],
                    ];
                })->all();
            }
        } catch (\Throwable) {
            // Keep fallback
        }

        $productLimit = (int) ($settings['landing_products_limit'] ?? 6);
        if ($productLimit > 0 && count($featuredProducts) > $productLimit) {
            $featuredProducts = array_slice($featuredProducts, 0, $productLimit);
            $bestSellers = array_slice($bestSellers, 0, $productLimit);
        }

        try {
            // This Is Feature Products 
            $products = \App\Models\Product::with('category')->where('is_active', true)->orderBy('sort_order')->get();
            if ($products->isEmpty()) {
                $products = collect(ProductViewModel::all());
            }
        } catch (\Throwable) {
            $products = collect(ProductViewModel::all());
        }
        //  Get The Main Product take over view and Rating 
        $mainProduct = \App\Models\Product::query()
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->orderByDesc('reviews_count')
            ->first();
        // Fetch dynamic Hero Carousel Slides
        try {
            $heroSlides = \App\Models\HeroSlide::active()->ordered()->with('product')->get();
        } catch (\Throwable) {
            $heroSlides = collect();
        }

        return view('customer.home.index', [
            'allProducts' => $allProducts,
            'products' => $products,
            'mainProduct' => $mainProduct,
            'featuredProducts' => $featuredProducts,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals,
            'scienceProducts' => $scienceProducts,
            'categories' => $categories,
            'hero' => $content['hero'],
            'heroSlides' => $heroSlides,
            'zones' => $content['zones'],
            'faqs' => $content['faqs'],
            'settings' => $settings,
            'landingSettings' => $settings,
            'landingSectionsOrder' => $landingSectionsOrder,
            'landingSectionsMeta' => \App\View\ViewModels\SettingViewModel::landingSections(),
        ]);
    }
}
