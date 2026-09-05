<x-layouts.customer :title="'OUR SCIENCE — ' . __('app.brand_name')" :description="'Discover the clinical research, bio-identical extraction, and formulation science behind BLUE ZONE dietary supplements.'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 space-y-16 sm:space-y-24">
      
      <!-- 1. EDITORIAL HERO SECTION -->
      <section class="max-w-3xl mx-auto text-center space-y-4">
        <span class="inline-block text-[11px] font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
          OUR SCIENCE
        </span>
        <h1 class="text-3xl sm:text-5xl md:text-6xl font-light text-[#031827] dark:text-[#F6F5EF] tracking-tight leading-tight">
          WHERE LONGEVITY MEETS <span class="font-bold text-[#67B34A]">FORMULATION</span>.
        </h1>
        <p class="text-xs sm:text-base text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium leading-relaxed max-w-2xl mx-auto">
          BLUE ZONE explores the dietary patterns, botanical polyphenols, and lifestyle habits observed in the world's longest-lived communities.
        </p>
      </section>

      <!-- 2. THE 4-STAGE INTERACTIVE JOURNEY -->
      <section class="space-y-10" id="bz-science-journey-section">
        
        <div class="text-center max-w-xl mx-auto space-y-2">
          <span class="text-[10px] font-extrabold uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">THE PROCESS</span>
          <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#031827] dark:text-[#F6F5EF]">THE 4 STAGES</h2>
        </div>

        <!-- Desktop Horizontal Timeline -->
        <div class="hidden md:block relative py-6 max-w-3xl mx-auto" id="bz-science-desktop-timeline">
          <div class="absolute top-1/2 left-[12%] right-[12%] h-0.5 bg-[#0A4F78]/15 dark:bg-[#0A4F78]/30 -translate-y-1/2 z-0"></div>
          <div id="bz-timeline-progress" class="absolute top-1/2 left-[12%] h-0.5 bg-[#67B34A] -translate-y-1/2 z-0 transition-all duration-500" style="width: 0%;"></div>

          <div class="grid grid-cols-4 gap-4 relative z-10 text-center">
            <!-- Node 01: SOURCE -->
            <button onclick="BLUEZONE_SCIENCE.select(0)" onmouseenter="BLUEZONE_SCIENCE.select(0)" class="bz-timeline-node group cursor-pointer flex flex-col items-center gap-2 transition-transform duration-300" data-index="0">
              <div class="node-circle w-11 h-11 rounded-full bg-[#67B34A] text-white flex items-center justify-center font-mono text-xs font-black shadow-[0_0_15px_rgba(103,179,74,0.4)] scale-110 border-2 border-[#67B34A] transition-all">
                01
              </div>
              <span class="node-title text-xs font-extrabold uppercase tracking-widest text-[#67B34A]">SOURCE</span>
            </button>

            <!-- Node 02: FORMULATION -->
            <button onclick="BLUEZONE_SCIENCE.select(1)" onmouseenter="BLUEZONE_SCIENCE.select(1)" class="bz-timeline-node group cursor-pointer flex flex-col items-center gap-2 transition-transform duration-300" data-index="1">
              <div class="node-circle w-9 h-9 rounded-full bg-[#F6F5EF] dark:bg-[#031827] border-2 border-[#0A4F78]/30 flex items-center justify-center font-mono text-xs font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50 transition-all group-hover:border-[#67B34A]">
                02
              </div>
              <span class="node-title text-xs font-semibold uppercase tracking-widest text-[#031827]/50 dark:text-[#F6F5EF]/50 group-hover:text-[#67B34A] transition-colors">FORMULATION</span>
            </button>

            <!-- Node 03: VALIDATION -->
            <button onclick="BLUEZONE_SCIENCE.select(2)" onmouseenter="BLUEZONE_SCIENCE.select(2)" class="bz-timeline-node group cursor-pointer flex flex-col items-center gap-2 transition-transform duration-300" data-index="2">
              <div class="node-circle w-9 h-9 rounded-full bg-[#F6F5EF] dark:bg-[#031827] border-2 border-[#0A4F78]/30 flex items-center justify-center font-mono text-xs font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50 transition-all group-hover:border-[#67B34A]">
                03
              </div>
              <span class="node-title text-xs font-semibold uppercase tracking-widest text-[#031827]/50 dark:text-[#F6F5EF]/50 group-hover:text-[#67B34A] transition-colors">VALIDATION</span>
            </button>

            <!-- Node 04: WELLNESS -->
            <button onclick="BLUEZONE_SCIENCE.select(3)" onmouseenter="BLUEZONE_SCIENCE.select(3)" class="bz-timeline-node group cursor-pointer flex flex-col items-center gap-2 transition-transform duration-300" data-index="3">
              <div class="node-circle w-9 h-9 rounded-full bg-[#F6F5EF] dark:bg-[#031827] border-2 border-[#0A4F78]/30 flex items-center justify-center font-mono text-xs font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50 transition-all group-hover:border-[#67B34A]">
                04
              </div>
              <span class="node-title text-xs font-semibold uppercase tracking-widest text-[#031827]/50 dark:text-[#F6F5EF]/50 group-hover:text-[#67B34A] transition-colors">WELLNESS</span>
            </button>
          </div>
        </div>

        <!-- Mobile Vertical Timeline Selector -->
        <div class="md:hidden space-y-2 border-l-2 border-[#0A4F78]/20 pl-4 py-2" id="bz-science-mobile-timeline">
          <button onclick="BLUEZONE_SCIENCE.select(0)" class="bz-mobile-node flex items-center gap-3 py-2 text-xs font-bold text-[#67B34A]" data-index="0">
            <span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center text-[10px]">01</span>
            <span>SOURCE — FROM NATURE</span>
          </button>
          <button onclick="BLUEZONE_SCIENCE.select(1)" class="bz-mobile-node flex items-center gap-3 py-2 text-xs font-medium text-[#031827]/60 dark:text-[#F6F5EF]/60" data-index="1">
            <span class="w-6 h-6 rounded-full bg-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] flex items-center justify-center text-[10px]">02</span>
            <span>FORMULATION — PRECISION</span>
          </button>
          <button onclick="BLUEZONE_SCIENCE.select(2)" class="bz-mobile-node flex items-center gap-3 py-2 text-xs font-medium text-[#031827]/60 dark:text-[#F6F5EF]/60" data-index="2">
            <span class="w-6 h-6 rounded-full bg-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] flex items-center justify-center text-[10px]">03</span>
            <span>VALIDATION — QUALITY FOCUS</span>
          </button>
          <button onclick="BLUEZONE_SCIENCE.select(3)" class="bz-mobile-node flex items-center gap-3 py-2 text-xs font-medium text-[#031827]/60 dark:text-[#F6F5EF]/60" data-index="3">
            <span class="w-6 h-6 rounded-full bg-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] flex items-center justify-center text-[10px]">04</span>
            <span>WELLNESS — DAILY LIFE</span>
          </button>
        </div>

        <!-- Two-Column Editorial Display Panel -->
        <div class="p-8 sm:p-12 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 shadow-xl transition-all duration-500 min-h-[380px]" id="bz-science-panel">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Left Column: Editorial Visual -->
            <div class="lg:col-span-6 relative">
              <div class="w-full h-72 sm:h-96 rounded-2xl overflow-hidden relative shadow-md border border-[#0A4F78]/20 bg-[#031827] group">
                <img
                  id="bz-science-active-img"
                  src="{{ asset('assets/images/hero_longevity.jpg') }}"
                  alt="Nature Sourcing"
                  onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';"
                  class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105"
                />
                
                <div class="absolute inset-0 bg-gradient-to-t from-[#031827]/90 via-[#031827]/30 to-transparent"></div>
                
                <!-- Biological System Flow -->
                <div class="absolute inset-x-4 bottom-4 p-4 rounded-xl bg-[#031827]/85 border border-[#2A8FC2]/30 backdrop-blur-md text-white space-y-2">
                  <span class="block text-[9px] font-mono font-bold tracking-widest text-[#2A8FC2] uppercase">
                    BIOLOGICAL SYSTEM FLOW:
                  </span>
                  <div class="flex items-center justify-between text-[10px] font-mono font-bold text-white/90">
                    <span id="bz-flow-step-1" class="text-[#67B34A]">INGREDIENT</span>
                    <span class="text-[#2A8FC2]">→</span>
                    <span id="bz-flow-step-2" class="text-white/40">FUNCTION</span>
                    <span class="text-[#2A8FC2]">→</span>
                    <span id="bz-flow-step-3" class="text-white/40">BODY</span>
                    <span class="text-[#2A8FC2]">→</span>
                    <span id="bz-flow-step-4" class="text-white/40">WELLNESS</span>
                  </div>
                </div>

                <div class="absolute top-4 left-4 px-3 py-1 rounded-full bg-[#031827]/80 text-[#67B34A] text-[10px] font-mono font-bold border border-[#67B34A]/40 backdrop-blur-sm">
                  STAGE <span id="bz-science-stage-code">01/04</span>
                </div>
              </div>
            </div>

            <!-- Right Column: Stage Details -->
            <div class="lg:col-span-6 space-y-6">
              <div class="space-y-2">
                <div class="flex items-center gap-3">
                  <span id="bz-science-active-num" class="text-4xl font-light font-mono text-[#67B34A]">01</span>
                  <span class="text-xs font-bold text-[#67B34A]">—</span>
                  <span id="bz-science-active-stage" class="text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">SOURCE</span>
                </div>

                <h3 id="bz-science-active-title" class="text-2xl sm:text-4xl font-bold text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                  FROM NATURE
                </h3>
              </div>

              <p id="bz-science-active-desc" class="text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed">
                Selected botanical and nutritional ingredients inspired by the natural foundations of longevity.
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
      </section>

      <!-- 3. FORMULATED WITH PURPOSE -->
      <section class="space-y-8 pt-4">
        <div class="text-center max-w-2xl mx-auto space-y-3">
          <span class="text-[11px] font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
            OUR PHILOSOPHY
          </span>
          <h2 class="text-3xl sm:text-4xl font-light text-[#031827] dark:text-[#F6F5EF] tracking-tight">
            FORMULATED WITH <span class="font-bold text-[#67B34A]">PURPOSE</span>.
          </h2>
          <p class="text-xs sm:text-sm text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium">
            A clear focus on ingredient selection, formulation balance, and practical daily wellness.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Point 01 -->
          <div class="space-y-3 p-2">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-full bg-[#67B34A]/15 text-[#67B34A] flex items-center justify-center font-mono font-bold text-xs">01</span>
              <h3 class="text-sm font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">INGREDIENT SELECTION</h3>
            </div>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
              Identifying key dietary botanicals and polyphenols observed across long-lived communities worldwide.
            </p>
          </div>

          <!-- Point 02 -->
          <div class="space-y-3 p-2">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-full bg-[#0A4F78]/15 dark:bg-[#0A4F78]/40 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center justify-center font-mono font-bold text-xs">02</span>
              <h3 class="text-sm font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">FORMULATION THINKING</h3>
            </div>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
              Thoughtfully selected ingredients brought together into focused wellness formulations.
            </p>
          </div>

          <!-- Point 03 -->
          <div class="space-y-3 p-2">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-full bg-[#2A8FC2]/15 text-[#2A8FC2] flex items-center justify-center font-mono font-bold text-xs">03</span>
              <h3 class="text-sm font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">EVERYDAY WELLNESS</h3>
            </div>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
              Creating clean, practical dietary support designed to fit effortlessly into your daily morning routine.
            </p>
          </div>
        </div>
      </section>

      <!-- 4. SCIENCE IN PRACTICE -->
      <section class="bg-[#031827] text-white rounded-3xl p-8 sm:p-12 border border-[#0A4F78]/40 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center sm:text-left">
          <span class="text-[10px] font-extrabold uppercase tracking-[0.25em] text-[#67B34A]">SCIENCE IN PRACTICE</span>
          <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">SEE THE SCIENCE IN BLUE MIND</h2>
          <p class="text-xs sm:text-sm text-[#F6F5EF]/75 font-medium max-w-xl">
            Discover how the BLUE ZONE formulation philosophy comes to life in our flagship cognitive complex.
          </p>
        </div>
        <a href="{{ route('customer.product.show', 'blue-mind') }}" class="px-7 py-3.5 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-extrabold uppercase tracking-widest rounded-xl transition-all shadow-md shrink-0 whitespace-nowrap hover:translate-x-1">
          EXPLORE BLUE MIND →
        </a>
      </section>

    </div>

    <!-- Science Controller Script -->
    <script>
      (function() {
        const SCIENCE_DATA = [
          {
            num: "01",
            code: "01/04",
            stage: "SOURCE",
            title: "FROM NATURE",
            desc: "Selected botanical and nutritional ingredients inspired by the natural foundations of longevity.",
            img: "{{ asset('assets/images/hero_longevity.jpg') }}",
            chips: ["Standardized Botanical Extraction", "Peak Potency Sourcing"],
            flowStep: 1
          },
          {
            num: "02",
            code: "02/04",
            stage: "FORMULATION",
            title: "PRECISION IN EVERY FORMULA.",
            desc: "Thoughtfully selected ingredients brought together into focused wellness formulations.",
            img: "{{ asset('assets/products/blue-mind.jpg') }}",
            chips: ["Bio-Identical Nutrient Ratios", "Cellular Absorption Focus"],
            flowStep: 2
          },
          {
            num: "03",
            code: "03/04",
            stage: "VALIDATION",
            title: "QUALITY YOU CAN TRUST.",
            desc: "A clear focus on ingredient quality, consistency, and responsible formulation.",
            img: "{{ asset('assets/images/blog-1.jpg') }}",
            chips: ["Third-Party Quality Verified", "Zero Synthetic Additives"],
            flowStep: 3
          },
          {
            num: "04",
            code: "04/04",
            stage: "WELLNESS",
            title: "DESIGNED FOR DAILY LIFE.",
            desc: "Bringing longevity-inspired principles into modern everyday wellness.",
            img: "{{ asset('assets/images/blog-2.jpg') }}",
            chips: ["Cognitive Resilience", "Daily Vitality Support"],
            flowStep: 4
          }
        ];

        function selectScience(idx) {
          if (idx < 0 || idx >= SCIENCE_DATA.length) return;
          const s = SCIENCE_DATA[idx];

          const progressLine = document.getElementById('bz-timeline-progress');
          if (progressLine) {
            const percents = [0, 33.3, 66.6, 100];
            progressLine.style.width = percents[idx] + '%';
          }

          const desktopNodes = document.querySelectorAll('#bz-science-desktop-timeline .bz-timeline-node');
          desktopNodes.forEach((node, i) => {
            const circle = node.querySelector('.node-circle');
            const title = node.querySelector('.node-title');
            if (i === idx) {
              if (circle) circle.className = 'node-circle w-11 h-11 rounded-full bg-[#67B34A] text-white flex items-center justify-center font-mono text-xs font-black shadow-[0_0_15px_rgba(103,179,74,0.4)] scale-110 border-2 border-[#67B34A] transition-all';
              if (title) title.className = 'node-title text-xs font-extrabold uppercase tracking-widest text-[#67B34A]';
            } else {
              if (circle) circle.className = 'node-circle w-9 h-9 rounded-full bg-[#F6F5EF] dark:bg-[#031827] border-2 border-[#0A4F78]/30 flex items-center justify-center font-mono text-xs font-bold text-[#031827]/50 dark:text-[#F6F5EF]/50 transition-all group-hover:border-[#67B34A]';
              if (title) title.className = 'node-title text-xs font-semibold uppercase tracking-widest text-[#031827]/50 dark:text-[#F6F5EF]/50 group-hover:text-[#67B34A] transition-colors';
            }
          });

          const mobileNodes = document.querySelectorAll('#bz-science-mobile-timeline .bz-mobile-node');
          mobileNodes.forEach((btn, i) => {
            if (i === idx) {
              btn.className = 'bz-mobile-node flex items-center gap-3 py-2 text-xs font-bold text-[#67B34A]';
            } else {
              btn.className = 'bz-mobile-node flex items-center gap-3 py-2 text-xs font-medium text-[#031827]/60 dark:text-[#F6F5EF]/60';
            }
          });

          for (let step = 1; step <= 4; step++) {
            const flowEl = document.getElementById(`bz-flow-step-${step}`);
            if (flowEl) {
              if (step <= s.flowStep) {
                flowEl.className = 'text-[#67B34A] font-bold';
              } else {
                flowEl.className = 'text-white/40 font-normal';
              }
            }
          }

          const panel = document.getElementById('bz-science-panel');
          if (panel) {
            panel.style.opacity = '0.3';
            setTimeout(() => {
              const imgEl = document.getElementById('bz-science-active-img');
              const codeEl = document.getElementById('bz-science-stage-code');
              const numEl = document.getElementById('bz-science-active-num');
              const stageEl = document.getElementById('bz-science-active-stage');
              const titleEl = document.getElementById('bz-science-active-title');
              const descEl = document.getElementById('bz-science-active-desc');
              const chipsEl = document.getElementById('bz-science-active-chips');

              if (imgEl) {
                imgEl.src = s.img;
                imgEl.alt = s.title;
              }
              if (codeEl) codeEl.textContent = s.code;
              if (numEl) numEl.textContent = s.num;
              if (stageEl) stageEl.textContent = s.stage;
              if (titleEl) titleEl.textContent = s.title;
              if (descEl) descEl.textContent = s.desc;
              if (chipsEl) {
                chipsEl.innerHTML = s.chips.map((chip, cIdx) => 
                  `<span class="px-3 py-1.5 rounded-lg ${cIdx === 0 ? 'bg-[#67B34A]/15 text-[#67B34A]' : 'bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 text-[#031827] dark:text-[#F6F5EF]'} text-xs font-bold">${chip}</span>`
                ).join('');
              }

              panel.style.opacity = '1';
            }, 180);
          }
        }

        window.BLUEZONE_SCIENCE = { select: selectScience };
      })();
    </script>
</x-layouts.customer>
