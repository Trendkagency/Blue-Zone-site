{{-- <section class="space-y-8 py-6 -my-2 container pt-4" id="bz-clinical-formulations-section">
  <!-- Section Header with View Mode Switcher and Swapper Arrows -->
  <div
    class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-4 border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
    <div class="space-y-2 max-w-2xl">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-[#67B34A] animate-pulse"></span>
        <span class="text-[10px] font-extrabold uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">
          {{ app()->getLocale() === 'ar' ? 'علم الصيدلة الإكلينيكية والتركيبات' : 'CLINICAL PHARMACOLOGY & FORMULATIONS' }}
        </span>
      </div>
      <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-[#031827] dark:text-[#F6F5EF]">
        {{ app()->getLocale() === 'ar' ? 'جميع التركيبات الطبية والملفات السريرية' : 'ALL LONGEVITY FORMULATIONS & MEDICAL DATA' }}
      </h2>
      <p class="text-xs sm:text-sm text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium leading-relaxed">
        {{ app()->getLocale() === 'ar'
  ? 'ملف طبي إكلينيكي مفصل لكل منتج: المسار الحيوي الخلوي، المكونات المعايرة، المؤشرات الحيوية، وبروتوكول الاستخدام الطبي.'
  : 'Complete clinical dossiers for all formulations: biochemical pathways of action, standardized active ingredients, target biomarkers, and dosage protocols.' }}
      </p>
    </div>

    <!-- Controls: Swapper Navigation Arrows & Card Grade / Slider Toggle -->
    <div class="flex items-center gap-3 shrink-0 self-start md:self-end">
      <!-- View Mode Switcher -->
      <div class="inline-flex p-1 rounded-xl bg-[#0A4F78]/10 dark:bg-[#062B49] border border-[#0A4F78]/20">
        <button type="button" id="bz-view-slider-btn" onclick="BLUEZONE_MED_SWAPPER.setView('slider')"
          class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 bg-[#0A4F78] text-white shadow-sm"
          title="{{ app()->getLocale() === 'ar' ? 'سلايدر العرض التفاعلي' : 'Slider Swapper View' }}">
          <i class="fa-solid fa-sliders"></i>
          <span class="hidden sm:inline">{{ app()->getLocale() === 'ar' ? 'سلايدر تفاعلي' : 'Slider Swapper' }}</span>
        </button>
        <button type="button" id="bz-view-grid-btn" onclick="BLUEZONE_MED_SWAPPER.setView('grid')"
          class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 text-[#031827]/70 dark:text-[#F6F5EF]/70 hover:text-[#0A4F78] dark:hover:text-[#2A8FC2]"
          title="{{ app()->getLocale() === 'ar' ? 'شبكة البطاقات' : 'Card Grade View' }}">
          <i class="fa-solid fa-table-cells-large"></i>
          <span class="hidden sm:inline">{{ app()->getLocale() === 'ar' ? 'شبكة البطاقات' : 'Card Grade' }}</span>
        </button>
      </div>

      <!-- Swapper Navigation Arrows -->
      <div id="bz-swapper-arrows" class="flex items-center gap-2">
        <button type="button" onclick="BLUEZONE_MED_SWAPPER.prev()"
          aria-label="{{ app()->getLocale() === 'ar' ? 'السابق' : 'Previous' }}"
          class="w-10 h-10 rounded-xl bg-white dark:bg-[#062B49] hover:bg-[#0A4F78] hover:text-white dark:hover:bg-[#2A8FC2] dark:hover:text-[#031827] text-[#031827] dark:text-white border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 shadow-sm transition-all flex items-center justify-center cursor-pointer hover:scale-105 active:scale-95">
          <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }} text-sm"></i>
        </button>
        <button type="button" onclick="BLUEZONE_MED_SWAPPER.next()"
          aria-label="{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}"
          class="w-10 h-10 rounded-xl bg-white dark:bg-[#062B49] hover:bg-[#0A4F78] hover:text-white dark:hover:bg-[#2A8FC2] dark:hover:text-[#031827] text-[#031827] dark:text-white border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 shadow-sm transition-all flex items-center justify-center cursor-pointer hover:scale-105 active:scale-95">
          <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }} text-sm"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- 1. SLIDER SWAPPER VIEW (Default Active) -->
  <div id="bz-swapper-container" class="relative w-full space-y-6">
    <div id="bz-swapper-viewport" class="overflow-hidden relative w-full py-2 -my-2 select-none">
      <div id="bz-swapper-track" class="flex transition-transform duration-500 ease-out gap-6"
        style="transform: translateX(0px);">
        @foreach($products as $product)
          <div class="bz-swapper-slide w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] shrink-0">
            @include('customer.pages.partials.medical-product-card', ['product' => $product, 'mode' => 'slider'])
          </div>
        @endforeach
      </div>
    </div>

    <!-- Swapper Pagination Dots -->
    <div id="bz-swapper-dots" class="flex items-center justify-center gap-2 pt-2">
      @foreach($products as $idx => $p)
        <button type="button" onclick="BLUEZONE_MED_SWAPPER.goTo({{ $idx }})" aria-label="Go to product {{ $idx + 1 }}"
          class="bz-dot-indicator h-2.5 rounded-full transition-all duration-300 cursor-pointer {{ $idx === 0 ? 'w-8 bg-[#67B34A]' : 'w-2.5 bg-[#0A4F78]/20 dark:bg-white/20' }}"
          data-index="{{ $idx }}">
        </button>
      @endforeach
    </div>
  </div>

  <!-- 2. CARD GRADE (GRID) VIEW (Toggleable) -->
  <div id="bz-grid-container" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
    @foreach($products as $product)
      <div class="h-full">
        @include('customer.pages.partials.medical-product-card', ['product' => $product, 'mode' => 'grid'])
      </div>
    @endforeach
  </div>
</section>


<!-- Science Controller Script -->
<script>
  (function () {
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
        img: "{{ asset('assets/products/blue-mind.webp') }}",
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

  // Clinical Formulations Medical Swapper / Slider Controller
  (function () {
    let currentIdx = 0;
    let currentView = 'slider';
    let startX = 0;
    let isDragging = false;
    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

    const track = document.getElementById('bz-swapper-track');
    const slides = document.querySelectorAll('.bz-swapper-slide');
    const dots = document.querySelectorAll('#bz-swapper-dots .bz-dot-indicator');
    const totalSlides = slides.length;

    function getVisibleCount() {
      const w = window.innerWidth;
      if (w < 640) return 1;
      if (w < 1024) return 2;
      return 3;
    }

    function getMaxIndex() {
      const visible = getVisibleCount();
      return Math.max(0, totalSlides - visible);
    }

    function updateSwapper(smooth = true) {
      if (!track || slides.length === 0) return;
      const maxIdx = getMaxIndex();
      if (currentIdx > maxIdx) currentIdx = maxIdx;
      if (currentIdx < 0) currentIdx = 0;

      const slideWidth = slides[0].getBoundingClientRect().width;
      const gap = 24; // 1.5rem (gap-6)
      const offset = currentIdx * (slideWidth + gap);

      track.style.transition = smooth ? 'transform 0.45s cubic-bezier(0.16, 1, 0.3, 1)' : 'none';
      track.style.transform = isRtl ? `translateX(${offset}px)` : `translateX(-${offset}px)`;

      dots.forEach((dot, i) => {
        if (i === currentIdx) {
          dot.className = 'bz-dot-indicator h-2.5 rounded-full transition-all duration-300 cursor-pointer w-8 bg-[#67B34A]';
        } else {
          dot.className = 'bz-dot-indicator h-2.5 rounded-full transition-all duration-300 cursor-pointer w-2.5 bg-[#0A4F78]/20 dark:bg-white/20';
        }
      });
    }

    function nextSlide() {
      const maxIdx = getMaxIndex();
      if (currentIdx < maxIdx) {
        currentIdx++;
      } else {
        currentIdx = 0;
      }
      updateSwapper();
    }

    function prevSlide() {
      const maxIdx = getMaxIndex();
      if (currentIdx > 0) {
        currentIdx--;
      } else {
        currentIdx = maxIdx;
      }
      updateSwapper();
    }

    function goToSlide(idx) {
      const maxIdx = getMaxIndex();
      currentIdx = Math.min(Math.max(0, idx), maxIdx);
      updateSwapper();
    }

    function setView(view) {
      currentView = view;
      const swapperContainer = document.getElementById('bz-swapper-container');
      const gridContainer = document.getElementById('bz-grid-container');
      const sliderBtn = document.getElementById('bz-view-slider-btn');
      const gridBtn = document.getElementById('bz-view-grid-btn');
      const arrows = document.getElementById('bz-swapper-arrows');

      if (view === 'grid') {
        if (swapperContainer) swapperContainer.classList.add('hidden');
        if (gridContainer) gridContainer.classList.remove('hidden');
        if (arrows) arrows.classList.add('hidden');

        if (gridBtn) {
          gridBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 bg-[#0A4F78] text-white shadow-sm';
        }
        if (sliderBtn) {
          sliderBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 text-[#031827]/70 dark:text-[#F6F5EF]/70 hover:text-[#0A4F78] dark:hover:text-[#2A8FC2]';
        }
      } else {
        if (swapperContainer) swapperContainer.classList.remove('hidden');
        if (gridContainer) gridContainer.classList.add('hidden');
        if (arrows) arrows.classList.remove('hidden');

        if (sliderBtn) {
          sliderBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 bg-[#0A4F78] text-white shadow-sm';
        }
        if (gridBtn) {
          gridBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 text-[#031827]/70 dark:text-[#F6F5EF]/70 hover:text-[#0A4F78] dark:hover:text-[#2A8FC2]';
        }
        updateSwapper(false);
      }
    }

    // Touch swipe support
    const viewport = document.getElementById('bz-swapper-viewport');
    if (viewport) {
      viewport.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
      }, { passive: true });

      viewport.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        isDragging = false;
        const endX = e.changedTouches[0].clientX;
        const diff = endX - startX;
        if (Math.abs(diff) > 40) {
          if (isRtl) {
            if (diff > 0) nextSlide();
            else prevSlide();
          } else {
            if (diff < 0) nextSlide();
            else prevSlide();
          }
        }
      }, { passive: true });
    }

    window.addEventListener('resize', () => {
      if (currentView === 'slider') updateSwapper(false);
    });

    // Global export
    window.BLUEZONE_MED_SWAPPER = {
      next: nextSlide,
      prev: prevSlide,
      goTo: goToSlide,
      setView: setView,
      refresh: () => updateSwapper(false)
    };

    // Initialize swapper positioning
    setTimeout(() => updateSwapper(false), 50);
  })();
</script> --}}