<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('app.brand_name') . ' — ' . __('app.brand_tagline') }}</title>
    <meta name="description" content="{{ $description ?? __('shop.catalog_subtitle') }}">

    <!-- Dynamic Typography Engine -->
    @include('partials.fonts')

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Script to prevent flash of wrong theme (Default: Light) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('bluezone_theme') || localStorage.getItem('bz_theme');
                if (savedTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch(e) {}
        })();
    </script>
</head>
<body class="{{ $bodyClass ?? '' }}">
    {{ $slot }}

    @stack('scripts')
</body>
</html>
