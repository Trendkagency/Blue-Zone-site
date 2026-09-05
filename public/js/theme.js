// BLUE ZONE Theme Manager (Light & Dark Mode with Persistent Storage)
// Default theme: Light Mode (User selectable Dark Mode)

(function () {
  const THEME_KEY = 'bluezone_theme';
  const LEGACY_KEY = 'bz_theme';

  function getSavedTheme() {
    try {
      const saved = localStorage.getItem(THEME_KEY) || localStorage.getItem(LEGACY_KEY);
      if (saved === 'dark') {
        return 'dark';
      }
      return 'light'; // Default is always light mode
    } catch (e) {
      return 'light';
    }
  }

  function updateUI(theme) {
    const isDark = theme === 'dark';
    
    // Update theme text labels
    document.querySelectorAll('[data-theme-label]').forEach(function (el) {
      el.textContent = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
    });

    // Update toggle buttons aria attributes and titles
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
      const nextTitle = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
      btn.setAttribute('aria-label', nextTitle);
      btn.setAttribute('title', nextTitle);
    });
  }

  function applyTheme(theme) {
    const root = document.documentElement;
    const isDark = theme === 'dark';

    if (isDark) {
      root.classList.add('dark');
    } else {
      root.classList.remove('dark');
    }

    try {
      localStorage.setItem(THEME_KEY, theme);
      localStorage.setItem(LEGACY_KEY, theme);
    } catch (e) {}

    updateUI(theme);

    // Dispatch event for any reactive listeners
    window.dispatchEvent(new CustomEvent('themechanged', { detail: { theme: theme, isDark: isDark } }));
  }

  // Initial application immediately
  const currentTheme = getSavedTheme();
  applyTheme(currentTheme);

  // Re-sync UI after DOM is fully ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      updateUI(getSavedTheme());
    });
  } else {
    updateUI(currentTheme);
  }

  window.BLUEZONE_THEME = {
    get: getSavedTheme,
    set: function (theme) {
      applyTheme(theme === 'dark' ? 'dark' : 'light');
    },
    toggle: function () {
      const active = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
      const next = active === 'light' ? 'dark' : 'light';
      applyTheme(next);
      return next;
    }
  };

  // Global helper fallback
  window.toggleTheme = function () {
    return window.BLUEZONE_THEME.toggle();
  };
})();
