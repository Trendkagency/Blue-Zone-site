// BLUE ZONE Full-Width Cinematic Hero Image Slider

(function () {
  const AUTOPLAY_DELAY = 5500; // 5.5 seconds autoplay
  let currentIndex = 0;
  let autoplayTimer = null;
  let slides = [];
  let dots = [];

  function initHeroSlider() {
    slides = document.querySelectorAll('.hero-slide');
    dots = document.querySelectorAll('.hero-dot');
    const heroContainer = document.getElementById('hero-slider-container');

    if (!slides || slides.length === 0) return;

    showSlide(0);
    startAutoplay();

    if (heroContainer) {
      heroContainer.addEventListener('mouseenter', stopAutoplay);
      heroContainer.addEventListener('mouseleave', startAutoplay);

      // Touch swipe support for mobile
      let touchStartX = 0;
      let touchEndX = 0;

      heroContainer.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
      }, { passive: true });

      heroContainer.addEventListener('touchend', (e) => {
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

  function showSlide(index) {
    if (!slides || slides.length === 0) return;

    if (index >= slides.length) currentIndex = 0;
    else if (index < 0) currentIndex = slides.length - 1;
    else currentIndex = index;

    slides.forEach((slide, idx) => {
      if (idx === currentIndex) {
        slide.classList.remove('opacity-0', 'pointer-events-none');
        slide.classList.add('opacity-100', 'z-10');
      } else {
        slide.classList.remove('opacity-100', 'z-10');
        slide.classList.add('opacity-0', 'pointer-events-none');
      }
    });

    dots.forEach((dot, idx) => {
      const indicator = dot.querySelector('.hero-dot-indicator') || dot;
      if (idx === currentIndex) {
        indicator.className = 'hero-dot-indicator w-8 h-2 rounded-full bg-[#2A8FC2] transition-all duration-300 block pointer-events-none';
      } else {
        indicator.className = 'hero-dot-indicator w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all duration-300 block pointer-events-none';
      }
    });
  }

  function nextSlide() {
    showSlide(currentIndex + 1);
  }

  function prevSlide() {
    showSlide(currentIndex - 1);
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

  window.BLUEZONE_HERO = {
    init: initHeroSlider,
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
    goTo: function (index) {
      stopAutoplay();
      showSlide(index);
      startAutoplay();
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    initHeroSlider();
  });
})();
