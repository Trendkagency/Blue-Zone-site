<x-layouts.customer :title="'LONGEVITY JOURNAL — ' . __('app.brand_name')" :description="'Evidence-based articles on telomere attrition, intermittent fasting, polyphenols, and lifestyle habits from the world\'s 5 longevity Blue Zones.'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 space-y-16">
      
      <!-- HERO -->
      <div class="text-center max-w-3xl mx-auto space-y-4">
        <span class="text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
          EVIDENCE & RESEARCH
        </span>
        <h1 class="text-4xl sm:text-6xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
          THE LONGEVITY JOURNAL.
        </h1>
        <p class="text-sm sm:text-base text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
          Clinical whitepapers, centenarian biological mechanisms, and dietary protocols written by our scientific board.
        </p>
      </div>

      <!-- FEATURED SPOTLIGHT ARTICLE -->
      <div class="bg-white dark:bg-[#062B49] rounded-3xl overflow-hidden border border-[#0A4F78]/15 shadow-xl card-hover-lift grid grid-cols-1 lg:grid-cols-12 gap-8 items-center p-6 sm:p-10">
        <div class="lg:col-span-7 rounded-2xl overflow-hidden aspect-video bg-[#031827]">
          <img src="{{ asset('assets/images/blog-1.jpg') }}" alt="Featured Whitepaper" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
        </div>
        <div class="lg:col-span-5 space-y-4">
          <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full bg-[#0A4F78] text-white">
            FEATURED WHITEPAPER
          </span>
          <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF] leading-tight">
            The Cellular Architecture of Centenarians: What 100-Year-Old Blood Biomarkers Reveal
          </h2>
          <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
            An in-depth analysis of cellular senescence, NAD+ regeneration, and dietary polyphenols observed across Okinawa and Sardinia.
          </p>
          <div class="pt-2 flex items-center justify-between text-xs font-bold text-[#7EA5B8] border-t border-[#0A4F78]/10">
            <span>BY DR. ELENA VANCE • 8 MIN READ</span>
            <span class="text-[#0A4F78] dark:text-[#2A8FC2]">MAY 2026</span>
          </div>
        </div>
      </div>

      <!-- ARTICLES GRID -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Article 1 -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl overflow-hidden border border-[#0A4F78]/15 shadow-md card-hover-lift flex flex-col justify-between p-6 space-y-4">
          <div class="space-y-4">
            <div class="aspect-video rounded-2xl overflow-hidden bg-[#031827]">
              <img src="{{ asset('assets/images/blog-2.jpg') }}" alt="Article 2" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
            </div>
            <span class="text-[9px] font-black uppercase tracking-wider text-[#2A8FC2]">NEUROLOGY</span>
            <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF] leading-snug">
              Polyphenols & Synaptic Plasticity in Middle Age
            </h3>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
              How bacosides and flavanols stimulate BDNF growth factors and protect against cognitive fatigue.
            </p>
          </div>
          <div class="pt-4 border-t border-[#0A4F78]/10 text-[11px] font-bold text-[#7EA5B8]">
            5 MIN READ • DR. KENJI TANAKA
          </div>
        </div>

        <!-- Article 2 -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl overflow-hidden border border-[#0A4F78]/15 shadow-md card-hover-lift flex flex-col justify-between p-6 space-y-4">
          <div class="space-y-4">
            <div class="aspect-video rounded-2xl overflow-hidden bg-[#031827]">
              <img src="{{ asset('assets/images/blog-3.jpg') }}" alt="Article 3" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
            </div>
            <span class="text-[9px] font-black uppercase tracking-wider text-[#67B34A]">LONGEVITY HABITS</span>
            <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF] leading-snug">
              Ikigai: The Neurobiology of Purpose and Stress Reduction
            </h3>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
              Why having a daily sense of direction lowers resting cortisol and improves cardiovascular resilience.
            </p>
          </div>
          <div class="pt-4 border-t border-[#0A4F78]/10 text-[11px] font-bold text-[#7EA5B8]">
            6 MIN READ • SARAH JENKINS, MS
          </div>
        </div>

        <!-- Article 3 -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl overflow-hidden border border-[#0A4F78]/15 shadow-md card-hover-lift flex flex-col justify-between p-6 space-y-4">
          <div class="space-y-4">
            <div class="aspect-video rounded-2xl overflow-hidden bg-[#031827]">
              <img src="{{ asset('assets/images/blog-4.jpg') }}" alt="Article 4" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" class="w-full h-full object-cover" />
            </div>
            <span class="text-[9px] font-black uppercase tracking-wider text-[#2A8FC2]">CIRCADIAN REST</span>
            <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF] leading-snug">
              Restorative Sleep Habits of Okinawan Centenarians
            </h3>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
              Evening herbal infusions, darkness protocols, and deep slow-wave sleep architecture.
            </p>
          </div>
          <div class="pt-4 border-t border-[#0A4F78]/10 text-[11px] font-bold text-[#7EA5B8]">
            4 MIN READ • PROF. MARCO MORETTI
          </div>
        </div>

      </div>

    </div>
</x-layouts.customer>
