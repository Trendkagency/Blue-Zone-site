<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('app.brand_name') . ' — ' . __('app.brand_tagline') }}</title>
    <meta name="description" content="{{ $description ?? __('shop.catalog_subtitle') }}">

    <!-- Immediate Theme Initialization to Prevent Flash of Wrong Theme (Default: Light) -->
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

    <!-- Suppress Tailwind CDN production warning in browser console -->
    <script>
        (function() {
            const originalWarn = console.warn;
            console.warn = function(...args) {
                if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com should not be used in production')) {
                    return;
                }
                originalWarn.apply(console, args);
            };
        })();
    </script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'deep-navy': '#031827',
                        'dark-navy': '#062B49',
                        'ocean-blue': '#0A4F78',
                        'accent-blue': '#2A8FC2',
                        'natural-green': '#67B34A',
                        'leaf-green': '#B8D98A',
                        'off-white': '#F6F5EF',
                        'warm-sand': '#E8DCC4',
                    },
                    fontFamily: {
                        cairo: ['var(--font-family-base)', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', 'sans-serif'],
                        primary: ['var(--font-family-base)', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', 'sans-serif'],
                        headings: ['var(--font-family-headings)', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 Local Asset (Zero CDN Network Dependency) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Custom CSS & Map Loader CSS from lazy_html -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/map-loader.css') }}">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Global Dynamic Typography Engine -->
    @include('partials.fonts')
</head>
<body class="bg-[#F6F5EF] text-[#031827] dark:bg-[#031827] dark:text-[#F6F5EF] transition-colors duration-300 antialiased selection:bg-[#0A4F78] selection:text-white {{ $bodyClass ?? '' }}">
    {{ $slot }}

    <!-- Scripts from lazy_html & Toast Engine -->
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/wishlist.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>
    <script src="{{ asset('js/hero-slider.js') }}"></script>
    <script src="{{ asset('js/map.js') }}"></script>
    <script src="{{ asset('js/product-slider.js') }}"></script>
    <script src="{{ asset('js/products.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <x-toast />

    @stack('scripts')
</body>
</html>
