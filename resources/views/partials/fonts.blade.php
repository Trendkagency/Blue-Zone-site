@php
    $config = \App\Services\TypographyService::getActiveConfig();
    $fontsToLoad = array_unique([$config['font_family'], $config['font_heading_family']]);
    $primaryFontUrl = \App\Services\TypographyService::getPrimaryFontsUrl($fontsToLoad);
    $fallbackFontUrl = \App\Services\TypographyService::getFallbackFontsUrl($fontsToLoad);
@endphp

<!-- Blue Zone Global Dynamic Typography Engine (Fast Bunny Edge CDN + Google Fallback) -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="{{ $primaryFontUrl }}" rel="stylesheet" id="bz-global-typography-font" onerror="if(!this.dataset.failed){this.dataset.failed='1';this.href='{{ $fallbackFontUrl }}';}">

<style id="bz-global-typography-vars">

    :root {
        --font-family-base: {!! $config['font_family_stack'] ?? "'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif" !!};
        --font-family-headings: {!! $config['font_heading_family_stack'] ?? "var(--font-family-base)" !!};
        --font-size-base: {{ $config['font_size_base'] }};
        --font-weight-headings: {{ $config['font_weight_headings'] }};
        --font-weight-body: {{ $config['font_weight_body'] }};
        --font-letter-spacing: {{ $config['font_letter_spacing'] }};
    }

    body, input, select, textarea, button {
        font-family: var(--font-family-base) !important;
        font-weight: var(--font-weight-body) !important;
        letter-spacing: var(--font-letter-spacing) !important;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: var(--font-family-headings) !important;
        font-weight: var(--font-weight-headings) !important;
    }

    /* Common Heading & Brand classes */
    .brand-title,
    .section-title,
    .page-title,
    .sidebar-brand-title {
        font-family: var(--font-family-headings) !important;
    }
</style>
