<x-layouts.customer :title="'MEET THE TEAM — ' . __('app.brand_name')" :description="'Meet the neurobiologists, longevity researchers, and clinical nutritionists behind BLUE ZONE science-driven dietary formulations.'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 space-y-16 sm:space-y-24">
      
      <!-- HERO -->
      <div class="text-center max-w-3xl mx-auto space-y-4">
        <span class="text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
          SCIENTIFIC ADVISORY BOARD
        </span>
        <h1 class="text-4xl sm:text-6xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
          MEET THE ARCHITECTS OF LONGEVITY.
        </h1>
        <p class="text-sm sm:text-base text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
          Our multidisciplinary board combines ethnobotanical field research from the 5 Blue Zones with molecular pharmacology and clinical cellular biology.
        </p>
      </div>

      <!-- TEAM GRID -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        
        <!-- Member 1 -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl overflow-hidden border border-[#0A4F78]/15 shadow-xl card-hover-lift flex flex-col justify-between">
          <div>
            <div class="aspect-square overflow-hidden bg-[#031827]">
              <img src="{{ asset('assets/images/team_elena.jpg') }}" alt="Dr. Elena Vance" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
            </div>
            <div class="p-6 space-y-3">
              <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">CHIEF MEDICAL OFFICER</span>
              <h3 class="text-2xl font-black text-[#031827] dark:text-[#F6F5EF]">Dr. Elena Vance, MD, PhD</h3>
              <p class="text-xs font-extrabold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2]">Stanford Longevity Center Fellow</p>
              <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">
                Leading researcher in cellular senescence and sirtuin pathway activation. Dr. Vance leads formulation standards and clinical efficacy assays across all Blue Zone products.
              </p>
            </div>
          </div>
          <div class="p-6 pt-0 text-[11px] font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50 border-t border-[#0A4F78]/10">
            Specialization: Sirtuin Biology & Synaptic Plasticity
          </div>
        </div>

        <!-- Member 2 -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl overflow-hidden border border-[#0A4F78]/15 shadow-xl card-hover-lift flex flex-col justify-between">
          <div>
            <div class="aspect-square overflow-hidden bg-[#031827]">
              <img src="{{ asset('assets/images/team_marco.jpg') }}" alt="Prof. Marco Moretti" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
            </div>
            <div class="p-6 space-y-3">
              <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">ETHNOBOTANY</span>
              <h3 class="text-2xl font-black text-[#031827] dark:text-[#F6F5EF]">Prof. Marco Moretti, PhD</h3>
              <p class="text-xs font-extrabold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2]">University of Sassari, Sardinia</p>
              <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">
                25+ years documenting indigenous Mediterranean plants and wild herbal teas of Ikaria and Sardinia. He oversees botanical extraction purity and bio-integrity.
              </p>
            </div>
          </div>
          <div class="p-6 pt-0 text-[11px] font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50 border-t border-[#0A4F78]/10">
            Specialization: Mediterranean Polyphenols & Wild Teas
          </div>
        </div>

        <!-- Member 3 -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl overflow-hidden border border-[#0A4F78]/15 shadow-xl card-hover-lift flex flex-col justify-between">
          <div>
            <div class="aspect-square overflow-hidden bg-[#031827]">
              <img src="{{ asset('assets/images/team_kenji.jpg') }}" alt="Dr. Kenji Tanaka" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
            </div>
            <div class="p-6 space-y-3">
              <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">CELLULAR BIOLOGY</span>
              <h3 class="text-2xl font-black text-[#031827] dark:text-[#F6F5EF]">Dr. Kenji Tanaka, PhD</h3>
              <p class="text-xs font-extrabold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2]">Kyoto University Medical Sciences</p>
              <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">
                Over 18 years studying centenarian mitochondrial density in Okinawa. Dr. Tanaka formulates our ATP cellular energy complex without synthetic stimulants.
              </p>
            </div>
          </div>
          <div class="p-6 pt-0 text-[11px] font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50 border-t border-[#0A4F78]/10">
            Specialization: Co-Q10 Ubiquinol & Krebs Cycle Biogenesis
          </div>
        </div>

        <!-- Member 4 -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl overflow-hidden border border-[#0A4F78]/15 shadow-xl card-hover-lift flex flex-col justify-between">
          <div>
            <div class="aspect-square overflow-hidden bg-[#031827]">
              <img src="{{ asset('assets/images/team_sarah.jpg') }}" alt="Sarah Jenkins" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
            </div>
            <div class="p-6 space-y-3">
              <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">BIO-NUTRITION</span>
              <h3 class="text-2xl font-black text-[#031827] dark:text-[#F6F5EF]">Sarah Jenkins, MS</h3>
              <p class="text-xs font-extrabold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2]">Director of Clinical Bio-Nutrition</p>
              <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">
                Author of Mediterranean Polyphenol Standardization. Sarah oversees plant botanical selection and spore-forming probiotic micro-encapsulation.
              </p>
            </div>
          </div>
          <div class="p-6 pt-0 text-[11px] font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50 border-t border-[#0A4F78]/10">
            Specialization: Synbiotic Microbiome & Polyphenol Delivery
          </div>
        </div>

      </div>

      <!-- ETHICS & STANDARDS -->
      <div class="bg-[#031827] rounded-3xl p-8 sm:p-12 text-white border border-[#0A4F78]/30 space-y-6">
        <div class="max-w-3xl space-y-4">
          <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#67B34A]">OUR ETHICAL MANDATE</span>
          <h2 class="text-2xl sm:text-4xl font-black tracking-tight">EVIDENCE OVER HYPE. BIO-INTEGRITY OVER TRENDS.</h2>
          <p class="text-xs sm:text-sm text-[#E8DCC4] leading-relaxed font-medium">
            Every BLUE ZONE ingredient must pass three gates before formulation: (1) Documented epidemiological prevalence in at least one validated Blue Zone region, (2) Peer-reviewed human clinical trials validating biological mechanism, and (3) 100% bio-identical purity with zero synthetic fillers.
          </p>
        </div>
      </div>

    </div>
</x-layouts.customer>
