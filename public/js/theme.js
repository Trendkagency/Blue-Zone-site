// BLUE ZONE Theme Manager (Light & Dark Mode with Persistent Storage)

(function () {
  const THEME_KEY = 'bluezone_theme';

  function getSavedTheme() {
    try {
      const saved = localStorage.getItem(THEME_KEY);
      if (saved === 'dark' || saved === 'light') {
        return saved;
      }
      return 'light'; // Default mood is light
    } catch (e) {
      return 'light';
    }
  }

  function applyTheme(theme) {
    const root = document.documentElement;
    if (theme === 'dark') {
      root.classList.add('dark');
    } else {
      root.classList.remove('dark');
    }
    try {
      localStorage.setItem(THEME_KEY, theme);
    } catch (e) {}

    // Dispatch event for any listeners
    window.dispatchEvent(new CustomEvent('themechanged', { detail: { theme } }));
  }

  // Initial application on DOM ready
  const currentTheme = getSavedTheme();
  applyTheme(currentTheme);

  window.BLUEZONE_THEME = {
    get: getSavedTheme,
    set: function(theme) {
      applyTheme(theme);
    },
    toggle: function () {
      const active = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
      const next = active === 'light' ? 'dark' : 'light';
      applyTheme(next);
      return next;
    }
  };

  // Global helper fallback
  window.toggleTheme = function() {
    return window.BLUEZONE_THEME.toggle();
  };
})();
