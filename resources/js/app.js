/**
 * BLUE ZONE Application Master JS
 */

document.addEventListener('DOMContentLoaded', () => {
<<<<<<< HEAD
    // 1. Theme Manager (Light / Dark mode)
    const initTheme = () => {
        const savedTheme = localStorage.getItem('bz_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
=======
    // 1. Theme Manager (Light / Dark mode - Default Light)
    const THEME_KEY = 'bluezone_theme';
    const LEGACY_KEY = 'bz_theme';

    const initTheme = () => {
        if (window.BLUEZONE_THEME) {
            return;
        }
        const savedTheme = localStorage.getItem(THEME_KEY) || localStorage.getItem(LEGACY_KEY);
>>>>>>> origin/main
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    window.toggleTheme = () => {
<<<<<<< HEAD
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('bz_theme', isDark ? 'dark' : 'light');
=======
        if (window.BLUEZONE_THEME) {
            return window.BLUEZONE_THEME.toggle();
        }
        const isDark = document.documentElement.classList.toggle('dark');
        const theme = isDark ? 'dark' : 'light';
        try {
            localStorage.setItem(THEME_KEY, theme);
            localStorage.setItem(LEGACY_KEY, theme);
        } catch(e) {}
        return theme;
>>>>>>> origin/main
    };

    initTheme();

    // 2. Tab Navigation Component Handler
    const initTabs = () => {
        document.querySelectorAll('[data-tab-group]').forEach(group => {
            const tabs = group.querySelectorAll('[data-tab-target]');
            const groupName = group.getAttribute('data-tab-group');
            const contents = document.querySelectorAll(`[data-tab-content="${groupName}"]`);

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetId = tab.getAttribute('data-tab-target');

                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => {
                        if (c.id === targetId) {
                            c.style.display = 'block';
                        } else {
                            c.style.display = 'none';
                        }
                    });

                    tab.classList.add('active');
                });
            });
        });
    };

    initTabs();

    // 3. Modal / Drawer Management
    window.openModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    };

    // Close modal on backdrop click or ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.is-active, .drawer-backdrop.is-active').forEach(el => {
                el.classList.remove('is-active');
            });
            document.body.style.overflow = '';
        }
    });

    // 4. Quantity Adjuster
    window.changeQty = (button, delta) => {
        const container = button.closest('.quantity-control');
        if (!container) return;
        const input = container.querySelector('.qty-input');
        if (!input) return;

        let val = parseInt(input.value, 10) || 1;
        val = Math.max(1, val + delta);
        input.value = val;
    };

    // 5. Admin Sidebar Mobile Toggle
    window.toggleAdminSidebar = () => {
        const sidebar = document.querySelector('.admin-sidebar');
        if (sidebar) {
            sidebar.classList.toggle('is-open');
        }
    };
});
