<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('app.brand_name') . ' — ' . __('app.brand_tagline') }}</title>
    <meta name="description" content="{{ $description ?? __('shop.catalog_subtitle') }}">

    @if(request()->routeIs('customer.home') || request()->is('/'))
    <!-- Preload Hero LCP Image (Mobile and Desktop Responsive) on Homepage Only -->
    <link rel="preload" as="image" href="{{ asset('assets/images/hero/hero-01.webp') }}" imagesrcset="{{ asset('assets/images/hero/hero-01-mobile.webp') }} 768w, {{ asset('assets/images/hero/hero-01.webp') }} 1920w" imagesizes="100vw" type="image/webp" fetchpriority="high">
    @endif

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

    <!-- FontAwesome 6 Local Asset -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Custom CSS & Map Loader CSS from lazy_html (Cache-Busted) -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ file_exists(public_path('css/custom.css')) ? filemtime(public_path('css/custom.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('css/map-loader.css') }}?v={{ file_exists(public_path('css/map-loader.css')) ? filemtime(public_path('css/map-loader.css')) : time() }}">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Global Dynamic Typography Engine -->
    @include('partials.fonts')

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}">
    @stack('styles')
</head>
<body class="bg-[#F6F5EF] text-[#031827] dark:bg-[#031827] dark:text-[#F6F5EF] transition-colors duration-300 antialiased selection:bg-[#0A4F78] selection:text-white {{ $bodyClass ?? '' }}">
    {{ $slot }}

        <!-- Global Dynamic Currency Engine -->
    <script>
        window.BLUEZONE_CURRENCY = {
            code: @json(\App\Services\CurrencyService::code()),
            symbol: @json(\App\Services\CurrencyService::symbol()),
            position: @json(\App\Services\CurrencyService::position()),
            decimals: {{ \App\Services\CurrencyService::decimals() }},
            format: function(amount) {
                var num = (amount === null || amount === undefined || isNaN(amount)) ? 0 : parseFloat(amount);
                var formatted = num.toLocaleString(undefined, {
                    minimumFractionDigits: this.decimals,
                    maximumFractionDigits: this.decimals
                });
                if (this.position === 'before') {
                    var separator = (this.symbol.length > 1 && !/^[\$\€\£\¥]$/.test(this.symbol)) ? ' ' : '';
                    return this.symbol + separator + formatted;
                }
                return formatted + ' ' + this.symbol;
            }
        };
    </script>

    <!-- Early Global Cart Interface Stub -->
    <script>
        window.BLUEZONE_CART = window.BLUEZONE_CART || {
            _queue: [],
            add: function() { this._queue.push(['add', Array.from(arguments)]); },
            addItem: function() { this._queue.push(['addItem', Array.from(arguments)]); },
            open: function() { this._queue.push(['open', Array.from(arguments)]); },
            close: function() { this._queue.push(['close', Array.from(arguments)]); }
        };
    </script>

    <!-- Global Request Lazy Loading & Progress Engine -->
    <script src="{{ asset('js/request-loader.js') }}"></script>

    <!-- Scripts from lazy_html & Toast Engine (Deferred) -->
    <script defer src="{{ asset('js/toast.js') }}"></script>
    <script defer src="{{ asset('js/theme.js') }}"></script>
    <script defer src="{{ asset('js/cart.js') }}?v={{ file_exists(public_path('js/cart.js')) ? filemtime(public_path('js/cart.js')) : 1 }}"></script>
    <script defer src="{{ asset('js/wishlist.js') }}"></script>
    <script defer src="{{ asset('js/search.js') }}"></script>
    <script defer src="{{ asset('js/hero-slider.js') }}"></script>
    <script defer src="{{ asset('js/map.js') }}"></script>
    <script defer src="{{ asset('js/product-slider.js') }}"></script>
    <script defer src="{{ asset('js/products.js') }}"></script>
    <script defer src="{{ asset('js/app.js') }}"></script>

    <x-toast />

    <!-- Swiper JS -->
    <script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}"></script>

    @stack('scripts')
</body>
</html>
