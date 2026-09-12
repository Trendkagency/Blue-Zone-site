@php
    $rawArrivals = $newArrivals ?? [];
    if (is_object($rawArrivals) && method_exists($rawArrivals, 'all')) {
        $rawArrivals = $rawArrivals->all();
    } elseif (!is_array($rawArrivals)) {
        $rawArrivals = (array) $rawArrivals;
    }

    // Swiper requires at least slidesPerView * 2 (>= 8 slides) for seamless, non-stop infinite loop
    $carouselArrivals = $rawArrivals;
    if (count($rawArrivals) > 0 && count($rawArrivals) < 8) {
        $multiplier = (int) ceil(8 / count($rawArrivals));
        $carouselArrivals = [];
        for ($m = 0; $m < $multiplier; $m++) {
            $carouselArrivals = array_merge($carouselArrivals, $rawArrivals);
        }
    }
@endphp

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
        <button id="new-arrivals-prev" onclick="if(window.BLUEZONE_NEW_ARRIVALS){window.BLUEZONE_NEW_ARRIVALS.prev();}" aria-label="Previous new arrivals" class="p-3.5 rounded-full bg-white dark:bg-[#062B49] hover:bg-[#67B34A] hover:text-white text-[#0A4F78] dark:text-[#2A8FC2] shadow-md border border-[#0A4F78]/15 transition-all cursor-pointer hover:scale-105 flex items-center justify-center w-11 h-11 active:scale-95">
          <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button id="new-arrivals-next" onclick="if(window.BLUEZONE_NEW_ARRIVALS){window.BLUEZONE_NEW_ARRIVALS.next();}" aria-label="Next new arrivals" class="p-3.5 rounded-full bg-white dark:bg-[#062B49] hover:bg-[#67B34A] hover:text-white text-[#0A4F78] dark:text-[#2A8FC2] shadow-md border border-[#0A4F78]/15 transition-all cursor-pointer hover:scale-105 flex items-center justify-center w-11 h-11 active:scale-95">
          <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>

    <!-- Swiper Infinite Mouse-Draggable Carousel Container -->
    <div id="new-arrivals-swiper" class="swiper new-arrivals-swiper relative overflow-hidden py-4 -my-4 px-1 cursor-grab active:cursor-grabbing">
      <div class="swiper-wrapper">
        @foreach($carouselArrivals as $product)
          @php
            $pId = $product['id'] ?? ($product->id ?? 0);
            $pSlug = $product['slug'] ?? ($product->slug ?? '');
            $pNameEn = $product['name_en'] ?? ($product->name_en ?? '');
            $pNameAr = $product['name_ar'] ?? ($product->name_ar ?? $pNameEn);
            $pName = app()->getLocale() === 'ar' ? $pNameAr : $pNameEn;
            $pCategory = $product['category']['name_en'] ?? ($product['category_en'] ?? ($product->category->name_en ?? 'Cellular Formula'));
            $pRating = $product['rating'] ?? ($product->rating ?? 4.9);
            $pPrice = $product['price'] ?? ($product->price ?? 0);
            $pSalePrice = $product['sale_price'] ?? ($product->sale_price ?? null);
            $effectivePrice = !empty($pSalePrice) ? $pSalePrice : $pPrice;
            $pShortDesc = app()->getLocale() === 'ar'
                ? ($product['short_description_ar'] ?? ($product->short_description_ar ?? ($product['short_description_en'] ?? ($product->short_description_en ?? ''))))
                : ($product['short_description_en'] ?? ($product->short_description_en ?? ''));
            $pIngredients = $product['ingredients'] ?? ($product->ingredients ?? []);
          @endphp
          <div class="swiper-slide h-auto group">
            <div class="w-full h-full bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm hover:shadow-2xl transition-all duration-300 p-6 flex flex-col justify-between card-hover-lift select-none">
              <div class="space-y-4">
                <!-- Top badges -->
                <div class="flex items-center justify-between">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-[10px] font-black uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#67B34A]"></span>
                    {{ app()->getLocale() === 'ar' ? 'جديد' : 'NEW' }}
                  </span>
                  <span class="text-xs font-bold text-[#67B34A] flex items-center gap-1">
                    <i class="fa-solid fa-star text-amber-400"></i> {{ number_format((float)$pRating, 1) }}
                  </span>
                </div>

                <!-- Image with link -->
                <a href="{{ route('customer.product.show', $pSlug) }}" class="block aspect-square rounded-2xl relative overflow-hidden bg-[#031827] border border-[#0A4F78]/10 group-hover:border-[#67B34A]/50 transition-all duration-500 shadow-sm">
                  <img
                    src="{{ asset('assets/products/' . $pSlug . '.webp') }}"
                    alt="{{ $pNameEn }}"
                    onerror="this.onerror=null; this.src='{{ asset('assets/products/' . $pSlug . '.jpg') }}';"
                    width="400" height="400"
                    loading="lazy" decoding="async"
                    class="w-full h-full object-cover object-center group-hover:scale-108 transition-transform duration-700 ease-out pointer-events-none"
                  />
                  <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#031827]/40 via-transparent to-transparent"></div>
                  <div class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-white/10 rounded-2xl"></div>
                </a>

                <!-- Details -->
                <div class="space-y-1.5">
                  <span class="text-[10px] font-mono font-bold uppercase text-[#2A8FC2] tracking-wider block">
                    {{ $pCategory }}
                  </span>
                  <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF] group-hover:text-[#67B34A] transition-colors">
                    <a href="{{ route('customer.product.show', $pSlug) }}">
                      {{ $pName }}
                    </a>
                  </h3>
                  <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 line-clamp-2 leading-relaxed">
                    {{ $pShortDesc }}
                  </p>
                </div>

                <!-- Bioactive ingredient pills -->
                @if(!empty($pIngredients) && is_array($pIngredients))
                  <div class="flex flex-wrap gap-1.5 pt-1">
                    @foreach(array_slice($pIngredients, 0, 2) as $ing)
                      <span class="text-[9px] font-bold px-2 py-0.5 rounded-md bg-[#0A4F78]/5 dark:bg-[#0A4F78]/30 text-[#0A4F78] dark:text-[#2A8FC2]">
                        {{ is_array($ing) ? ($ing['name_en'] ?? '') . ' (' . ($ing['dose'] ?? '') . ')' : $ing }}
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
                      @currency($effectivePrice)
                    </span>
                    @if(!empty($pSalePrice) && $pSalePrice < $pPrice)
                      <span class="text-xs text-slate-400 line-through">
                        @currency($pPrice)
                      </span>
                    @endif
                  </div>
                  <span class="text-[10px] font-bold text-[#67B34A]">
                    <i class="fa-solid fa-circle-check text-[#67B34A] mr-1"></i> {{ app()->getLocale() === 'ar' ? 'متوفر' : 'In Stock' }}
                  </span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                  <a href="{{ route('customer.product.show', $pSlug) }}" class="px-3 py-2.5 text-center text-xs font-bold rounded-xl border border-[#0A4F78]/30 hover:border-[#0A4F78] text-[#031827] dark:text-[#F6F5EF] hover:bg-[#0A4F78]/5 transition-colors">
                    {{ app()->getLocale() === 'ar' ? 'التفاصيل' : 'Details' }}
                  </a>
                  <button
                    type="button"
                    onclick="if(window.BLUEZONE_CART){window.BLUEZONE_CART.addItem({{ $pId }}, '{{ addslashes($pNameEn) }}', {{ $effectivePrice }}, '{{ asset('assets/products/' . $pSlug . '.jpg') }}', 1);}"
                    class="px-3 py-2.5 text-center text-xs font-black uppercase tracking-wider rounded-xl bg-[#67B34A] hover:bg-[#589c3e] text-white transition-all shadow-md hover:scale-102 flex items-center justify-center gap-1 cursor-pointer active:scale-95">
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
    <div id="new-arrivals-dots" class="flex justify-center items-center gap-2 pt-4"></div>
  </div>
</section>

<!-- Scoped Styles for New Arrivals Swiper -->
<style>
  .new-arrivals-swiper {
    user-select: none;
    -webkit-user-select: none;
    padding-bottom: 8px !important;
  }
  .new-arrivals-swiper img {
    -webkit-user-drag: none;
    user-drag: none;
  }
  .new-arrivals-swiper .swiper-slide {
    height: auto !important;
    display: flex;
    box-sizing: border-box;
  }
  .bz-swiper-bullet {
    width: 10px;
    height: 10px;
    border-radius: 9999px;
    background-color: #cbd5e1;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: none;
    padding: 0;
    margin: 0 4px;
    outline: none;
    display: inline-block;
  }
  .dark .bz-swiper-bullet {
    background-color: #334155;
  }
  .bz-swiper-bullet-active {
    width: 28px !important;
    background-color: #67B34A !important;
    border-radius: 9999px !important;
    box-shadow: 0 2px 8px rgba(103, 179, 74, 0.4);
  }
</style>

<!-- Script for Infinite Mouse-Draggable New Arrivals Slider -->
<script>
  (function() {
    function initSlider() {
      const swiperContainer = document.getElementById('new-arrivals-swiper');
      if (!swiperContainer) return;

      // Clean up previous instance if reloading / re-rendering
      if (window.__bzNewArrivalsSwiper && typeof window.__bzNewArrivalsSwiper.destroy === 'function') {
        try {
          window.__bzNewArrivalsSwiper.destroy(true, true);
        } catch(e) {}
      }

      window.__bzNewArrivalsSwiper = new Swiper('#new-arrivals-swiper', {
        loop: true,
        grabCursor: true,
        simulateTouch: true,
        touchRatio: 1.15,
        touchAngle: 45,
        threshold: 3,
        speed: 550,
        spaceBetween: 24,
        slidesPerView: 1,
        watchSlidesProgress: true,
        preventClicks: true,
        preventClicksPropagation: true,
        slideToClickedSlide: false,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        },
        keyboard: {
          enabled: true,
          onlyInViewport: true,
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
            spaceBetween: 20,
          },
          1024: {
            slidesPerView: 3,
            spaceBetween: 24,
          },
          1280: {
            slidesPerView: 4,
            spaceBetween: 24,
          },
        },
        navigation: {
          nextEl: '#new-arrivals-next',
          prevEl: '#new-arrivals-prev',
        },
        pagination: {
          el: '#new-arrivals-dots',
          clickable: true,
          bulletClass: 'bz-swiper-bullet',
          bulletActiveClass: 'bz-swiper-bullet-active',
          renderBullet: function (index, className) {
            return '<button type="button" class="' + className + '" aria-label="Slide ' + (index + 1) + '"></button>';
          }
        },
      });

      // Global API for backwards compatibility
      window.BLUEZONE_NEW_ARRIVALS = {
        next: function() {
          if (window.__bzNewArrivalsSwiper) window.__bzNewArrivalsSwiper.slideNext();
        },
        prev: function() {
          if (window.__bzNewArrivalsSwiper) window.__bzNewArrivalsSwiper.slidePrev();
        },
        goTo: function(idx) {
          if (window.__bzNewArrivalsSwiper) window.__bzNewArrivalsSwiper.slideToLoop(idx);
        }
      };
    }

    if (typeof Swiper !== 'undefined') {
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSlider);
      } else {
        initSlider();
      }
    } else {
      // Dynamic fallback loader if Swiper script was deferred or not yet loaded
      const script = document.createElement('script');
      script.src = "{{ asset('vendor/swiper/swiper-bundle.min.js') }}";
      script.onload = initSlider;
      document.head.appendChild(script);

      // Also ensure CSS is present
      if (!document.querySelector('link[href*="swiper-bundle"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = "{{ asset('vendor/swiper/swiper-bundle.min.css') }}";
        document.head.appendChild(link);
      }
    }
  })();
</script>
