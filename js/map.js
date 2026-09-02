// BLUE ZONE Cinematic World Map Animation Controller

(function () {
  const INTRO_KEY = 'bluezone_intro_completed';
  const MIN_DURATION = 6000;
  const FADE_DURATION = 800;

  const BLUE_ZONES = [
    { id: 'okinawa', name: 'OKINAWA', country: 'JAPAN' },
    { id: 'sardinia', name: 'SARDINIA', country: 'ITALY' },
    { id: 'nicoya', name: 'NICOYA', country: 'COSTA RICA' },
    { id: 'ikaria', name: 'IKARIA', country: 'GREECE' },
    { id: 'loma-linda', name: 'LOMA LINDA', country: 'USA' }
  ];

  function initMapIntro(forceReplay = true) {
    const loaderEl = document.getElementById('blue-zone-loader') || document.getElementById('cinematic-map-loader');
    if (!loaderEl) return;

    // Lock loader visible at 100% opacity
    loaderEl.style.display = 'flex';
    loaderEl.style.opacity = '1';
    loaderEl.style.visibility = 'visible';

    const textEl = document.getElementById('map-intro-text');
    const progressBar = document.getElementById('map-intro-progress');
    const linesGroup = document.getElementById('map-lines-group');

    // Reset initial element states (Hidden opacity for animation)
    BLUE_ZONES.forEach(zone => {
      const marker = document.getElementById(`marker-${zone.id}`);
      const label = document.getElementById(`label-${zone.id}`);
      if (marker) {
        marker.style.opacity = '0';
        marker.style.transition = 'opacity 350ms ease';
      }
      if (label) {
        label.style.opacity = '0';
        label.style.transition = 'opacity 350ms ease';
      }
    });

    if (linesGroup) {
      linesGroup.style.opacity = '0';
      linesGroup.style.transition = 'opacity 500ms ease';
    }

    const timers = [];

    function updateStepText(text, progressPct) {
      if (textEl) textEl.textContent = text;
      if (progressBar) progressBar.style.width = `${progressPct}%`;
    }

    // Timeline Sequence (Total 6.0s + 0.8s Fade Out)
    
    // 0.0s – 0.8s: World Map Fade In. Text: DISCOVERING THE WORLD
    updateStepText("DISCOVERING THE WORLD", 15);

    // 0.8s – 1.5s: OKINAWA — JAPAN (Marker at 0.8s, Label at 1.0s)
    timers.push(setTimeout(() => {
      showMarker('okinawa');
      updateStepText("LOCATING THE BLUE ZONES", 30);
    }, 800));
    timers.push(setTimeout(() => {
      showLabel('okinawa');
    }, 1000));

    // 1.5s – 2.2s: SARDINIA — ITALY (Marker at 1.5s, Label at 1.7s)
    timers.push(setTimeout(() => {
      showMarker('sardinia');
      updateStepText("LOCATING THE BLUE ZONES", 45);
    }, 1500));
    timers.push(setTimeout(() => {
      showLabel('sardinia');
    }, 1700));

    // 2.2s – 2.9s: NICOYA — COSTA RICA (Marker at 2.2s, Label at 2.4s)
    timers.push(setTimeout(() => {
      showMarker('nicoya');
      updateStepText("LOCATING THE BLUE ZONES", 60);
    }, 2200));
    timers.push(setTimeout(() => {
      showLabel('nicoya');
    }, 2400));

    // 2.9s – 3.6s: IKARIA — GREECE (Marker at 2.9s, Label at 3.1s)
    timers.push(setTimeout(() => {
      showMarker('ikaria');
      updateStepText("LOCATING THE BLUE ZONES", 75);
    }, 2900));
    timers.push(setTimeout(() => {
      showLabel('ikaria');
    }, 3100));

    // 3.6s – 4.3s: LOMA LINDA — USA (Marker at 3.6s, Label at 3.8s)
    timers.push(setTimeout(() => {
      showMarker('loma-linda');
      updateStepText("LOCATING THE BLUE ZONES", 85);
    }, 3600));
    timers.push(setTimeout(() => {
      showLabel('loma-linda');
    }, 3800));

    // 4.3s – 5.1s: CONNECTING THE STORIES + Reveal Connecting Lines
    timers.push(setTimeout(() => {
      updateStepText("CONNECTING THE STORIES", 95);
      if (linesGroup) linesGroup.style.opacity = '0.7';
    }, 4300));

    // 5.1s – 6.0s: ENTERING BLUE ZONE
    timers.push(setTimeout(() => {
      updateStepText("ENTERING BLUE ZONE", 100);
    }, 5100));

    // 6.0s – 6.8s: Smooth 800ms Fade Out Transition to Homepage
    timers.push(setTimeout(() => {
      finishIntro();
    }, MIN_DURATION));

    // 10s Failsafe Timeout
    timers.push(setTimeout(() => {
      finishIntro();
    }, 10000));

    function showMarker(id) {
      const marker = document.getElementById(`marker-${id}`);
      if (marker) marker.style.opacity = '1';
    }

    function showLabel(id) {
      const label = document.getElementById(`label-${id}`);
      if (label) label.style.opacity = '1';
    }

    function finishIntro() {
      timers.forEach(t => clearTimeout(t));
      try {
        localStorage.setItem(INTRO_KEY, 'true');
      } catch (e) {}

      loaderEl.style.transition = `opacity ${FADE_DURATION}ms ease`;
      loaderEl.style.opacity = '0';

      setTimeout(() => {
        loaderEl.style.display = 'none';
      }, FADE_DURATION);
    }

    // Skip Button Handler
    const skipBtn = document.getElementById('skip-intro-btn');
    if (skipBtn) {
      skipBtn.onclick = function () {
        finishIntro();
      };
    }
  }

  window.BLUEZONE_MAP = {
    init: initMapIntro,
    replay: function () {
      initMapIntro(true);
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    initMapIntro(true);
  });
})();
