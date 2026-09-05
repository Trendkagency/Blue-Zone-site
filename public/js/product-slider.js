// BLUE ZONE Premium Featured Products Carousel Slider

(function () {
  const AUTOPLAY_DELAY = 5500;
  let currentIndex = 0;
  let autoplayTimer = null;
  let products = [];
  let visibleCount = 3;

  function calculateVisibleCount() {
    const width = window.innerWidth;
    if (width < 1024) return 2; // Always display 2 cards side-by-side on mobile & tablet
    return 3; // Display 3 cards on desktop
  }

  function renderProductCard(p) {
    return `
      <div class="product-slide-card flex-shrink-0 px-1 sm:px-3 transition-all duration-500 ease-out" style="width: ${100 / visibleCount}%;">
        <div onclick="window.location.href='product.html?id=${p.id}'" class="group relative bg-white dark:bg-[#062B49] rounded-2xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm hover:shadow-2xl transition-all duration-300 p-2.5 sm:p-6 flex flex-col justify-between h-full card-hover-lift img-zoom-container cursor-pointer">
          
          <div class="space-y-2 sm:space-y-4">
            <!-- Header Badges -->
            <div class="flex justify-between items-center">
              <span class="text-[8px] sm:text-[10px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">
                ${p.category}
              </span>
              <span class="text-[9px] sm:text-xs font-bold text-[#67B34A] flex items-center gap-0.5 sm:gap-1">
                <svg class="w-2.5 h-2.5 sm:w-3.5 sm:h-3.5 fill-current text-[#67B34A]" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                ${p.rating}
              </span>
            </div>

            <!-- Product Image Container -->
            <div class="aspect-square p-1.5 sm:p-4 bg-[#F6F5EF] dark:bg-[#031827] rounded-xl flex items-center justify-center relative overflow-hidden">
              <img
                src="${p.image}"
                alt="${p.name}"
                onerror="BLUEZONE_APP.handleImageFallback(this)"
                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
              />
            </div>

            <!-- Title & Short Description -->
            <div>
              <h3 class="text-xs sm:text-lg font-black text-[#031827] dark:text-[#F6F5EF] group-hover:text-[#67B34A] dark:group-hover:text-[#67B34A] transition-colors truncate">
                ${p.name}
              </h3>
              <p class="text-[9px] sm:text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 line-clamp-2 font-medium leading-tight sm:leading-relaxed mt-0.5 sm:mt-1">
                ${p.shortDesc}
              </p>
            </div>
          </div>

          <!-- Price & CTA Controls -->
          <div class="pt-2 sm:pt-6 space-y-2 sm:space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-xs sm:text-xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                $${p.price.toFixed(2)}
              </span>
              <button
                onclick="event.stopPropagation(); BLUEZONE_WISHLIST.toggle('${p.id}')"
                aria-label="Toggle wishlist"
                class="p-1 sm:p-2.5 rounded-full hover:bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] transition-colors cursor-pointer"
              >
                <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
            <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
              <button
                onclick="event.stopPropagation(); BLUEZONE_CART.add('${p.id}', 1)"
                class="w-full sm:flex-1 py-1.5 sm:py-3 bg-[#0A4F78] hover:bg-[#062B49] text-white text-[9px] sm:text-xs font-extrabold uppercase tracking-wider rounded-lg sm:rounded-xl transition-all shadow-sm hover:shadow-md btn-sheen cursor-pointer"
              >
                ADD TO CART
              </button>
              <button
                onclick="event.stopPropagation(); BLUEZONE_APP.openQuickView('${p.id}')"
                class="w-full sm:w-auto px-2 sm:px-3.5 py-1.5 sm:py-3 border border-[#0A4F78]/30 hover:border-[#0A4F78] text-[#031827] dark:text-[#F6F5EF] text-[9px] sm:text-xs font-extrabold rounded-lg sm:rounded-xl transition-colors cursor-pointer text-center whitespace-nowrap"
              >
                QUICK VIEW
              </button>
            </div>
          </div>

        </div>
      </div>
    `;
  }

  function initProductSlider() {
    const track = document.getElementById('featured-products-track');
    const container = document.getElementById('featured-products-container');
    if (!track || !window.BLUEZONE_PRODUCTS) return;

    products = window.BLUEZONE_PRODUCTS.slice(0, 6);
    visibleCount = calculateVisibleCount();

    track.innerHTML = products.map(p => renderProductCard(p)).join('');

    renderDots();
    updateSliderPosition();
    startAutoplay();

    // Responsive resize listener
    window.addEventListener('resize', () => {
      const newVisible = calculateVisibleCount();
      if (newVisible !== visibleCount) {
        visibleCount = newVisible;
        track.innerHTML = products.map(p => renderProductCard(p)).join('');
        renderDots();
        updateSliderPosition();
      }
    });

    if (container) {
      container.addEventListener('mouseenter', stopAutoplay);
      container.addEventListener('mouseleave', startAutoplay);

      // Touch swipe support for mobile
      let touchStartX = 0;
      let touchEndX = 0;

      container.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
      }, { passive: true });

      container.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
      }, { passive: true });

      function handleSwipe() {
        if (touchEndX < touchStartX - 40) {
          nextSlide();
        } else if (touchEndX > touchStartX + 40) {
          prevSlide();
        }
      }
    }
  }

  function getMaxIndex() {
    return Math.max(0, products.length - visibleCount);
  }

  function updateSliderPosition() {
    const track = document.getElementById('featured-products-track');
    if (!track) return;

    const maxIdx = getMaxIndex();
    if (currentIndex > maxIdx) currentIndex = maxIdx;
    if (currentIndex < 0) currentIndex = 0;

    const translatePct = (currentIndex * (100 / visibleCount));
    track.style.transform = `translateX(-${translatePct}%)`;

    updateDots();
  }

  function renderDots() {
    const dotsContainer = document.getElementById('featured-products-dots');
    if (!dotsContainer) return;

    const totalPages = getMaxIndex() + 1;
    let html = '';

    for (let i = 0; i < totalPages; i++) {
      const isActive = i === currentIndex;
      html += `
        <button
          onclick="BLUEZONE_PRODUCT_SLIDER.goTo(${i})"
          aria-label="Go to page ${i + 1}"
          class="${isActive ? 'w-8 bg-[#0A4F78] dark:bg-[#2A8FC2]' : 'w-2.5 bg-[#0A4F78]/30 dark:bg-white/30'} h-2.5 rounded-full transition-all duration-300 cursor-pointer"
        ></button>
      `;
    }
    dotsContainer.innerHTML = html;
  }

  function updateDots() {
    const dotsContainer = document.getElementById('featured-products-dots');
    if (!dotsContainer) return;

    const buttons = dotsContainer.querySelectorAll('button');
    buttons.forEach((btn, i) => {
      if (i === currentIndex) {
        btn.className = 'w-8 h-2.5 rounded-full bg-[#0A4F78] dark:bg-[#2A8FC2] transition-all duration-300 cursor-pointer';
      } else {
        btn.className = 'w-2.5 h-2.5 rounded-full bg-[#0A4F78]/30 dark:bg-white/30 hover:bg-[#0A4F78]/60 transition-all duration-300 cursor-pointer';
      }
    });
  }

  function nextSlide() {
    const maxIdx = getMaxIndex();
    if (currentIndex >= maxIdx) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
    updateSliderPosition();
  }

  function prevSlide() {
    const maxIdx = getMaxIndex();
    if (currentIndex <= 0) {
      currentIndex = maxIdx;
    } else {
      currentIndex--;
    }
    updateSliderPosition();
  }

  function startAutoplay() {
    stopAutoplay();
    autoplayTimer = setInterval(nextSlide, AUTOPLAY_DELAY);
  }

  function stopAutoplay() {
    if (autoplayTimer) {
      clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  window.BLUEZONE_PRODUCT_SLIDER = {
    init: initProductSlider,
    next: function () {
      stopAutoplay();
      nextSlide();
      startAutoplay();
    },
    prev: function () {
      stopAutoplay();
      prevSlide();
      startAutoplay();
    },
    goTo: function (idx) {
      stopAutoplay();
      currentIndex = idx;
      updateSliderPosition();
      startAutoplay();
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    initProductSlider();
  });
})();
