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
        $featuredProducts = array_values(array_filter($allProducts, fn ($p) => $p['is_featured'] ?? false));
        $bestSellers = array_values(array_filter($allProducts, fn ($p) => $p['is_best_seller'] ?? false));
        $newArrivals = array_values(array_filter($allProducts, fn ($p) => ($p['is_new'] ?? false) || in_array($p['slug'], ['blue-cell', 'blue-metabolic', 'blue-defense', 'blue-vitality'])));
        if (empty($newArrivals)) {
            $newArrivals = array_slice($allProducts, 0, 4);
        }
        $categories = CategoryViewModel::all();
        $content = ContentViewModel::all();

        try {
            $dbProducts = \App\Models\Product::with('category')->where(function ($q) {
                $q->where('status', 'active')->orWhere('is_active', true);
            })->get();
        } catch (\Throwable) {
            $dbProducts = collect();
        }
        $scienceProducts = $dbProducts->isNotEmpty() ? $dbProducts : collect(ProductViewModel::all());

        $defaults = \App\View\ViewModels\SettingViewModel::all();
        $saved = \App\Models\Setting::getAll();
        $settings = array_merge($defaults, $saved);

        $defaultOrder = $defaults['landing_sections_order'] ?? array_keys(\App\View\ViewModels\SettingViewModel::landingSections());
        $configuredOrder = $settings['landing_sections_order'] ?? $defaultOrder;
        if (is_string($configuredOrder)) {
            $decoded = json_decode($configuredOrder, true);
            if (is_array($decoded)) {
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

        $productLimit = (int) ($settings['landing_products_limit'] ?? 6);
        if ($productLimit > 0) {
            $featuredProducts = array_slice($featuredProducts, 0, $productLimit);
            $bestSellers = array_slice($bestSellers, 0, $productLimit);
        }

        return view('customer.home.index', [
            'allProducts' => $allProducts,
            'featuredProducts' => $featuredProducts,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals,
            'scienceProducts' => $scienceProducts,
            'categories' => $categories,
            'hero' => $content['hero'],
            'zones' => $content['zones'],
            'faqs' => $content['faqs'],
            'settings' => $settings,
            'landingSettings' => $settings,
            'landingSectionsOrder' => $landingSectionsOrder,
            'landingSectionsMeta' => \App\View\ViewModels\SettingViewModel::landingSections(),
        ]);
    }
}
