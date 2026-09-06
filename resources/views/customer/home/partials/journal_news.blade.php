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
                <img src="{{ asset('assets/images/blog-1.webp') }}" alt="Featured Article" onerror="this.onerror=null; this.src='{{ asset('assets/images/blog-1.jpg') }}';" width="700" height="394" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
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
                <img src="{{ asset('assets/images/blog-2.webp') }}" alt="Blog 2" onerror="this.onerror=null; this.src='{{ asset('assets/images/blog-2.jpg') }}';" width="80" height="80" loading="lazy" decoding="async" class="w-full h-full object-cover" />
              </div>
              <div class="space-y-1">
                <span class="text-[9px] font-black uppercase tracking-wider text-[#2A8FC2]">NEUROLOGY</span>
                <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF] line-clamp-2">Polyphenols & Synaptic Plasticity in Middle Age</h4>
                <a href="{{ route('customer.pages.blog') }}" class="text-[11px] font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline block pt-1">READ STORY →</a>
              </div>
            </div>

            <div class="p-6 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl border border-[#0A4F78]/15 flex gap-4 items-center card-hover-lift">
              <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                <img src="{{ asset('assets/images/blog-3.webp') }}" alt="Blog 3" onerror="this.onerror=null; this.src='{{ asset('assets/images/blog-3.jpg') }}';" width="80" height="80" loading="lazy" decoding="async" class="w-full h-full object-cover" />
              </div>
              <div class="space-y-1">
                <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-[#67B34A]">LONGEVITY HABITS</span>
                <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF] line-clamp-2">Ikigai: The Neurobiology of Purpose and Stress Reduction</h4>
                <a href="{{ route('customer.pages.blog') }}" class="text-[11px] font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline block pt-1">READ STORY →</a>
              </div>
            </div>

            <div class="p-6 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl border border-[#0A4F78]/15 flex gap-4 items-center card-hover-lift">
              <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                <img src="{{ asset('assets/images/blog-4.webp') }}" alt="Blog 4" onerror="this.onerror=null; this.src='{{ asset('assets/images/blog-4.jpg') }}';" width="80" height="80" loading="lazy" decoding="async" class="w-full h-full object-cover" />
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
