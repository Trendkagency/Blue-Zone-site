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
                src="{{ asset('assets/images/okinawa.webp') }}"
                alt="Okinawa Blue Zone"
                onerror="this.onerror=null; this.src='{{ asset('assets/images/okinawa.jpg') }}';"
                width="800"
                height="520"
                loading="lazy"
                decoding="async"
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

    <!-- Controller Script for Interactive Blue Zones Showcase -->
    <script>
      (function() {
        const REGIONS_DATA = [
          {
            num: "01",
            title: "OKINAWA",
            country: "JAPAN",
            img: "{{ asset('assets/images/okinawa.jpg') }}",
            marker: "COGNITION",
            desc: "Moai social circles, daily natural movement, and a plant-rich traditional diet abundant in turmeric and purple sweet potatoes."
          },
          {
            num: "02",
            title: "SARDINIA",
            country: "ITALY",
            img: "{{ asset('assets/images/sardinia.jpg') }}",
            marker: "CARDIO",
            desc: "Daily mountain walking, polyphenol-rich Cannonau red wine, and strong lifelong family integration across generations."
          },
          {
            num: "03",
            title: "NICOYA",
            country: "COSTA RICA",
            img: "{{ asset('assets/images/nicoya.jpg') }}",
            marker: "BONE DENSITY",
            desc: "Calcium-rich natural water, daily outdoor sunshine, and a clear 'Plan de Vida' purpose that drives lifelong physical vitality."
          },
          {
            num: "04",
            title: "IKARIA",
            country: "GREECE",
            img: "{{ asset('assets/images/ikaria.jpg') }}",
            marker: "VASCULAR",
            desc: "Wild mountain herbal teas, polyphenol-dense Mediterranean olive oil, stress-free pacing, and restorative afternoon rests."
          },
          {
            num: "05",
            title: "LOMA LINDA",
            country: "USA",
            img: "{{ asset('assets/images/loma_linda.jpg') }}",
            marker: "CELLULAR",
            desc: "Whole-food plant-based nutrition, regular outdoor physical exercise, and dedicated weekly Sabbath rest for systemic stress relief."
          }
        ];

        let currentIndex = 0;
        let autoplayTimer = null;

        function selectRegion(idx) {
          if (idx < 0 || idx >= REGIONS_DATA.length) return;
          currentIndex = idx;
          const r = REGIONS_DATA[idx];

          // Update Desktop Right Navigation
          const desktopBtns = document.querySelectorAll('.region-desktop-btn');
          desktopBtns.forEach((btn, i) => {
            const numSpan = btn.querySelector('span:first-child');
            const h4 = btn.querySelector('h4');
            const arrow = btn.querySelector('span:last-child');
            if (i === idx) {
              btn.className = 'region-desktop-btn w-full py-4 px-4 flex items-center justify-between text-left transition-all duration-300 group cursor-pointer border-l-4 border-[#67B34A] bg-[#67B34A]/10';
              if (numSpan) numSpan.className = 'text-xs font-extrabold text-[#67B34A] font-mono';
              if (h4) h4.className = 'text-sm font-bold text-[#67B34A] tracking-wider uppercase';
              if (arrow) arrow.className = 'text-xs font-extrabold text-[#67B34A]';
            } else {
              btn.className = 'region-desktop-btn w-full py-4 px-4 flex items-center justify-between text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5';
              if (numSpan) numSpan.className = 'text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono';
              if (h4) h4.className = 'text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]';
              if (arrow) arrow.className = 'text-xs font-bold text-[#031827]/30 dark:text-[#F6F5EF]/30 group-hover:text-[#2A8FC2]';
            }
          });

          // Update Mobile Navigation Bar
          const mobileBtns = document.querySelectorAll('.region-nav-btn');
          mobileBtns.forEach((btn, i) => {
            if (i === idx) {
              btn.className = 'region-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#67B34A] text-white';
            } else {
              btn.className = 'region-nav-btn shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]';
            }
          });

          // Update Main Display Image & Content Overlay with smooth fade
          const mainImg = document.getElementById('bz-region-active-img');
          const textPanel = document.getElementById('bz-region-text-panel');
          
          if (mainImg) {
            mainImg.style.opacity = '0.3';
            setTimeout(() => {
              mainImg.src = r.img;
              mainImg.alt = `${r.title} Blue Zone`;
              mainImg.style.opacity = '1';
            }, 200);
          }

          if (textPanel) {
            textPanel.style.opacity = '0.2';
            setTimeout(() => {
              const numEl = document.getElementById('bz-region-active-num');
              const countryEl = document.getElementById('bz-region-active-country');
              const titleEl = document.getElementById('bz-region-active-title');
              const descEl = document.getElementById('bz-region-active-desc');
              const markerEl = document.getElementById('bz-region-active-marker');
              const stepEl = document.getElementById('bz-region-curr-step');

              if (numEl) numEl.textContent = r.num;
              if (countryEl) countryEl.textContent = r.country;
              if (titleEl) titleEl.textContent = r.title;
              if (descEl) descEl.textContent = r.desc;
              if (markerEl) markerEl.textContent = r.marker;
              if (stepEl) stepEl.textContent = r.num;

              textPanel.style.opacity = '1';
            }, 200);
          }

          restartAutoplay();
        }

        function startAutoplay() {
          stopAutoplay();
          autoplayTimer = setInterval(() => {
            let nextIndex = (currentIndex + 1) % REGIONS_DATA.length;
            selectRegion(nextIndex);
          }, 5500);
        }

        function stopAutoplay() {
          if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
          }
        }

        function restartAutoplay() {
          stopAutoplay();
          autoplayTimer = setInterval(() => {
            let nextIndex = (currentIndex + 1) % REGIONS_DATA.length;
            selectRegion(nextIndex);
          }, 5500);
        }

        function initRegions() {
          startAutoplay();

          const container = document.getElementById('bz-region-interactive-container');
          if (container) {
            container.addEventListener('mouseenter', stopAutoplay);
            container.addEventListener('mouseleave', startAutoplay);
          }
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initRegions);
        } else {
          initRegions();
        }

        window.BLUEZONE_REGIONS = { select: selectRegion };
      })();
    </script>
