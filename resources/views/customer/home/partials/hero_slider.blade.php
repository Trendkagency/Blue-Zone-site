@php
    $isAr = app()->getLocale() === 'ar';
    $lSettings = $lSettings ?? ($settings ?? ($landingSettings ?? \App\View\ViewModels\SettingViewModel::all()));

    // Master Hero Slider Enable/Disable Switch
    $sliderEnabled = $lSettings['landing_hero_slider_enabled'] ?? true;
    if (is_string($sliderEnabled)) {
        $sliderEnabled = filter_var($sliderEnabled, FILTER_VALIDATE_BOOLEAN);
    }

    $autoplayEnabled = $lSettings['landing_hero_slider_autoplay'] ?? true;
    if (is_string($autoplayEnabled)) {
        $autoplayEnabled = filter_var($autoplayEnabled, FILTER_VALIDATE_BOOLEAN);
    }
    $autoplayDelay = (int) ($lSettings['landing_hero_slider_delay'] ?? 5500);

    // Fetch dynamic slides
    if (!isset($heroSlides) || (is_object($heroSlides) && $heroSlides->isEmpty()) || (is_array($heroSlides) && empty($heroSlides))) {
        try {
            $slides = \App\Models\HeroSlide::active()->ordered()->with('product')->get();
        } catch (\Throwable) {
            $slides = collect();
        }
    } else {
        $slides = is_array($heroSlides) ? collect($heroSlides) : $heroSlides;
    }
@endphp

@if ($sliderEnabled && $slides->isNotEmpty())
    <!-- Pass dynamic slider settings to client script -->
    <script>
        window.BLUEZONE_HERO_CONFIG = {
            delay: {{ $autoplayDelay }},
            autoplay: {{ $autoplayEnabled ? 'true' : 'false' }}
        };
    </script>

    <!-- 02. HERO SLIDER (DYNAMIC LIFESTYLE & PRODUCT SHOWCASE CAROUSEL) -->
    <section id="hero-slider-container"
        class="relative w-full h-[85vh] min-h-[580px] bg-[#031827] overflow-hidden select-none group"
        aria-label="{{ $isAr ? 'سلايدر العرض الرئيسي' : 'Hero Product Showcase' }}">

        @foreach ($slides as $idx => $slide)
            @php
                $isFirst = $idx === 0;
                $bgUrl = $slide->display_bg_image_url;
                $badgeText = $slide->display_badge;
                $badgeColor = $slide->badge_color ?? 'emerald';
                $badgeClasses = match ($badgeColor) {
                    'cyan' => 'bg-[#2A8FC2]/20 text-[#2A8FC2] border-[#2A8FC2]/40',
                    'amber' => 'bg-amber-500/20 text-amber-400 border-amber-500/40',
                    'rose' => 'bg-rose-500/20 text-rose-400 border-rose-500/40',
                    'purple' => 'bg-purple-500/20 text-purple-400 border-purple-500/40',
                    default => 'bg-[#67B34A]/20 text-[#67B34A] border-[#67B34A]/40',
                };

                $btn1Text = $slide->display_primary_cta_text;
                $btn1Link = $slide->primary_cta_link ?: ($slide->product ? route('customer.product.show', $slide->product->slug) : route('customer.products'));
                $btn1Style = $slide->primary_cta_style ?? 'emerald';
                $btn1Classes = match ($btn1Style) {
                    'cyan' => 'bg-[#2A8FC2] hover:bg-[#0A4F78] text-white',
                    'white' => 'bg-white hover:bg-gray-100 text-[#031827]',
                    default => 'bg-[#67B34A] hover:bg-[#589c3e] text-white',
                };

                $btn2Text = $slide->display_secondary_cta_text;
                $btn2Link = $slide->secondary_cta_link ?: route('customer.products');

                $showCard = (bool) ($slide->show_floating_card ?? true);
                $cardBadge = $slide->display_card_badge;
                $cardTitle = $slide->display_card_title;
                $cardSubtitle = $slide->display_card_subtitle;
                $cardImage = $slide->display_card_image_url;
                $cardLink = $slide->resolved_card_link;
            @endphp

            <!-- Slide {{ $idx + 1 }}: {{ $slide->display_title }} -->
            <div
                class="hero-slide absolute inset-0 w-full h-full {{ $isFirst ? 'opacity-100 z-10' : 'opacity-0 pointer-events-none' }} transition-opacity duration-1000 ease-in-out">
                <img src="{{ $bgUrl }}"
                    alt="{{ $slide->display_title }}"
                    onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-01.webp') }}';"
                    width="1920" height="1080"
                    {{ $isFirst ? 'fetchpriority=high loading=eager' : 'loading=lazy' }} decoding="async"
                    class="w-full h-full object-cover filter brightness-[0.7]" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#031827] via-[#031827]/40 to-transparent"></div>

                <!-- Slide Copy & Call To Action Center -->
                <div class="absolute inset-0 flex items-center justify-center text-center p-4 sm:p-6 lg:p-8">
                    <div class="max-w-4xl space-y-4 sm:space-y-6">
                        @if (!empty($badgeText))
                            <span
                                class="inline-block px-4 py-1.5 rounded-full {{ $badgeClasses }} text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.3em] backdrop-blur-md border shadow-lg">
                                {{ $badgeText }}
                            </span>
                        @endif

                        <h2 class="text-3xl sm:text-6xl md:text-7xl font-black text-white tracking-tight leading-none drop-shadow-2xl">
                            {!! $slide->formatted_title !!}
                        </h2>

                        @if (!empty($slide->display_subtitle))
                            <p class="text-xs sm:text-lg md:text-xl text-[#E8DCC4] font-medium tracking-wide max-w-2xl mx-auto leading-relaxed">
                                {{ $slide->display_subtitle }}
                            </p>
                        @endif

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 pt-2 sm:pt-4">
                            @if (!empty($btn1Text))
                                <a href="{{ $btn1Link }}"
                                    class="w-full sm:w-auto px-8 py-4 {{ $btn1Classes }} text-xs font-black uppercase tracking-widest rounded-full transition-all shadow-xl hover:scale-105 btn-sheen text-center">
                                    {{ $btn1Text }}
                                </a>
                            @endif

                            @if (!empty($btn2Text))
                                <a href="{{ $btn2Link }}"
                                    class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-black uppercase tracking-widest rounded-full backdrop-blur-md transition-all text-center">
                                    {{ $btn2Text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Floating Glassmorphic Product / Regional Hotspot Badge -->
                @if ($showCard)
                    <div class="hidden lg:flex absolute bottom-24 sm:bottom-28 ltr:right-8 sm:ltr:right-12 rtl:left-8 sm:rtl:left-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-white/15 dark:border-[#2A8FC2]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer"
                        onclick="window.location.href='{{ $cardLink }}'">
                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-white/10 p-1 flex items-center justify-center">
                            <img src="{{ $cardImage }}"
                                alt="{{ $cardTitle }}"
                                onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.webp') }}';"
                                width="48" height="48" loading="lazy" decoding="async"
                                class="w-full h-full object-contain" />
                        </div>
                        <div class="text-left rtl:text-right space-y-0.5">
                            <span class="block text-[9px] font-mono font-bold text-[#67B34A] uppercase tracking-widest">
                                {{ $cardBadge }}
                            </span>
                            <span class="block text-xs font-black text-white">
                                {{ $cardTitle }}
                            </span>
                            <span class="block text-[10px] text-[#2A8FC2] font-bold">
                                {{ $cardSubtitle }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        @if ($slides->count() > 1)
            <!-- Navigation Arrows -->
            <button onclick="BLUEZONE_HERO.prev()" aria-label="{{ $isAr ? 'الشريحة السابقة' : 'Previous slide' }}"
                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-30 p-3 rounded-full bg-black/30 hover:bg-black/60 text-white backdrop-blur-md transition-all cursor-pointer border border-white/20 hover:scale-110">
                ‹
            </button>
            <button onclick="BLUEZONE_HERO.next()" aria-label="{{ $isAr ? 'الشريحة التالية' : 'Next slide' }}"
                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-30 p-3 rounded-full bg-black/30 hover:bg-black/60 text-white backdrop-blur-md transition-all cursor-pointer border border-white/20 hover:scale-110">
                ›
            </button>

            <!-- Pagination Dots -->
            <div class="absolute bottom-6 sm:bottom-8 left-1/2 -translate-x-1/2 z-30 flex items-center space-x-1.5 sm:space-x-2">
                @foreach ($slides as $idx => $s)
                    <button onclick="BLUEZONE_HERO.goTo({{ $idx }})"
                        class="hero-dot p-2 inline-flex items-center justify-center cursor-pointer min-w-[32px] min-h-[32px]"
                        aria-label="Go to slide {{ $idx + 1 }}">
                        <span
                            class="hero-dot-indicator {{ $idx === 0 ? 'w-8 h-2 rounded-full bg-[#67B34A]' : 'w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80' }} transition-all duration-300 block pointer-events-none"></span>
                    </button>
                @endforeach
            </div>
        @endif
    </section>
@endif
