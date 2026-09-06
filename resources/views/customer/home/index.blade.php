<x-layouts.customer :title="($landingSettings['landing_meta_title_' . (app()->getLocale() === 'ar' ? 'ar' : 'en')] ?? __('app.brand_name') . ' — Cellular Longevity & Botanical Medicine')">
@php
    $isAr = app()->getLocale() === 'ar';
    $lSettings = $landingSettings ?? ($settings ?? \App\View\ViewModels\SettingViewModel::all());
    $sectionsOrder = $landingSectionsOrder ?? ($lSettings['landing_sections_order'] ?? array_keys(\App\View\ViewModels\SettingViewModel::landingSections()));
@endphp

@foreach($sectionsOrder as $sectionKey)
    @php
        $isEnabled = $lSettings['landing_' . $sectionKey . '_enabled'] ?? true;
        if (is_string($isEnabled)) {
            $isEnabled = filter_var($isEnabled, FILTER_VALIDATE_BOOLEAN);
        }
    @endphp

    @if($isEnabled)
        @includeIf('customer.home.partials.' . $sectionKey, [
            'isAr' => $isAr,
            'lSettings' => $lSettings,
            'settings' => $lSettings,
            'allProducts' => $allProducts ?? [],
            'featuredProducts' => $featuredProducts ?? [],
            'bestSellers' => $bestSellers ?? [],
            'newArrivals' => $newArrivals ?? [],
            'scienceProducts' => $scienceProducts ?? collect(),
            'categories' => $categories ?? [],
            'hero' => $hero ?? [],
            'zones' => $zones ?? [],
            'faqs' => $faqs ?? [],
        ])
    @endif
@endforeach

</x-layouts.customer>
