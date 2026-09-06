    <!-- 03. WHO WE ARE -->
    <section id="who-we-are" class="py-24 bg-[#F6F5EF] dark:bg-[#031827] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          <!-- Image Column with Dynamic Nodes -->
          <div class="lg:col-span-6 relative">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-[#0A4F78]/20 group">
              <img src="{{ asset('assets/images/story-lifestyle.webp') }}" alt="Blue Zone Centenarian Lifestyle" onerror="this.onerror=null; this.src='{{ asset('assets/images/story-lifestyle.jpg') }}';" width="800" height="460" loading="lazy" decoding="async" class="w-full h-[460px] object-cover group-hover:scale-105 transition-transform duration-700" />
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
