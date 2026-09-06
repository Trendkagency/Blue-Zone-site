    <!-- 05. NEW ARRIVALS SLIDER (CAROUSEL) -->
    <section id="new-arrivals" class="py-20 bg-[#F6F5EF] dark:bg-[#031827] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
          <div class="space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-xs font-black uppercase tracking-widest border border-[#67B34A]/30">
              <span class="w-2 h-2 rounded-full bg-[#67B34A] animate-ping"></span>
              {{ app()->getLocale() === 'ar' ? 'وصل حديثاً' : 'NEW ARRIVALS' }}
            </div>
            <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
              {{ app()->getLocale() === 'ar' ? 'أحدث المنتجات والابتكارات' : 'NEW ARRIVALS & INNOVATIONS' }}
            </h2>
            <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium max-w-xl">
              {{ app()->getLocale() === 'ar' ? 'استكشف أحدث تركيبات طول العمر الخلوي المصممة وفق أعلى المعايير الصيدلانية السريرية.' : 'Explore our newest bio-identical formulations engineered for cellular longevity, energy synthesis, and biological resilience.' }}
            </p>
          </div>
          
          <div class="flex items-center gap-3">
            <button onclick="BLUEZONE_NEW_ARRIVALS.prev()" aria-label="Previous new arrivals" class="p-3.5 rounded-full bg-white dark:bg-[#062B49] hover:bg-[#67B34A] hover:text-white text-[#0A4F78] dark:text-[#2A8FC2] shadow-md border border-[#0A4F78]/15 transition-all cursor-pointer hover:scale-105">
              ‹
            </button>
            <button onclick="BLUEZONE_NEW_ARRIVALS.next()" aria-label="Next new arrivals" class="p-3.5 rounded-full bg-white dark:bg-[#062B49] hover:bg-[#67B34A] hover:text-white text-[#0A4F78] dark:text-[#2A8FC2] shadow-md border border-[#0A4F78]/15 transition-all cursor-pointer hover:scale-105">
              ›
            </button>
          </div>
        </div>

        <div id="new-arrivals-carousel" class="relative overflow-hidden py-2">
          <div id="new-arrivals-track" class="flex transition-transform duration-500 ease-out gap-6">
            @foreach($newArrivals ?? [] as $product)
              <div class="new-arrival-card flex-shrink-0 w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)] group">
                <div class="h-full bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm hover:shadow-2xl transition-all duration-300 p-6 flex flex-col justify-between card-hover-lift">
                  <div class="space-y-4">
                    <!-- Top badges -->
                    <div class="flex items-center justify-between">
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-[10px] font-black uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#67B34A]"></span>
                        {{ app()->getLocale() === 'ar' ? 'جديد' : 'NEW' }}
                      </span>
                      <span class="text-xs font-bold text-[#67B34A] flex items-center gap-1">
                        ★ {{ number_format($product['rating'] ?? 4.9, 1) }}
                      </span>
                    </div>

                    <!-- Image with link -->
                    <a href="{{ route('customer.product.show', $product['slug']) }}" class="block aspect-square p-4 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl relative overflow-hidden group-hover:border-[#67B34A]/40 border border-transparent transition-colors">
                      <img
                        src="{{ asset('assets/products/' . $product['slug'] . '.webp') }}"
                        alt="{{ $product['name_en'] }}"
                        onerror="this.onerror=null; this.src='{{ asset('assets/products/' . $product['slug'] . '.jpg') }}';"
                        width="300" height="300"
                        loading="lazy" decoding="async"
                        class="w-full h-full object-contain group-hover:scale-108 transition-transform duration-500"
                      />
                    </a>

                    <!-- Details -->
                    <div class="space-y-1.5">
                      <span class="text-[10px] font-mono font-bold uppercase text-[#2A8FC2] tracking-wider block">
                        {{ $product['category_en'] ?? 'Cellular Formula' }}
                      </span>
                      <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF] group-hover:text-[#67B34A] transition-colors">
                        <a href="{{ route('customer.product.show', $product['slug']) }}">
                          {{ app()->getLocale() === 'ar' ? ($product['name_ar'] ?? $product['name_en']) : $product['name_en'] }}
                        </a>
                      </h3>
                      <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 line-clamp-2 leading-relaxed">
                        {{ app()->getLocale() === 'ar' ? ($product['short_description_ar'] ?? $product['short_description_en']) : $product['short_description_en'] }}
                      </p>
                    </div>

                    <!-- Bioactive ingredient pills -->
                    @if(!empty($product['ingredients']))
                      <div class="flex flex-wrap gap-1.5 pt-1">
                        @foreach(array_slice($product['ingredients'], 0, 2) as $ing)
                          <span class="text-[9px] font-bold px-2 py-0.5 rounded-md bg-[#0A4F78]/5 dark:bg-[#0A4F78]/30 text-[#0A4F78] dark:text-[#2A8FC2]">
                            {{ $ing['name_en'] }} ({{ $ing['dose'] }})
                          </span>
                        @endforeach
                      </div>
                    @endif
                  </div>

                  <!-- Price and Action -->
                  <div class="pt-6 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 mt-4 space-y-3">
                    <div class="flex items-baseline justify-between">
                      <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                          ${{ number_format($product['sale_price'] ?? $product['price'], 2) }}
                        </span>
                        @if(!empty($product['sale_price']) && $product['sale_price'] < $product['price'])
                          <span class="text-xs text-slate-400 line-through">
                            ${{ number_format($product['price'], 2) }}
                          </span>
                        @endif
                      </div>
                      <span class="text-[10px] font-bold text-[#67B34A]">
                        ✓ {{ app()->getLocale() === 'ar' ? 'متوفر' : 'In Stock' }}
                      </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                      <a href="{{ route('customer.product.show', $product['slug']) }}" class="px-3 py-2.5 text-center text-xs font-bold rounded-xl border border-[#0A4F78]/30 hover:border-[#0A4F78] text-[#031827] dark:text-[#F6F5EF] hover:bg-[#0A4F78]/5 transition-colors">
                        {{ app()->getLocale() === 'ar' ? 'التفاصيل' : 'Details' }}
                      </a>
                      <button
                        onclick="if(window.BLUEZONE_CART){window.BLUEZONE_CART.addItem({{ $product['id'] }}, '{{ addslashes($product['name_en']) }}', {{ $product['sale_price'] ?? $product['price'] }}, '{{ asset('assets/products/' . $product['slug'] . '.jpg') }}', 1);}"
                        class="px-3 py-2.5 text-center text-xs font-black uppercase tracking-wider rounded-xl bg-[#67B34A] hover:bg-[#589c3e] text-white transition-all shadow-md hover:scale-102 flex items-center justify-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>{{ app()->getLocale() === 'ar' ? 'أضف' : 'Add' }}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Slider Pagination Dots -->
        <div id="new-arrivals-dots" class="flex justify-center items-center gap-2 pt-2"></div>
      </div>
    </section>

    <!-- Inline Script for New Arrivals Slider -->
    <script>
      (function() {
        let currentIdx = 0;
        const track = document.getElementById('new-arrivals-track');
        const cards = document.querySelectorAll('.new-arrival-card');
        const dotsContainer = document.getElementById('new-arrivals-dots');
        if (!track || !cards.length) return;

        function getVisibleCards() {
          const w = window.innerWidth;
          if (w >= 1280) return 4;
          if (w >= 1024) return 3;
          if (w >= 640) return 2;
          return 1;
        }

        function maxIndex() {
          return Math.max(0, cards.length - getVisibleCards());
        }

        function renderDots() {
          if (!dotsContainer) return;
          const total = maxIndex() + 1;
          dotsContainer.innerHTML = '';
          if (total <= 1) return;
          for (let i = 0; i < total; i++) {
            const dot = document.createElement('button');
            dot.className = `w-2.5 h-2.5 rounded-full transition-all duration-300 ${i === currentIdx ? 'bg-[#67B34A] w-6' : 'bg-slate-300 dark:bg-slate-700'}`;
            dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
            dot.onclick = () => goTo(i);
            dotsContainer.appendChild(dot);
          }
        }

        function updateSlider() {
          const visible = getVisibleCards();
          const cardEl = cards[0];
          const cardWidth = cardEl ? cardEl.getBoundingClientRect().width + 24 : 0;
          const offset = currentIdx * cardWidth;
          const isRtl = document.documentElement.dir === 'rtl';
          track.style.transform = isRtl ? `translateX(${offset}px)` : `translateX(-${offset}px)`;
          renderDots();
        }

        function next() {
          if (currentIdx < maxIndex()) {
            currentIdx++;
          } else {
            currentIdx = 0;
          }
          updateSlider();
        }

        function prev() {
          if (currentIdx > 0) {
            currentIdx--;
          } else {
            currentIdx = maxIndex();
          }
          updateSlider();
        }

        function goTo(i) {
          currentIdx = Math.max(0, Math.min(i, maxIndex()));
          updateSlider();
        }

        window.BLUEZONE_NEW_ARRIVALS = { next, prev, goTo };
        window.addEventListener('resize', () => {
          if (currentIdx > maxIndex()) currentIdx = maxIndex();
          updateSlider();
        });
        renderDots();
      })();
    </script>
