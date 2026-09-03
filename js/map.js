// BLUE ZONE Authentic World Map Animation Controller

(function () {
  const FADE_DURATION = 600;

  function initMapIntro() {
    const loaderEl = document.getElementById('blue-zone-loader');
    if (!loaderEl) return;

    // Lock document scroll
    document.documentElement.classList.add('bz-loader-active');
    document.body.classList.add('bz-loader-active');
    try { window.scrollTo(0, 0); } catch (e) {}

    // Ensure loader is visible
    loaderEl.style.transition = 'none';
    loaderEl.style.display = 'flex';
    loaderEl.style.opacity = '1';
    loaderEl.style.visibility = 'visible';
    loaderEl.style.pointerEvents = 'auto';

    const isCompact = loaderEl.classList.contains('compact-loader');
    const totalDuration = isCompact ? 2000 : 3800;

    // Active Zone Cycling
    const zoneSpans = loaderEl.querySelectorAll('.zones span:not(.sep)');
    let activeIdx = 0;
    const cycleInterval = setInterval(() => {
      if (zoneSpans.length > 0) {
        zoneSpans.forEach(s => s.classList.remove('active'));
        if (zoneSpans[activeIdx]) zoneSpans[activeIdx].classList.add('active');
        activeIdx = (activeIdx + 1) % zoneSpans.length;
      }
    }, isCompact ? 350 : 700);

    function finishIntro() {
      clearInterval(cycleInterval);
      loaderEl.style.pointerEvents = 'none';
      loaderEl.classList.add('is-loaded');
      document.documentElement.classList.remove('bz-loader-active');
      document.body.classList.remove('bz-loader-active');

      loaderEl.style.transition = `opacity ${FADE_DURATION}ms cubic-bezier(0.16, 1, 0.3, 1), visibility ${FADE_DURATION}ms ease`;
      loaderEl.style.opacity = '0';
      loaderEl.style.visibility = 'hidden';

      setTimeout(() => {
        loaderEl.style.display = 'none';
        if (loaderEl.parentNode) {
          loaderEl.parentNode.removeChild(loaderEl);
        }
      }, FADE_DURATION);
    }

    // Skip button handler
    const skipBtn = document.getElementById('skip-intro-btn') || loaderEl.querySelector('.skip');
    if (skipBtn) {
      skipBtn.onclick = function (e) {
        if (e) e.preventDefault();
        finishIntro();
      };
    }

    // Auto finish after timer
    setTimeout(() => {
      finishIntro();
    }, totalDuration);
  }

  window.BLUEZONE_MAP = {
    init: initMapIntro,
    replay: function () {
      initMapIntro();
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    initMapIntro();
  });
})();
