// BLUE ZONE Theme Manager (Light Mode Default)

(function () {
  const THEME_KEY = 'bluezone_theme';

  function getSavedTheme() {
    try {
      const saved = localStorage.getItem(THEME_KEY);
      return saved === 'dark' || saved === 'light' ? saved : 'light';
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
    } catch (e) {
      // safe fallback
    }
  }

  // Initial application
  const currentTheme = getSavedTheme();
  applyTheme(currentTheme);

  window.BLUEZONE_THEME = {
    get: getSavedTheme,
    toggle: function () {
      const active = getSavedTheme();
      const next = active === 'light' ? 'dark' : 'light';
      applyTheme(next);
      return next;
    }
  };
})();
