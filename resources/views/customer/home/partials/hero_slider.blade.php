@php
    $isAr = app()->getLocale() === 'ar';
    $lSettings = $lSettings ?? ($settings ?? ($landingSettings ?? \App\View\ViewModels\SettingViewModel::all()));

    $heroBadge = $isAr ? ($lSettings['landing_hero_badge_ar'] ?? $lSettings['landing_hero_badge_en'] ?? 'CENTENARIAN WISDOM') : ($lSettings['landing_hero_badge_en'] ?? 'CENTENARIAN WISDOM');
    $heroTitle = $isAr ? ($lSettings['landing_hero_title_ar'] ?? $lSettings['landing_hero_title_en'] ?? 'LIVE LONG. LIVE WELL.') : ($lSettings['landing_hero_title_en'] ?? 'LIVE LONG. LIVE WELL.');
    $heroSub = $isAr ? ($lSettings['landing_hero_subtitle_ar'] ?? $lSettings['landing_hero_subtitle_en'] ?? '') : ($lSettings['landing_hero_subtitle_en'] ?? '');
    $heroCta1Text = $isAr ? ($lSettings['landing_hero_cta_primary_text_ar'] ?? $lSettings['landing_hero_cta_primary_text_en'] ?? 'DISCOVER OUR STORY') : ($lSettings['landing_hero_cta_primary_text_en'] ?? 'DISCOVER OUR STORY');
    $heroCta1Link = $lSettings['landing_hero_cta_primary_link'] ?? '#who-we-are';
    $heroCta2Text = $isAr ? ($lSettings['landing_hero_cta_secondary_text_ar'] ?? $lSettings['landing_hero_cta_secondary_text_en'] ?? 'EXPLORE PRODUCTS') : ($lSettings['landing_hero_cta_secondary_text_en'] ?? 'EXPLORE PRODUCTS');
    $heroCta2Link = $lSettings['landing_hero_cta_secondary_link'] ?? route('customer.products');
@endphp

    <!-- 02. HERO SLIDER (5 FULL-WIDTH LIFESTYLE & PRODUCT SHOWCASE SLIDES) -->
    <section id="hero-slider-container" class="relative w-full h-[85vh] min-h-[580px] bg-[#031827] overflow-hidden select-none group">
      
      <!-- Slide 1: Okinawa & Centenarian Wisdom -->
      <div class="hero-slide absolute inset-0 w-full h-full opacity-100 transition-opacity duration-1000 ease-in-out z-10">
        <img src="{{ asset('assets/images/hero/hero-01.webp') }}"
             srcset="{{ asset('assets/images/hero/hero-01-mobile.webp') }} 768w, {{ asset('assets/images/hero/hero-01.webp') }} 1920w"
             sizes="(max-width: 768px) 100vw, 100vw"
             alt="Blue Zone Longevity"
             onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-01.jpg') }}';"
             width="1920" height="1080"
             fetchpriority="high" loading="eager" decoding="async"
             class="w-full h-full object-cover filter brightness-[0.7]" />
        <div class="absolute inset-0 bg-gradient-to-t from-[#031827] via-[#031827]/40 to-transparent"></div>
        <div class="absolute inset-0 flex items-center justify-center text-center p-4 sm:p-6 lg:p-8">
          <div class="max-w-4xl space-y-4 sm:space-y-6">
            <span class="inline-block px-4 py-1.5 rounded-full bg-[#67B34A]/20 text-[#67B34A] text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.3em] backdrop-blur-md border border-[#67B34A]/40 shadow-lg">
              {{ $heroBadge }}
            </span>
            <h1 class="text-3xl sm:text-6xl md:text-7xl font-black text-white tracking-tight leading-none drop-shadow-2xl">
              {{ $heroTitle }}
            </h1>
            <p class="text-xs sm:text-lg md:text-xl text-[#E8DCC4] font-medium tracking-wide max-w-2xl mx-auto leading-relaxed">
              {{ $heroSub }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 pt-2 sm:pt-4">
              <a href="{{ $heroCta1Link }}" class="w-full sm:w-auto px-8 py-4 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest rounded-full transition-all shadow-xl hover:scale-105 btn-sheen text-center">
                {{ $heroCta1Text }}
              </a>
              <a href="{{ $heroCta2Link }}" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-black uppercase tracking-widest rounded-full backdrop-blur-md transition-all text-center">
                {{ $heroCta2Text }}
              </a>
            </div>
          </div>
        </div>

        <!-- Floating Product Badge -->
        <div class="hidden lg:flex absolute bottom-24 sm:bottom-28 ltr:right-8 sm:ltr:right-12 rtl:left-8 sm:rtl:left-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#67B34A]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='{{ route('customer.product.show', 'blue-mind') }}'">
          <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center">
            <img src="{{ asset('assets/products/blue-mind.webp') }}" alt="BLUE MIND" onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.jpg') }}';" width="48" height="48" loading="lazy" decoding="async" class="w-full h-full object-contain" />
          </div>
          <div class="text-left space-y-0.5">
            <span class="block text-[9px] font-mono font-bold text-[#67B34A] uppercase tracking-widest">FLAGSHIP NOOTROPIC</span>
            <span class="block text-xs font-black text-white">BLUE MIND</span>
            <span class="block text-[10px] text-[#2A8FC2] font-bold">★ 4.9 Verified Reviews</span>
          </div>
        </div>
      </div>

      <!-- Slide 2: Bio-Engineered Nutrition -->
      <div class="hero-slide absolute inset-0 w-full h-full opacity-0 pointer-events-none transition-opacity duration-1000 ease-in-out">
        <img src="{{ asset('assets/images/hero/hero-02.webp') }}" alt="Scientific Formulation" onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-02.jpg') }}';" width="1920" height="1080" loading="lazy" decoding="async" class="w-full h-full object-cover filter brightness-[0.7]" />
        <div class="absolute inset-0 bg-gradient-to-t from-[#031827] via-[#031827]/40 to-transparent"></div>
        <div class="absolute inset-0 flex items-center justify-center text-center p-4 sm:p-6 lg:p-8">
          <div class="max-w-4xl space-y-4 sm:space-y-6">
            <span class="inline-block px-4 py-1.5 rounded-full bg-[#2A8FC2]/20 text-[#2A8FC2] text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.3em] backdrop-blur-md border border-[#2A8FC2]/40 shadow-lg">
              BIO-ENGINEERED NUTRITION
            </span>
            <h2 class="text-3xl sm:text-6xl md:text-7xl font-black text-white tracking-tight leading-none drop-shadow-2xl">
              CLINICAL BOTANICAL <span class="text-[#2A8FC2]">INTEGRITY.</span>
            </h2>
            <p class="text-xs sm:text-lg md:text-xl text-[#E8DCC4] font-medium tracking-wide max-w-2xl mx-auto leading-relaxed">
              Bio-identical dietary compounds standardized for optimal cellular absorption and daily mitochondrial vitality.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 pt-2 sm:pt-4">
              <a href="{{ route('customer.pages.science') }}" class="w-full sm:w-auto px-8 py-4 bg-[#2A8FC2] hover:bg-[#0A4F78] text-white text-xs font-black uppercase tracking-widest rounded-full transition-all shadow-xl hover:scale-105 btn-sheen text-center">
                EXPLORE OUR SCIENCE
              </a>
              <a href="{{ route('customer.shop') }}" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-black uppercase tracking-widest rounded-full backdrop-blur-md transition-all text-center">
                SHOP FORMULATIONS
              </a>
            </div>
          </div>
        </div>

        <!-- Floating Product Badge -->
        <div class="hidden lg:flex absolute bottom-24 sm:bottom-28 ltr:right-8 sm:ltr:right-12 rtl:left-8 sm:rtl:left-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#2A8FC2]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='{{ route('customer.product.show', 'blue-cell') }}'">
          <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center">
            <img src="{{ asset('assets/products/blue-cell.jpg') }}" alt="BLUE CELL" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" width="48" height="48" loading="lazy" decoding="async" class="w-full h-full object-contain" />
          </div>
          <div class="text-left space-y-0.5">
            <span class="block text-[9px] font-mono font-bold text-[#2A8FC2] uppercase tracking-widest">MITOCHONDRIAL ATP</span>
            <span class="block text-xs font-black text-white">BLUE CELL</span>
            <span class="block text-[10px] text-[#67B34A] font-bold">★ 4.9 Verified Reviews</span>
          </div>
        </div>
      </div>

      <!-- Slide 3: 6 Pillars & Daily Movement -->
      <div class="hero-slide absolute inset-0 w-full h-full opacity-0 pointer-events-none transition-opacity duration-1000 ease-in-out">
        <img src="{{ asset('assets/images/hero/hero-03.webp') }}" alt="Centenarian Lifestyle" onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-03.jpg') }}';" width="1920" height="1080" loading="lazy" decoding="async" class="w-full h-full object-cover filter brightness-[0.7]" />
        <div class="absolute inset-0 bg-gradient-to-t from-[#031827] via-[#031827]/40 to-transparent"></div>
        <div class="absolute inset-0 flex items-center justify-center text-center p-4 sm:p-6 lg:p-8">
          <div class="max-w-4xl space-y-4 sm:space-y-6">
            <span class="inline-block px-4 py-1.5 rounded-full bg-[#67B34A]/20 text-[#67B34A] text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.3em] backdrop-blur-md border border-[#67B34A]/40 shadow-lg">
              WELLNESS PHILOSOPHY
            </span>
            <h2 class="text-3xl sm:text-6xl md:text-7xl font-black text-white tracking-tight leading-none drop-shadow-2xl">
              DAILY VITALITY & <span class="text-[#67B34A]">FLUID MOVEMENT.</span>
            </h2>
            <p class="text-xs sm:text-lg md:text-xl text-[#E8DCC4] font-medium tracking-wide max-w-2xl mx-auto leading-relaxed">
              Integrating natural movement, plant-rich nutrition, and cognitive purpose into daily routines.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 pt-2 sm:pt-4">
              <a href="#philosophy" class="w-full sm:w-auto px-8 py-4 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest rounded-full transition-all shadow-xl hover:scale-105 btn-sheen text-center">
                THE 6 PILLARS
              </a>
              <a href="{{ route('customer.products') }}" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-black uppercase tracking-widest rounded-full backdrop-blur-md transition-all text-center">
                VIEW CATALOG
              </a>
            </div>
          </div>
        </div>

        <!-- Floating Product Badge -->
        <div class="hidden lg:flex absolute bottom-24 sm:bottom-28 ltr:right-8 sm:ltr:right-12 rtl:left-8 sm:rtl:left-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#67B34A]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='{{ route('customer.product.show', 'blue-flex') }}'">
          <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center">
            <img src="{{ asset('assets/products/blue-flex.webp') }}" alt="BLUE FLEX" onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-flex.jpg') }}';" width="48" height="48" loading="lazy" decoding="async" class="w-full h-full object-contain" />
          </div>
          <div class="text-left space-y-0.5">
            <span class="block text-[9px] font-mono font-bold text-[#67B34A] uppercase tracking-widest">JOINT MOBILITY</span>
            <span class="block text-xs font-black text-white">BLUE FLEX</span>
            <span class="block text-[10px] text-[#2A8FC2] font-bold">★ 4.7 Verified Reviews</span>
          </div>
        </div>
      </div>

      <!-- Slide 4: 5 Centenarian Regions -->
      <div class="hero-slide absolute inset-0 w-full h-full opacity-0 pointer-events-none transition-opacity duration-1000 ease-in-out">
        <img src="{{ asset('assets/images/hero/hero-04.webp') }}" alt="Mediterranean Blue Zone" onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-04.jpg') }}';" width="1920" height="1080" loading="lazy" decoding="async" class="w-full h-full object-cover filter brightness-[0.7]" />
        <div class="absolute inset-0 bg-gradient-to-t from-[#031827] via-[#031827]/40 to-transparent"></div>
        <div class="absolute inset-0 flex items-center justify-center text-center p-4 sm:p-6 lg:p-8">
          <div class="max-w-4xl space-y-4 sm:space-y-6">
            <span class="inline-block px-4 py-1.5 rounded-full bg-[#2A8FC2]/20 text-[#2A8FC2] text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.3em] backdrop-blur-md border border-[#2A8FC2]/40 shadow-lg">
              GLOBAL BLUE ZONES
            </span>
            <h2 class="text-3xl sm:text-6xl md:text-7xl font-black text-white tracking-tight leading-none drop-shadow-2xl">
              INSPIRED BY THE <span class="text-[#2A8FC2]">5 BLUE ZONES.</span>
            </h2>
            <p class="text-xs sm:text-lg md:text-xl text-[#E8DCC4] font-medium tracking-wide max-w-2xl mx-auto leading-relaxed">
              Unlocking the longevity principles observed across Okinawa, Sardinia, Nicoya, Ikaria, and Loma Linda.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 pt-2 sm:pt-4">
              <a href="#five-blue-zones" class="w-full sm:w-auto px-8 py-4 bg-[#2A8FC2] hover:bg-[#0A4F78] text-white text-xs font-black uppercase tracking-widest rounded-full transition-all shadow-xl hover:scale-105 btn-sheen text-center">
                EXPLORE REGIONS
              </a>
              <a href="{{ route('customer.shop') }}" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-black uppercase tracking-widest rounded-full backdrop-blur-md transition-all text-center">
                SHOP CATALOG
              </a>
            </div>
          </div>
        </div>

        <!-- Floating Regional Badge -->
        <div class="hidden lg:flex absolute bottom-24 sm:bottom-28 ltr:right-8 sm:ltr:right-12 rtl:left-8 sm:rtl:left-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#2A8FC2]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='#five-blue-zones'">
          <div class="w-12 h-12 rounded-xl overflow-hidden bg-white flex items-center justify-center">
            <img src="{{ asset('assets/images/okinawa.webp') }}" alt="Okinawa" onerror="this.onerror=null; this.src='{{ asset('assets/images/okinawa.jpg') }}';" width="48" height="48" loading="lazy" decoding="async" class="w-full h-full object-cover" />
          </div>
          <div class="text-left space-y-0.5">
            <span class="block text-[9px] font-mono font-bold text-[#2A8FC2] uppercase tracking-widest">CENTENARIAN HOTSPOT</span>
            <span class="block text-xs font-black text-white">OKINAWA, JAPAN</span>
            <span class="block text-[10px] text-[#67B34A] font-bold">Moai Social & Plant Diet</span>
          </div>
        </div>
      </div>

      <!-- Slide 5: Circadian Sleep & Recovery -->
      <div class="hero-slide absolute inset-0 w-full h-full opacity-0 pointer-events-none transition-opacity duration-1000 ease-in-out">
        <img src="{{ asset('assets/images/hero/hero-05.webp') }}" alt="Natural Restorative Sleep" onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-05.jpg') }}';" width="1920" height="1080" loading="lazy" decoding="async" class="w-full h-full object-cover filter brightness-[0.7]" />
        <div class="absolute inset-0 bg-gradient-to-t from-[#031827] via-[#031827]/40 to-transparent"></div>
        <div class="absolute inset-0 flex items-center justify-center text-center p-4 sm:p-6 lg:p-8">
          <div class="max-w-4xl space-y-4 sm:space-y-6">
            <span class="inline-block px-4 py-1.5 rounded-full bg-[#67B34A]/20 text-[#67B34A] text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.3em] backdrop-blur-md border border-[#67B34A]/40 shadow-lg">
              CIRCADIAN ALIGNMENT
            </span>
            <h2 class="text-3xl sm:text-6xl md:text-7xl font-black text-white tracking-tight leading-none drop-shadow-2xl">
              RESTORATIVE SLEEP & <span class="text-[#67B34A]">RECOVERY.</span>
            </h2>
            <p class="text-xs sm:text-lg md:text-xl text-[#E8DCC4] font-medium tracking-wide max-w-2xl mx-auto leading-relaxed">
              Natural nighttime neurological relaxation designed to promote deep slow-wave REM sleep cycles.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 pt-2 sm:pt-4">
              <a href="{{ route('customer.product.show', 'blue-rest') }}" class="w-full sm:w-auto px-8 py-4 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest rounded-full transition-all shadow-xl hover:scale-105 btn-sheen text-center">
                SHOP BLUE REST
              </a>
              <a href="{{ route('customer.products') }}" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-black uppercase tracking-widest rounded-full backdrop-blur-md transition-all text-center">
                BROWSE ALL
              </a>
            </div>
          </div>
        </div>

        <!-- Floating Product Badge -->
        <div class="hidden lg:flex absolute bottom-24 sm:bottom-28 ltr:right-8 sm:ltr:right-12 rtl:left-8 sm:rtl:left-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#67B34A]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='{{ route('customer.product.show', 'blue-rest') }}'">
          <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center">
            <img src="{{ asset('assets/products/blue-rest.webp') }}" alt="BLUE REST" onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-rest.jpg') }}';" width="48" height="48" loading="lazy" decoding="async" class="w-full h-full object-contain" />
          </div>
          <div class="text-left space-y-0.5">
            <span class="block text-[9px] font-mono font-bold text-[#67B34A] uppercase tracking-widest">DEEP SLEEP MATRIX</span>
            <span class="block text-xs font-black text-white">BLUE REST</span>
            <span class="block text-[10px] text-[#2A8FC2] font-bold">★ 4.9 Verified Reviews</span>
          </div>
        </div>
      </div>

      <!-- Navigation Arrows -->
      <button onclick="BLUEZONE_HERO.prev()" aria-label="Previous slide" class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-30 p-3 rounded-full bg-black/30 hover:bg-black/60 text-white backdrop-blur-md transition-all cursor-pointer border border-white/20 hover:scale-110">
        ‹
      </button>
      <button onclick="BLUEZONE_HERO.next()" aria-label="Next slide" class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-30 p-3 rounded-full bg-black/30 hover:bg-black/60 text-white backdrop-blur-md transition-all cursor-pointer border border-white/20 hover:scale-110">
        ›
      </button>

      <!-- Pagination Dots -->
      <div class="absolute bottom-6 sm:bottom-8 left-1/2 -translate-x-1/2 z-30 flex items-center space-x-1.5 sm:space-x-2">
        <button onclick="BLUEZONE_HERO.goTo(0)" class="hero-dot p-2 inline-flex items-center justify-center cursor-pointer min-w-[32px] min-h-[32px]" aria-label="Go to slide 1">
          <span class="hero-dot-indicator w-8 h-2 rounded-full bg-[#67B34A] transition-all duration-300 block pointer-events-none"></span>
        </button>
        <button onclick="BLUEZONE_HERO.goTo(1)" class="hero-dot p-2 inline-flex items-center justify-center cursor-pointer min-w-[32px] min-h-[32px]" aria-label="Go to slide 2">
          <span class="hero-dot-indicator w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 block pointer-events-none"></span>
        </button>
        <button onclick="BLUEZONE_HERO.goTo(2)" class="hero-dot p-2 inline-flex items-center justify-center cursor-pointer min-w-[32px] min-h-[32px]" aria-label="Go to slide 3">
          <span class="hero-dot-indicator w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 block pointer-events-none"></span>
        </button>
        <button onclick="BLUEZONE_HERO.goTo(3)" class="hero-dot p-2 inline-flex items-center justify-center cursor-pointer min-w-[32px] min-h-[32px]" aria-label="Go to slide 4">
          <span class="hero-dot-indicator w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 block pointer-events-none"></span>
        </button>
        <button onclick="BLUEZONE_HERO.goTo(4)" class="hero-dot p-2 inline-flex items-center justify-center cursor-pointer min-w-[32px] min-h-[32px]" aria-label="Go to slide 5">
          <span class="hero-dot-indicator w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 block pointer-events-none"></span>
        </button>
      </div>
    </section>
