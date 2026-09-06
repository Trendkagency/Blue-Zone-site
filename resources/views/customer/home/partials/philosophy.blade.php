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
                <img id="pillar-active-img" src="{{ asset('assets/images/hero/hero-03.webp') }}" alt="Pillar Visual" onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-03.jpg') }}';" width="600" height="200" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
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

    <!-- Inline Script for Pillar Selection Controller -->
    <script>
      (function() {
        const PILLARS_DATA = [
          {
            num: "01",
            title: "MOVEMENT",
            desc: "Natural daily movement is one of the defining habits shared by centenarians, supporting joint stamina, circulation, and fluid mobility throughout life.",
            tag: "Joint Stamina & Fluid Articulation",
            img: "{{ asset('assets/images/hero/hero-03.jpg') }}",
            spokeColor: "#67B34A",
            svgIcon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'
          },
          {
            num: "02",
            title: "NUTRITION",
            desc: "Bio-engineered from polyphenol-rich plant botanicals and standardized cellular antioxidants to nourish cellular metabolic pathways.",
            tag: "Botanical Bio-Integrity",
            img: "{{ asset('assets/images/hero/hero-02.jpg') }}",
            spokeColor: "#67B34A",
            svgIcon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>'
          },
          {
            num: "03",
            title: "PURPOSE",
            desc: "Cultivating Ikigai purpose—a clear sense of daily direction proven to promote neurological resilience and cognitive focus.",
            tag: "Neurological Resilience & Focus",
            img: "{{ asset('assets/images/hero/hero-01.jpg') }}",
            spokeColor: "#67B34A",
            svgIcon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h-0a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>'
          },
          {
            num: "04",
            title: "COMMUNITY",
            desc: "Fostering deep social bonds and emotional harmony that reduce systemic cortisol levels and daily physiological stress.",
            tag: "Cortisol & Stress Reduction",
            img: "{{ asset('assets/images/okinawa.jpg') }}",
            spokeColor: "#67B34A",
            svgIcon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
          },
          {
            num: "05",
            title: "REST",
            desc: "Sustaining restorative nighttime sleep and circadian rhythm alignment to allow cellular ATP regeneration without burnout.",
            tag: "Circadian ATP Regeneration",
            img: "{{ asset('assets/images/hero/hero-05.jpg') }}",
            spokeColor: "#67B34A",
            svgIcon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>'
          },
          {
            num: "06",
            title: "WELLNESS",
            desc: "Standardized botanical nutrition designed to preserve cellular energy, structural posture, and lifelong immune fortitude.",
            tag: "Systemic Immune Fortitude",
            img: "{{ asset('assets/images/hero_longevity.jpg') }}",
            spokeColor: "#67B34A",
            svgIcon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>'
          }
        ];

        function selectPillar(idx) {
          if (idx < 0 || idx >= PILLARS_DATA.length) return;
          const p = PILLARS_DATA[idx];

          // 1. Update Desktop Left Nav
          const desktopBtns = document.querySelectorAll('.pillar-desktop-btn');
          desktopBtns.forEach((btn, i) => {
            const numSpan = btn.querySelector('span:first-child');
            const titleSpan = btn.querySelector('span:last-child');
            if (i === idx) {
              btn.className = 'pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-[#67B34A] bg-[#67B34A]/10';
              if (numSpan) numSpan.className = 'text-xs font-extrabold text-[#67B34A] font-mono';
              if (titleSpan) titleSpan.className = 'text-sm font-bold text-[#67B34A] tracking-wider uppercase';
            } else {
              btn.className = 'pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5';
              if (numSpan) numSpan.className = 'text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono';
              if (titleSpan) titleSpan.className = 'text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]';
            }
          });

          // 2. Update Mobile Horizontal Nav
          const mobileBtns = document.querySelectorAll('.pillar-nav-btn');
          mobileBtns.forEach((btn, i) => {
            if (i === idx) {
              btn.className = 'pillar-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#67B34A] text-white';
            } else {
              btn.className = 'pillar-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]';
            }
          });

          // 3. Update Orbital Center SVG Nodes & Spokes
          for (let i = 0; i < 6; i++) {
            const node = document.getElementById(`node-${i}`);
            const spoke = document.getElementById(`spoke-${i}`);
            if (i === idx) {
              if (node) {
                node.setAttribute('r', '11');
                node.setAttribute('fill', '#67B34A');
                node.setAttribute('stroke', '#FFFFFF');
                node.setAttribute('stroke-width', '2.5');
              }
              if (spoke) {
                spoke.setAttribute('stroke', '#67B34A');
                spoke.setAttribute('stroke-width', '2');
                spoke.setAttribute('opacity', '0.9');
              }
            } else {
              if (node) {
                node.setAttribute('r', '7');
                node.setAttribute('fill', '#0A4F78');
                node.setAttribute('stroke', '#2A8FC2');
                node.setAttribute('stroke-width', '1.5');
              }
              if (spoke) {
                spoke.setAttribute('stroke', '#2A8FC2');
                spoke.setAttribute('stroke-width', '1');
                spoke.setAttribute('opacity', '0.25');
              }
            }
          }

          // 4. Update Right Display Panel with smooth fade
          const panel = document.getElementById('pillar-content-panel');
          if (panel) {
            panel.style.opacity = '0.4';
            setTimeout(() => {
              const numEl = document.getElementById('pillar-active-num');
              const titleEl = document.getElementById('pillar-active-title');
              const descEl = document.getElementById('pillar-active-desc');
              const tagEl = document.getElementById('pillar-active-tag');
              const iconBox = document.getElementById('pillar-active-icon-box');
              const imgEl = document.getElementById('pillar-active-img');

              if (numEl) numEl.textContent = p.num;
              if (titleEl) titleEl.textContent = p.title;
              if (descEl) descEl.textContent = p.desc;
              if (tagEl) tagEl.innerHTML = `<span>${p.tag}</span>`;
              if (iconBox) iconBox.innerHTML = p.svgIcon;
              if (imgEl) {
                imgEl.src = p.img;
                imgEl.alt = p.title;
              }

              panel.style.opacity = '1';
            }, 180);
          }
        }

        window.BLUEZONE_PILLARS = { select: selectPillar };
      })();
    </script>
