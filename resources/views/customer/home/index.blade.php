<x-layouts.customer :title="__('app.brand_name') . ' — Cellular Longevity & Botanical Medicine'">
    <!-- 02. HERO SLIDER (5 FULL-WIDTH LIFESTYLE & PRODUCT SHOWCASE SLIDES) -->
    <section id="hero-slider-container" class="relative w-full h-[85vh] min-h-[580px] bg-[#031827] overflow-hidden select-none group">
      
      <!-- Slide 1: Okinawa & Centenarian Wisdom -->
      <div class="hero-slide absolute inset-0 w-full h-full opacity-100 transition-opacity duration-1000 ease-in-out z-10">
        <img src="{{ asset('assets/images/hero/hero-01.jpg') }}" alt="Blue Zone Longevity" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover filter brightness-[0.7]" />
        <div class="absolute inset-0 bg-gradient-to-t from-[#031827] via-[#031827]/40 to-transparent"></div>
        <div class="absolute inset-0 flex items-center justify-center text-center p-4 sm:p-6 lg:p-8">
          <div class="max-w-4xl space-y-4 sm:space-y-6">
            <span class="inline-block px-4 py-1.5 rounded-full bg-[#67B34A]/20 text-[#67B34A] text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.3em] backdrop-blur-md border border-[#67B34A]/40 shadow-lg">
              CENTENARIAN WISDOM
            </span>
            <h1 class="text-3xl sm:text-6xl md:text-7xl font-black text-white tracking-tight leading-none drop-shadow-2xl">
              LIVE LONG. <span class="text-[#67B34A]">LIVE WELL.</span>
            </h1>
            <p class="text-xs sm:text-lg md:text-xl text-[#E8DCC4] font-medium tracking-wide max-w-2xl mx-auto leading-relaxed">
              Translating the lifestyle, diet, and biological resilience of the world’s 5 longest-lived communities into modern wellness formulations.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 pt-2 sm:pt-4">
              <a href="#who-we-are" class="w-full sm:w-auto px-8 py-4 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest rounded-full transition-all shadow-xl hover:scale-105 btn-sheen text-center">
                DISCOVER OUR STORY
              </a>
              <a href="{{ route('customer.products') }}" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-black uppercase tracking-widest rounded-full backdrop-blur-md transition-all text-center">
                EXPLORE PRODUCTS
              </a>
            </div>
          </div>
        </div>

        <!-- Floating Product Badge -->
        <div class="hidden lg:flex absolute bottom-12 right-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#67B34A]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='{{ route('customer.product.show', 'blue-mind') }}'">
          <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center">
            <img src="{{ asset('assets/products/blue-mind.jpg') }}" alt="BLUE MIND" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-contain" />
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
        <img src="{{ asset('assets/images/hero/hero-02.jpg') }}" alt="Scientific Formulation" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover filter brightness-[0.7]" />
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
        <div class="hidden lg:flex absolute bottom-12 right-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#2A8FC2]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='{{ route('customer.product.show', 'blue-energy') }}'">
          <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center">
            <img src="{{ asset('assets/products/blue-energy.jpg') }}" alt="BLUE ENERGY" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-contain" />
          </div>
          <div class="text-left space-y-0.5">
            <span class="block text-[9px] font-mono font-bold text-[#2A8FC2] uppercase tracking-widest">MITOCHONDRIAL ATP</span>
            <span class="block text-xs font-black text-white">BLUE ENERGY</span>
            <span class="block text-[10px] text-[#67B34A] font-bold">★ 4.8 Verified Reviews</span>
          </div>
        </div>
      </div>

      <!-- Slide 3: 6 Pillars & Daily Movement -->
      <div class="hero-slide absolute inset-0 w-full h-full opacity-0 pointer-events-none transition-opacity duration-1000 ease-in-out">
        <img src="{{ asset('assets/images/hero/hero-03.jpg') }}" alt="Centenarian Lifestyle" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover filter brightness-[0.7]" />
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
        <div class="hidden lg:flex absolute bottom-12 right-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#67B34A]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='{{ route('customer.product.show', 'blue-flex') }}'">
          <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center">
            <img src="{{ asset('assets/products/blue-flex.jpg') }}" alt="BLUE FLEX" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-contain" />
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
        <img src="{{ asset('assets/images/hero/hero-04.jpg') }}" alt="Mediterranean Blue Zone" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover filter brightness-[0.7]" />
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
        <div class="hidden lg:flex absolute bottom-12 right-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#2A8FC2]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='#five-blue-zones'">
          <div class="w-12 h-12 rounded-xl overflow-hidden bg-white flex items-center justify-center">
            <img src="{{ asset('assets/images/okinawa.jpg') }}" alt="Okinawa" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
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
        <img src="{{ asset('assets/images/hero/hero-05.jpg') }}" alt="Natural Restorative Sleep" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover filter brightness-[0.7]" />
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
        <div class="hidden lg:flex absolute bottom-12 right-12 z-20 p-4 rounded-2xl bg-[#031827]/85 border border-[#67B34A]/40 backdrop-blur-md items-center gap-4 text-white shadow-2xl hover:scale-105 transition-transform cursor-pointer" onclick="window.location.href='{{ route('customer.product.show', 'blue-rest') }}'">
          <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center">
            <img src="{{ asset('assets/products/blue-rest.jpg') }}" alt="BLUE REST" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-contain" />
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
      <div class="absolute bottom-6 sm:bottom-8 left-1/2 -translate-x-1/2 z-30 flex items-center space-x-2.5">
        <button onclick="BLUEZONE_HERO.goTo(0)" class="hero-dot w-8 h-2 rounded-full bg-[#67B34A] transition-all duration-300 cursor-pointer" aria-label="Go to slide 1"></button>
        <button onclick="BLUEZONE_HERO.goTo(1)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 cursor-pointer" aria-label="Go to slide 2"></button>
        <button onclick="BLUEZONE_HERO.goTo(2)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 cursor-pointer" aria-label="Go to slide 3"></button>
        <button onclick="BLUEZONE_HERO.goTo(3)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 cursor-pointer" aria-label="Go to slide 4"></button>
        <button onclick="BLUEZONE_HERO.goTo(4)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 cursor-pointer" aria-label="Go to slide 5"></button>
      </div>
    </section>

    <!-- 03. WHO WE ARE -->
    <section id="who-we-are" class="py-24 bg-[#F6F5EF] dark:bg-[#031827] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          <!-- Image Column with Dynamic Nodes -->
          <div class="lg:col-span-6 relative">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-[#0A4F78]/20 group">
              <img src="{{ asset('assets/images/story-lifestyle.jpg') }}" alt="Blue Zone Centenarian Lifestyle" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-[460px] object-cover group-hover:scale-105 transition-transform duration-700" />
              <div class="absolute inset-0 bg-gradient-to-t from-[#031827]/80 via-transparent to-transparent"></div>
              
              <!-- Floating Overlay Stats Card -->
              <div class="absolute bottom-6 left-6 right-6 p-6 rounded-2xl bg-[#031827]/85 backdrop-blur-md border border-[#2A8FC2]/40 text-white space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#2A8FC2]">RESEARCH DOSSIER</span>
                  <span class="w-2 h-2 rounded-full bg-[#67B34A] animate-pulse"></span>
                </div>
                <p class="text-sm font-bold text-[#E8DCC4] leading-relaxed">
                  "Translating centenarian longevity wisdom into bio-engineered botanical supplements."
                </p>
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-[#0A4F78]/40 text-center">
                  <div>
                    <span class="block text-lg font-black text-[#2A8FC2]">100+</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">Centenarians</span>
                  </div>
                  <div>
                    <span class="block text-lg font-black text-[#2A8FC2]">5</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">Blue Zones</span>
                  </div>
                  <div>
                    <span class="block text-lg font-black text-[#67B34A]">100%</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">Bio-Identical</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Editorial Content Column -->
          <div class="lg:col-span-6 space-y-6">
            <span class="inline-block text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">WHO IS BLUE ZONE?</span>
            <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight leading-tight">
              INSPIRED BY THE WORLD'S LONGEST-LIVED.
            </h2>
            <div class="w-16 h-1.5 bg-[#0A4F78] dark:bg-[#2A8FC2] rounded-full"></div>
            
            <p class="text-base text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed">
              BLUE ZONE draws inspiration from the world's five Blue Zones and translates their lifestyle wisdom into modern wellness and science-backed formulations.
            </p>
            
            <div class="space-y-3 pt-2">
              <div class="flex items-start gap-3">
                <span class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">Standardized cellular polyphenol compounds extracted at peak biological potency.</p>
              </div>
              <div class="flex items-start gap-3">
                <span class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">Clean dietary bio-integrity—zero artificial fillers, binders, or synthetic additives.</p>
              </div>
              <div class="flex items-start gap-3">
                <span class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">✓</span>
                <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">Bio-identical nutrition formulated to support stamina, brain health, and sleep.</p>
              </div>
            </div>

            <div class="pt-4 flex flex-wrap gap-4">
              <a href="#philosophy" class="px-7 py-3.5 bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md btn-sheen">
                OUR PHILOSOPHY →
              </a>
              <a href="{{ route('customer.pages.science') }}" class="px-7 py-3.5 border border-[#0A4F78]/30 hover:border-[#0A4F78] text-[#031827] dark:text-[#F6F5EF] text-xs font-extrabold uppercase tracking-widest rounded-xl transition-colors">
                OUR SCIENCE
              </a>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- 04. WHAT WE BELIEVE (THE 6 PILLARS) -->
    <section id="philosophy" class="py-20 bg-white dark:bg-[#062B49] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-2xl mx-auto space-y-3">
          <span class="text-[11px] font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
            OUR WELLNESS PHILOSOPHY
          </span>
          <h2 class="text-3xl sm:text-5xl font-light text-[#031827] dark:text-[#F6F5EF] tracking-tight">
            THE 6 PILLARS OF <span class="font-bold text-[#67B34A]">LONGEVITY</span>
          </h2>
          <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium max-w-lg mx-auto leading-relaxed">
            Every BLUE ZONE formulation is inspired by the core lifestyle principles shared by the world's longest-lived communities.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center min-h-[520px]">
          <!-- Column 1: Pillar Selectors -->
          <div class="lg:col-span-4 space-y-2">
            <div class="lg:hidden flex overflow-x-auto gap-2 pb-2 scrollbar-none border-b border-[#0A4F78]/15">
              <button onclick="BLUEZONE_PILLARS.select(0)" class="pillar-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#67B34A] text-white" data-index="0">01 MOVEMENT</button>
              <button onclick="BLUEZONE_PILLARS.select(1)" class="pillar-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="1">02 NUTRITION</button>
              <button onclick="BLUEZONE_PILLARS.select(2)" class="pillar-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="2">03 PURPOSE</button>
              <button onclick="BLUEZONE_PILLARS.select(3)" class="pillar-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="3">04 COMMUNITY</button>
              <button onclick="BLUEZONE_PILLARS.select(4)" class="pillar-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="4">05 REST</button>
              <button onclick="BLUEZONE_PILLARS.select(5)" class="pillar-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="5">06 WELLNESS</button>
            </div>

            <div class="hidden lg:block divide-y divide-[#0A4F78]/15 dark:divide-[#0A4F78]/30 border-y border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
              <button onclick="BLUEZONE_PILLARS.select(0)" onmouseenter="BLUEZONE_PILLARS.select(0)" class="pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-[#67B34A] bg-[#67B34A]/5" data-index="0">
                <span class="text-xs font-extrabold text-[#67B34A] font-mono">01</span>
                <span class="text-sm font-bold text-[#67B34A] tracking-wider uppercase">MOVEMENT</span>
              </button>
              <button onclick="BLUEZONE_PILLARS.select(1)" onmouseenter="BLUEZONE_PILLARS.select(1)" class="pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="1">
                <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">02</span>
                <span class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">NUTRITION</span>
              </button>
              <button onclick="BLUEZONE_PILLARS.select(2)" onmouseenter="BLUEZONE_PILLARS.select(2)" class="pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="2">
                <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">03</span>
                <span class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">PURPOSE</span>
              </button>
              <button onclick="BLUEZONE_PILLARS.select(3)" onmouseenter="BLUEZONE_PILLARS.select(3)" class="pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="3">
                <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">04</span>
                <span class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">COMMUNITY</span>
              </button>
              <button onclick="BLUEZONE_PILLARS.select(4)" onmouseenter="BLUEZONE_PILLARS.select(4)" class="pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="4">
                <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">05</span>
                <span class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">REST</span>
              </button>
              <button onclick="BLUEZONE_PILLARS.select(5)" onmouseenter="BLUEZONE_PILLARS.select(5)" class="pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="5">
                <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">06</span>
                <span class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">WELLNESS</span>
              </button>
            </div>
          </div>

          <!-- Column 2: Orbital SVG Animation -->
          <div class="hidden lg:flex lg:col-span-4 items-center justify-center relative py-4">
            <div class="w-72 h-72 relative flex items-center justify-center">
              <svg class="w-full h-full" viewBox="0 0 300 300" fill="none">
                <circle cx="150" cy="150" r="110" stroke="#0A4F78" stroke-width="1.5" stroke-opacity="0.25" stroke-dasharray="4 4" />
                <circle cx="150" cy="150" r="70" stroke="#2A8FC2" stroke-width="1" stroke-opacity="0.2" />

                <line id="spoke-0" x1="150" y1="150" x2="150" y2="40" stroke="#67B34A" stroke-width="2" opacity="0.9" />
                <line id="spoke-1" x1="150" y1="150" x2="245" y2="95" stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                <line id="spoke-2" x1="150" y1="150" x2="245" y2="205" stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                <line id="spoke-3" x1="150" y1="150" x2="150" y2="260" stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                <line id="spoke-4" x1="150" y1="150" x2="55" y2="205" stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                <line id="spoke-5" x1="150" y1="150" x2="55" y2="95" stroke="#2A8FC2" stroke-width="1" opacity="0.3" />

                <circle id="node-0" cx="150" cy="40" r="10" fill="#67B34A" stroke="#FFFFFF" stroke-width="2" class="transition-all duration-300" />
                <circle id="node-1" cx="245" cy="95" r="7" fill="#0A4F78" stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                <circle id="node-2" cx="245" cy="205" r="7" fill="#0A4F78" stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                <circle id="node-3" cx="150" cy="260" r="7" fill="#0A4F78" stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                <circle id="node-4" cx="55" cy="205" r="7" fill="#0A4F78" stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                <circle id="node-5" cx="55" cy="95" r="7" fill="#0A4F78" stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
              </svg>

              <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 rounded-full bg-[#031827] border-2 border-[#2A8FC2] flex flex-col items-center justify-center p-2 shadow-xl">
                  <span class="text-[9px] font-black tracking-widest text-[#2A8FC2] uppercase">BLUE ZONE</span>
                  <span class="text-[8px] font-bold text-[#E8DCC4] uppercase tracking-wider mt-0.5">CORE</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Column 3: Active Pillar Content Display Panel -->
          <div class="lg:col-span-4 p-6 sm:p-8 rounded-3xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 shadow-xl transition-all duration-500 relative min-h-[420px] flex flex-col justify-between" id="pillar-content-panel">
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span id="pillar-active-num" class="text-4xl font-light text-[#67B34A] font-mono">01</span>
                <span id="pillar-active-icon-box" class="w-10 h-10 rounded-xl bg-[#67B34A]/15 text-[#67B34A] flex items-center justify-center">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
              </div>

              <div class="w-full h-36 rounded-2xl overflow-hidden relative border border-[#0A4F78]/20 shadow-md group">
                <img id="pillar-active-img" src="{{ asset('assets/images/hero/hero-03.jpg') }}" alt="Pillar Visual" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#031827]/60 via-transparent to-transparent"></div>
              </div>

              <div class="space-y-1.5">
                <h3 id="pillar-active-title" class="text-2xl font-bold text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                  MOVEMENT
                </h3>
                <p id="pillar-active-desc" class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium leading-relaxed">
                  Natural daily movement is one of the defining habits shared by centenarians, supporting joint stamina, circulation, and fluid mobility throughout life.
                </p>
              </div>
            </div>

            <div class="pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 space-y-1.5">
              <span class="block text-[10px] font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">PHYSIOLOGICAL IMPACT</span>
              <div id="pillar-active-tag" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#67B34A]/15 text-[#67B34A] text-xs font-bold">
                <span>Joint Stamina & Fluid Articulation</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 06. FEATURED PRODUCTS SLIDER -->
    <section id="featured-products" class="py-24 bg-white dark:bg-[#062B49] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
          <div class="space-y-3">
            <span class="text-xs font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">CLINICAL FORMULATIONS</span>
            <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
              FEATURED PRODUCTS
            </h2>
          </div>
          
          <div class="flex items-center gap-3">
            <button onclick="if(window.BLUEZONE_PRODUCT_SLIDER){BLUEZONE_PRODUCT_SLIDER.prev();}" aria-label="Previous products" class="p-3.5 rounded-full bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 hover:bg-[#0A4F78] hover:text-white text-[#0A4F78] dark:text-[#2A8FC2] transition-colors cursor-pointer">
              ‹
            </button>
            <button onclick="if(window.BLUEZONE_PRODUCT_SLIDER){BLUEZONE_PRODUCT_SLIDER.next();}" aria-label="Next products" class="p-3.5 rounded-full bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 hover:bg-[#0A4F78] hover:text-white text-[#0A4F78] dark:text-[#2A8FC2] transition-colors cursor-pointer">
              ›
            </button>
          </div>
        </div>

        <div id="featured-products-container" class="relative overflow-hidden">
          <div id="featured-products-track" class="flex transition-transform duration-500 ease-out">
            <!-- Dynamically populated or rendered -->
          </div>
        </div>

        <div id="featured-products-dots" class="flex justify-center items-center gap-2 pt-4"></div>
      </div>
    </section>

    <!-- 07. BLUE MIND FLAGSHIP PRODUCT HERO -->
    <section id="blue-mind-flagship" class="py-24 bg-[#031827] text-white border-b border-[#0A4F78]/30 relative overflow-hidden">
      <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#2A8FC2]/20 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#0A4F78]/30 rounded-full blur-3xl pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          <div class="lg:col-span-6 relative flex justify-center">
            <div class="relative w-full max-w-md aspect-square bg-[#062B49]/80 rounded-3xl border border-[#2A8FC2]/40 p-8 shadow-2xl flex items-center justify-center group overflow-hidden">
              <svg class="absolute inset-0 w-full h-full opacity-30" viewBox="0 0 400 400" fill="none">
                <circle cx="200" cy="200" r="160" stroke="#2A8FC2" stroke-width="1" stroke-dasharray="4 4"/>
                <circle cx="200" cy="200" r="110" stroke="#2A8FC2" stroke-width="1" opacity="0.6"/>
                <line x1="40" y1="200" x2="360" y2="200" stroke="#2A8FC2" stroke-width="0.5"/>
                <line x1="200" y1="40" x2="200" y2="360" stroke="#2A8FC2" stroke-width="0.5"/>
              </svg>
              
              <img
                src="{{ asset('assets/products/blue-mind.jpg') }}"
                alt="BLUE MIND Flagship Nootropic"
                onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';"
                class="w-4/5 h-4/5 object-contain relative z-10 group-hover:scale-108 transition-transform duration-700"
              />

              <div class="absolute top-6 left-6 px-3 py-1.5 rounded-xl bg-[#031827]/90 border border-[#2A8FC2]/50 text-[10px] font-black uppercase tracking-wider text-[#2A8FC2] backdrop-blur-md">
                ⚡ Synaptic Speed
              </div>
              <div class="absolute bottom-6 right-6 px-3 py-1.5 rounded-xl bg-[#031827]/90 border border-[#67B34A]/50 text-[10px] font-black uppercase tracking-wider text-[#67B34A] backdrop-blur-md">
                🌿 Zero Caffeine Jitters
              </div>
            </div>
          </div>

          <div class="lg:col-span-6 space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#2A8FC2]/20 border border-[#2A8FC2]/40 text-[#2A8FC2] text-xs font-black uppercase tracking-[0.25em]">
              FLAGSHIP FORMULATION
            </div>

            <h2 class="text-4xl sm:text-6xl font-black tracking-tight leading-none text-white">
              BLUE MIND
            </h2>
            <p class="text-lg text-[#2A8FC2] font-bold tracking-wide">
              SCIENCE FOR A CLEARER MIND.
            </p>

            <p class="text-sm text-[#E8DCC4] font-medium leading-relaxed">
              BLUE MIND is our flagship cognitive bio-hacker, bio-engineered from standardized Bacopa Monnieri, L-Theanine, and adaptogenic Rhodiola Rosea to sustain daily mental focus without neurological burnout.
            </p>

            <div class="space-y-3 pt-2">
              <span class="block text-xs font-black uppercase tracking-widest text-[#7EA5B8]">ACTIVE STANDARDIZED BOTANICALS:</span>
              <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1.5 rounded-lg bg-[#062B49] border border-[#2A8FC2]/30 text-xs font-bold text-[#F6F5EF]">Bacopa Monnieri (50% Bacosides)</span>
                <span class="px-3 py-1.5 rounded-lg bg-[#062B49] border border-[#2A8FC2]/30 text-xs font-bold text-[#F6F5EF]">L-Theanine (200mg Pure)</span>
                <span class="px-3 py-1.5 rounded-lg bg-[#062B49] border border-[#2A8FC2]/30 text-xs font-bold text-[#F6F5EF]">Rhodiola Rosea (3% Rosavins)</span>
                <span class="px-3 py-1.5 rounded-lg bg-[#062B49] border border-[#2A8FC2]/30 text-xs font-bold text-[#F6F5EF]">Ginkgo Biloba Extract</span>
              </div>
            </div>

            <div class="pt-6 border-t border-[#0A4F78]/50 flex flex-wrap items-center gap-6">
              <div>
                <span class="block text-[10px] font-bold uppercase tracking-widest text-[#7EA5B8]">60 CAPSULES • 30-DAY SUPPLY</span>
                <span class="text-3xl font-black text-[#2A8FC2]">$64.00</span>
              </div>
              <div class="flex flex-wrap gap-3">
                <button onclick="if(window.BLUEZONE_CART){BLUEZONE_CART.add('blue-mind', 1);}" class="px-8 py-4 bg-[#2A8FC2] hover:bg-[#0A4F78] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl btn-sheen cursor-pointer">
                  ADD TO CART
                </button>
                <a href="{{ route('customer.product.show', 'blue-mind') }}" class="px-6 py-4 border border-[#2A8FC2]/40 hover:border-[#2A8FC2] text-white text-xs font-extrabold uppercase tracking-widest rounded-xl transition-colors">
                  EXPLORE CLINICAL STUDY →
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 08. THE FIVE BLUE ZONES SHOWCASE -->
    <section id="five-blue-zones" class="py-20 bg-[#F6F5EF] dark:bg-[#031827] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-2xl mx-auto space-y-3">
          <span class="text-[11px] font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
            WHERE LONGEVITY BEGINS
          </span>
          <h2 class="text-3xl sm:text-5xl font-light text-[#031827] dark:text-[#F6F5EF] tracking-tight">
            THE WORLD'S <span class="font-bold text-[#67B34A]">LONGEST-LIVED</span>
          </h2>
          <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
            Five regions. Five ways of living. One philosophy of longevity.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center" id="bz-region-interactive-container">
          <!-- Column 1: Main Large Visual Showcase -->
          <div class="lg:col-span-8 relative">
            <div class="w-full h-[420px] sm:h-[520px] rounded-3xl overflow-hidden relative shadow-2xl border border-[#0A4F78]/20 group">
              <img
                id="bz-region-active-img"
                src="{{ asset('assets/images/okinawa.jpg') }}"
                alt="Okinawa Blue Zone"
                onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';"
                class="w-full h-full object-cover transition-all duration-700 ease-out group-hover:scale-105"
              />

              <div class="absolute inset-0 bg-gradient-to-t from-[#031827] via-[#031827]/40 to-transparent"></div>

              <div class="absolute top-6 right-6 px-4 py-2 rounded-2xl bg-[#031827]/85 border border-[#67B34A]/50 text-white text-xs font-extrabold uppercase tracking-wider backdrop-blur-md shadow-lg flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#67B34A] animate-pulse"></span>
                <span class="text-white/60 text-[10px]">MARKER:</span>
                <span id="bz-region-active-marker" class="text-[#67B34A]">COGNITION</span>
              </div>

              <div class="absolute bottom-8 left-6 sm:left-10 right-6 sm:right-10 space-y-3 text-white transition-opacity duration-500" id="bz-region-text-panel">
                <div class="flex items-center gap-3">
                  <span id="bz-region-active-num" class="text-sm font-extrabold font-mono text-[#67B34A] tracking-wider">01</span>
                  <span id="bz-region-active-country" class="text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full bg-[#0A4F78]/80 text-[#F6F5EF] border border-[#2A8FC2]/40 backdrop-blur-sm">JAPAN</span>
                </div>
                <h3 id="bz-region-active-title" class="text-3xl sm:text-5xl font-bold tracking-tight text-white">
                  OKINAWA
                </h3>
                <p id="bz-region-active-desc" class="text-xs sm:text-sm text-[#F6F5EF]/85 font-medium max-w-xl leading-relaxed">
                  Moai social circles, daily natural movement, and a plant-rich traditional diet abundant in turmeric and purple sweet potatoes.
                </p>
              </div>

              <div class="absolute bottom-8 right-6 sm:right-10 text-xs font-mono font-bold text-white/70 hidden sm:block">
                <span id="bz-region-curr-step" class="text-[#67B34A]">01</span> / 05
              </div>
            </div>
          </div>

          <!-- Column 2: List Selector -->
          <div class="lg:col-span-4 space-y-2">
            <div class="lg:hidden flex overflow-x-auto gap-2 pb-2 scrollbar-none border-b border-[#0A4F78]/15">
              <button onclick="BLUEZONE_REGIONS.select(0)" class="region-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#67B34A] text-white" data-index="0">01 OKINAWA</button>
              <button onclick="BLUEZONE_REGIONS.select(1)" class="region-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="1">02 SARDINIA</button>
              <button onclick="BLUEZONE_REGIONS.select(2)" class="region-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="2">03 NICOYA</button>
              <button onclick="BLUEZONE_REGIONS.select(3)" class="region-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="3">04 IKARIA</button>
              <button onclick="BLUEZONE_REGIONS.select(4)" class="region-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]" data-index="4">05 LOMA LINDA</button>
            </div>

            <div class="hidden lg:block divide-y divide-[#0A4F78]/15 dark:divide-[#0A4F78]/30 border-y border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
              <button onclick="BLUEZONE_REGIONS.select(0)" onmouseenter="BLUEZONE_REGIONS.select(0)" class="region-desktop-btn w-full py-4 px-4 flex items-center justify-between text-left transition-all duration-300 group cursor-pointer border-l-4 border-[#67B34A] bg-[#67B34A]/10" data-index="0">
                <div class="flex items-center gap-4">
                  <span class="text-xs font-extrabold text-[#67B34A] font-mono">01</span>
                  <div>
                    <h4 class="text-sm font-bold text-[#67B34A] tracking-wider uppercase">OKINAWA</h4>
                    <span class="text-[10px] font-semibold text-[#031827]/50 dark:text-[#F6F5EF]/50 uppercase tracking-widest">JAPAN</span>
                  </div>
                </div>
                <span class="text-xs font-extrabold text-[#67B34A]">→</span>
              </button>
              <button onclick="BLUEZONE_REGIONS.select(1)" onmouseenter="BLUEZONE_REGIONS.select(1)" class="region-desktop-btn w-full py-4 px-4 flex items-center justify-between text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="1">
                <div class="flex items-center gap-4">
                  <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">02</span>
                  <div>
                    <h4 class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">SARDINIA</h4>
                    <span class="text-[10px] font-semibold text-[#031827]/50 dark:text-[#F6F5EF]/50 uppercase tracking-widest">ITALY</span>
                  </div>
                </div>
                <span class="text-xs font-bold text-[#031827]/30 dark:text-[#F6F5EF]/30 group-hover:text-[#2A8FC2]">→</span>
              </button>
              <button onclick="BLUEZONE_REGIONS.select(2)" onmouseenter="BLUEZONE_REGIONS.select(2)" class="region-desktop-btn w-full py-4 px-4 flex items-center justify-between text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="2">
                <div class="flex items-center gap-4">
                  <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">03</span>
                  <div>
                    <h4 class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">NICOYA</h4>
                    <span class="text-[10px] font-semibold text-[#031827]/50 dark:text-[#F6F5EF]/50 uppercase tracking-widest">COSTA RICA</span>
                  </div>
                </div>
                <span class="text-xs font-bold text-[#031827]/30 dark:text-[#F6F5EF]/30 group-hover:text-[#2A8FC2]">→</span>
              </button>
              <button onclick="BLUEZONE_REGIONS.select(3)" onmouseenter="BLUEZONE_REGIONS.select(3)" class="region-desktop-btn w-full py-4 px-4 flex items-center justify-between text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="3">
                <div class="flex items-center gap-4">
                  <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">04</span>
                  <div>
                    <h4 class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">IKARIA</h4>
                    <span class="text-[10px] font-semibold text-[#031827]/50 dark:text-[#F6F5EF]/50 uppercase tracking-widest">GREECE</span>
                  </div>
                </div>
                <span class="text-xs font-bold text-[#031827]/30 dark:text-[#F6F5EF]/30 group-hover:text-[#2A8FC2]">→</span>
              </button>
              <button onclick="BLUEZONE_REGIONS.select(4)" onmouseenter="BLUEZONE_REGIONS.select(4)" class="region-desktop-btn w-full py-4 px-4 flex items-center justify-between text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5" data-index="4">
                <div class="flex items-center gap-4">
                  <span class="text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">05</span>
                  <div>
                    <h4 class="text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]">LOMA LINDA</h4>
                    <span class="text-[10px] font-semibold text-[#031827]/50 dark:text-[#F6F5EF]/50 uppercase tracking-widest">USA</span>
                  </div>
                </div>
                <span class="text-xs font-bold text-[#031827]/30 dark:text-[#F6F5EF]/30 group-hover:text-[#2A8FC2]">→</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 09. OUR SCIENCE (4-STAGE JOURNEY) -->
    <section id="our-science" class="py-20 bg-white dark:bg-[#062B49] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-2xl mx-auto space-y-3">
          <span class="text-[11px] font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
            OUR SCIENCE
          </span>
          <h2 class="text-3xl sm:text-5xl font-light text-[#031827] dark:text-[#F6F5EF] tracking-tight">
            FROM NATURE TO <span class="font-bold text-[#67B34A]">WELLNESS</span>.
          </h2>
          <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
            Inspired by the wisdom of longevity. Refined through modern formulation.
          </p>
        </div>

        <div class="relative py-4">
          <div class="hidden md:block absolute top-1/2 left-[10%] right-[10%] h-0.5 bg-[#0A4F78]/20 dark:bg-[#0A4F78]/40 -translate-y-1/2 z-0"></div>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 relative z-10" id="bz-science-nodes">
            <button onclick="BLUEZONE_SCIENCE.select(0)" onmouseenter="BLUEZONE_SCIENCE.select(0)" class="bz-science-node-btn p-4 rounded-2xl bg-[#67B34A]/10 border-2 border-[#67B34A] text-left transition-all duration-300 group cursor-pointer flex flex-col items-center md:items-start gap-1.5" data-index="0">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center font-mono text-xs font-bold shadow-md">01</span>
                <span class="text-xs font-bold text-[#67B34A] uppercase tracking-wider">SOURCE</span>
              </div>
              <span class="text-[10px] font-semibold text-[#031827]/60 dark:text-[#F6F5EF]/60 hidden md:block">FROM NATURE</span>
            </button>
            <button onclick="BLUEZONE_SCIENCE.select(1)" onmouseenter="BLUEZONE_SCIENCE.select(1)" class="bz-science-node-btn p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border-2 border-[#0A4F78]/15 hover:border-[#2A8FC2]/50 text-left transition-all duration-300 group cursor-pointer flex flex-col items-center md:items-start gap-1.5" data-index="1">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] flex items-center justify-center font-mono text-xs font-bold">02</span>
                <span class="text-xs font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 uppercase tracking-wider group-hover:text-[#2A8FC2]">FORMULATION</span>
              </div>
              <span class="text-[10px] font-semibold text-[#031827]/50 dark:text-[#F6F5EF]/50 hidden md:block">BIO-PRECISION</span>
            </button>
            <button onclick="BLUEZONE_SCIENCE.select(2)" onmouseenter="BLUEZONE_SCIENCE.select(2)" class="bz-science-node-btn p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border-2 border-[#0A4F78]/15 hover:border-[#2A8FC2]/50 text-left transition-all duration-300 group cursor-pointer flex flex-col items-center md:items-start gap-1.5" data-index="2">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] flex items-center justify-center font-mono text-xs font-bold">03</span>
                <span class="text-xs font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 uppercase tracking-wider group-hover:text-[#2A8FC2]">VALIDATION</span>
              </div>
              <span class="text-[10px] font-semibold text-[#031827]/50 dark:text-[#F6F5EF]/50 hidden md:block">LAB QUALITY</span>
            </button>
            <button onclick="BLUEZONE_SCIENCE.select(3)" onmouseenter="BLUEZONE_SCIENCE.select(3)" class="bz-science-node-btn p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border-2 border-[#0A4F78]/15 hover:border-[#2A8FC2]/50 text-left transition-all duration-300 group cursor-pointer flex flex-col items-center md:items-start gap-1.5" data-index="3">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] flex items-center justify-center font-mono text-xs font-bold">04</span>
                <span class="text-xs font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 uppercase tracking-wider group-hover:text-[#2A8FC2]">WELLNESS</span>
              </div>
              <span class="text-[10px] font-semibold text-[#031827]/50 dark:text-[#F6F5EF]/50 hidden md:block">DAILY VITALITY</span>
            </button>
          </div>
        </div>

        <div class="p-8 sm:p-10 rounded-3xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 shadow-xl transition-all duration-500 min-h-[380px]" id="bz-science-panel">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-5 relative">
              <div class="w-full h-64 sm:h-80 rounded-2xl overflow-hidden relative shadow-md border border-[#0A4F78]/20 bg-[#062B49] group">
                <img
                  id="bz-science-active-img"
                  src="{{ asset('assets/images/hero_longevity.jpg') }}"
                  alt="Science Source Sourcing"
                  onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';"
                  class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-[#031827]/80 via-transparent to-transparent"></div>
                <div class="absolute top-4 left-4 px-3 py-1 rounded-full bg-[#031827]/80 text-[#67B34A] text-[10px] font-mono font-bold border border-[#67B34A]/40 backdrop-blur-sm">
                  STAGE <span id="bz-science-stage-code">01/04</span>
                </div>
              </div>
            </div>

            <div class="lg:col-span-7 space-y-6">
              <div class="space-y-2">
                <div class="flex items-center gap-3">
                  <span id="bz-science-active-num" class="text-3xl font-light font-mono text-[#67B34A]">01</span>
                  <span class="text-xs font-bold text-[#67B34A]">—</span>
                  <span id="bz-science-active-stage" class="text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">SOURCE</span>
                </div>
                <h3 id="bz-science-active-title" class="text-2xl sm:text-4xl font-bold text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                  FROM NATURE
                </h3>
              </div>

              <p id="bz-science-active-desc" class="text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed">
                Selected botanical and nutritional ingredients inspired by the natural foundations of centenarian longevity across Okinawa, Sardinia, and Nicoya.
              </p>

              <div class="pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 space-y-2">
                <span class="block text-[10px] font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">KEY HIGHLIGHTS</span>
                <div id="bz-science-active-chips" class="flex flex-wrap gap-2">
                  <span class="px-3 py-1.5 rounded-lg bg-[#67B34A]/15 text-[#67B34A] text-xs font-bold">Standardized Botanical Extraction</span>
                  <span class="px-3 py-1.5 rounded-lg bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 text-[#031827] dark:text-[#F6F5EF] text-xs font-bold">Peak Potency Sourcing</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 11. BLUE ZONE JOURNAL -->
    <section id="blue-zone-journal" class="py-24 bg-white dark:bg-[#062B49] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <div class="text-center max-w-3xl mx-auto space-y-4">
          <span class="text-xs font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">RESEARCH & INSIGHTS</span>
          <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
            THE BLUE ZONE JOURNAL
          </h2>
          <p class="text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">
            Explore whitepapers, clinical breakthroughs, and longevity habits written by our medical advisory board.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          <div class="lg:col-span-7 bg-[#F6F5EF] dark:bg-[#031827] rounded-3xl border border-[#0A4F78]/15 overflow-hidden shadow-sm hover:shadow-2xl transition-all card-hover-lift flex flex-col justify-between">
            <div class="space-y-6 p-8">
              <div class="aspect-video rounded-2xl overflow-hidden relative">
                <img src="{{ asset('assets/images/blog-1.jpg') }}" alt="Featured Article" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                <span class="absolute top-4 left-4 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full bg-[#0A4F78] text-white">FEATURED WHITEPAPER</span>
              </div>
              <h3 class="text-2xl font-black text-[#031827] dark:text-[#F6F5EF]">
                The Cellular Architecture of Centenarians: What 100-Year-Old Blood Biomarkers Reveal
              </h3>
              <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
                An in-depth analysis of cellular senescence, NAD+ regeneration, and dietary polyphenols observed across Okinawa and Sardinia.
              </p>
            </div>
            <div class="p-8 pt-0 flex justify-between items-center border-t border-[#0A4F78]/10 mt-4">
              <span class="text-xs font-bold text-[#7EA5B8]">BY DR. ELENA VANCE • 8 MIN READ</span>
              <a href="{{ route('customer.pages.blog') }}" class="text-xs font-black uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">READ ARTICLE →</a>
            </div>
          </div>

          <div class="lg:col-span-5 space-y-6 flex flex-col justify-between">
            <div class="p-6 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl border border-[#0A4F78]/15 flex gap-4 items-center card-hover-lift">
              <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                <img src="{{ asset('assets/images/blog-2.jpg') }}" alt="Blog 2" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
              </div>
              <div class="space-y-1">
                <span class="text-[9px] font-black uppercase tracking-wider text-[#2A8FC2]">NEUROLOGY</span>
                <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF] line-clamp-2">Polyphenols & Synaptic Plasticity in Middle Age</h4>
                <a href="{{ route('customer.pages.blog') }}" class="text-[11px] font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline block pt-1">READ STORY →</a>
              </div>
            </div>

            <div class="p-6 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl border border-[#0A4F78]/15 flex gap-4 items-center card-hover-lift">
              <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                <img src="{{ asset('assets/images/blog-3.jpg') }}" alt="Blog 3" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
              </div>
              <div class="space-y-1">
                <span class="text-[9px] font-black uppercase tracking-wider text-[#67B34A]">LONGEVITY HABITS</span>
                <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF] line-clamp-2">Ikigai: The Neurobiology of Purpose and Stress Reduction</h4>
                <a href="{{ route('customer.pages.blog') }}" class="text-[11px] font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline block pt-1">READ STORY →</a>
              </div>
            </div>

            <div class="p-6 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl border border-[#0A4F78]/15 flex gap-4 items-center card-hover-lift">
              <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                <img src="{{ asset('assets/images/blog-4.jpg') }}" alt="Blog 4" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
              </div>
              <div class="space-y-1">
                <span class="text-[9px] font-black uppercase tracking-wider text-[#2A8FC2]">CIRCADIAN REST</span>
                <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF] line-clamp-2">Restorative Sleep Habits of Okinawan Centenarians</h4>
                <a href="{{ route('customer.pages.blog') }}" class="text-[11px] font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline block pt-1">READ STORY →</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 12. FINAL CTA -->
    <section id="final-cta" class="py-24 bg-[#031827] text-white text-center relative overflow-hidden">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">
        <span class="inline-block px-4 py-1.5 rounded-full bg-[#2A8FC2]/20 text-[#2A8FC2] text-xs font-black uppercase tracking-[0.3em] border border-[#2A8FC2]/30">
          TRANSFORM YOUR VITALITY
        </span>
        <h2 class="text-4xl sm:text-6xl font-black tracking-tight leading-tight">
          LIVE LONG. LIVE WELL.
        </h2>
        <p class="text-base sm:text-xl text-[#E8DCC4] font-medium max-w-2xl mx-auto">
          DISCOVER A BETTER WAY TO LIVE WITH CLINICAL LONGEVITY FORMULATIONS.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
          <a href="{{ route('customer.shop') }}" class="w-full sm:w-auto px-10 py-5 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest rounded-full transition-all shadow-2xl hover:scale-105 btn-sheen text-center">
            SHOP ALL FORMULATIONS
          </a>
          <a href="{{ route('customer.pages.science') }}" class="w-full sm:w-auto px-10 py-5 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-black uppercase tracking-widest rounded-full backdrop-blur-md transition-all text-center">
            OUR SCIENCE
          </a>
        </div>
      </div>
    </section>
</x-layouts.customer>
