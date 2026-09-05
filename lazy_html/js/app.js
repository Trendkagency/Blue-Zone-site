// BLUE ZONE Global App Manager & Interactive UI Controllers

(function () {

  // Global Toast Notification System (replaces raw browser alerts)
  function showToast(message, type = 'info') {
    let container = document.getElementById('bz-toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'bz-toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `bz-toast ${type === 'success' ? 'toast-success' : ''}`;
    
    const icon = type === 'success' ? '✓' : 'ℹ';
    toast.innerHTML = `
      <span class="w-5 h-5 rounded-full bg-[#2A8FC2]/20 text-[#2A8FC2] flex items-center justify-center font-bold text-xs flex-shrink-0">${icon}</span>
      <span class="flex-1">${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('toast-out');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, 3200);
  }

  // Scroll Lock Helper for Modals & Drawers
  function lockBodyScroll(isLocked) {
    if (isLocked) {
      document.body.style.overflow = 'hidden';
    } else {
      const openModal = document.querySelector('.bz-modal-open');
      if (!openModal) {
        document.body.style.overflow = '';
      }
    }
  }

  // Active Navbar Link Highlighter
  function highlightActiveNavLink() {
    const path = window.location.pathname.split('/').pop() || 'index.html';
    const pageMap = {
      '': 'home',
      'index.html': 'home',
      'science.html': 'science',
      'products.html': 'products',
      'team.html': 'team',
      'blog.html': 'blog',
      'shop.html': 'shop',
      'contact.html': 'contact'
    };
    const activeKey = pageMap[path] || '';
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
      if (link.getAttribute('data-nav') === activeKey) {
        link.className = 'nav-link text-xs uppercase tracking-[0.15em] font-black text-[#0A4F78] dark:text-[#2A8FC2] border-b-2 border-[#0A4F78] dark:border-[#2A8FC2] py-1 transition-colors';
      } else {
        link.className = 'nav-link text-xs uppercase tracking-[0.15em] font-bold text-[#031827]/80 dark:text-[#F6F5EF]/80 hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-colors py-1';
      }
    });
  }

  // Header Scroll Behavior
  function initHeaderScroll() {
    const header = document.querySelector('header');
    if (!header) return;

    window.addEventListener('scroll', () => {
      if (window.scrollY > 20) {
        header.classList.add('header-scrolled');
      } else {
        header.classList.remove('header-scrolled');
      }
    }, { passive: true });
  }

  // IntersectionObserver Scroll Reveal Animations
  function initScrollReveal() {
    if (!('IntersectionObserver' in window)) {
      document.querySelectorAll('.reveal-on-scroll').forEach(el => el.classList.add('is-visible'));
      return;
    }

    const observerOptions = {
      root: null,
      rootMargin: '0px 0px -50px 0px',
      threshold: 0.08
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
        }
      });
    }, observerOptions);

    const targets = document.querySelectorAll('section, .reveal-on-scroll');
    targets.forEach(target => {
      if (!target.classList.contains('reveal-on-scroll')) {
        target.classList.add('reveal-on-scroll');
      }
      observer.observe(target);
    });
  }

  // Graceful Local Image Fallback
  function handleImageFallback(imgEl) {
    if (imgEl && !imgEl.getAttribute('data-fallback-triggered')) {
      imgEl.setAttribute('data-fallback-triggered', 'true');
      imgEl.src = 'assets/products/blue-mind.jpg';
    }
  }

  // Quick View Modal Controller
  function openQuickView(productId) {
    const products = window.BLUEZONE_PRODUCTS || [];
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const modal = document.getElementById('quick-view-modal');
    const content = document.getElementById('quick-view-content');

    if (!modal || !content) return;

    let qty = 1;

    content.innerHTML = `
      <div class="relative w-full max-w-3xl bg-[#F6F5EF] dark:bg-[#062B49] rounded-3xl overflow-hidden shadow-2xl border border-[#0A4F78]/30 grid grid-cols-1 md:grid-cols-12 max-h-[90vh] overflow-y-auto transform transition-transform duration-300 scale-100">
        <button onclick="BLUEZONE_APP.closeQuickView()" aria-label="Close modal" class="absolute top-4 right-4 z-20 p-2.5 rounded-full bg-black/10 dark:bg-white/10 hover:bg-black/20 text-[#031827] dark:text-white cursor-pointer transition-transform hover:scale-110">
          ✕
        </button>

        <div class="md:col-span-5 bg-white dark:bg-[#031827] p-8 flex items-center justify-center relative img-zoom-container">
          <a href="product.html?id=${product.id}" class="w-full h-full flex items-center justify-center">
            <img src="${product.image}" alt="${product.name}" onerror="BLUEZONE_APP.handleImageFallback(this)" class="w-full max-h-72 object-contain filter drop-shadow-xl transition-transform duration-500 hover:scale-105" />
          </a>
        </div>

        <div class="md:col-span-7 p-6 sm:p-8 space-y-5 flex flex-col justify-between">
          <div class="space-y-3">
            <span class="text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">
              ${product.category}
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF]">
              <a href="product.html?id=${product.id}" class="hover:text-[#67B34A] transition-colors">${product.name}</a>
            </h2>
            <div class="flex items-center gap-2">
              <span class="text-xs font-bold text-[#67B34A]">★ ${product.rating}</span>
              <span class="text-xs text-[#031827]/50 dark:text-[#F6F5EF]/50">(${product.reviewsCount} verified reviews)</span>
            </div>
            <p class="text-2xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">$${product.price.toFixed(2)}</p>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">${product.description}</p>
          </div>

          <div class="space-y-4 pt-4 border-t border-[#0A4F78]/15">
            <div class="flex items-center gap-4">
              <span class="text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">QUANTITY:</span>
              <div class="flex items-center border border-[#0A4F78]/30 rounded-lg overflow-hidden bg-white dark:bg-[#031827]">
                <button id="qv-qty-minus" aria-label="Decrease quantity" class="px-3 py-1 text-[#031827] dark:text-white cursor-pointer hover:bg-[#0A4F78]/10 transition-colors font-bold">-</button>
                <span id="qv-qty-val" class="px-4 text-xs font-bold text-[#031827] dark:text-white">1</span>
                <button id="qv-qty-plus" aria-label="Increase quantity" class="px-3 py-1 text-[#031827] dark:text-white cursor-pointer hover:bg-[#0A4F78]/10 transition-colors font-bold">+</button>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
              <button id="qv-add-cart-btn" class="flex-1 py-3.5 px-6 rounded-lg bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs uppercase font-extrabold tracking-widest shadow-md cursor-pointer transition-transform active:scale-95 btn-sheen">
                ADD TO CART
              </button>
              <a href="product.html?id=${product.id}" class="py-3.5 px-5 rounded-lg border border-[#0A4F78] text-[#0A4F78] dark:text-[#2A8FC2] hover:bg-[#0A4F78] hover:text-white text-xs uppercase font-extrabold tracking-widest text-center transition-all">
                VIEW FULL DETAILS →
              </a>
            </div>
          </div>
        </div>
      </div>
    `;

    modal.classList.remove('hidden');
    modal.classList.add('flex', 'bz-modal-open');
    lockBodyScroll(true);

    const minusBtn = document.getElementById('qv-qty-minus');
    const plusBtn = document.getElementById('qv-qty-plus');
    const qtyVal = document.getElementById('qv-qty-val');
    const addBtn = document.getElementById('qv-add-cart-btn');

    if (minusBtn && plusBtn && qtyVal) {
      minusBtn.onclick = () => {
        qty = Math.max(1, qty - 1);
        qtyVal.textContent = qty;
      };
      plusBtn.onclick = () => {
        qty = qty + 1;
        qtyVal.textContent = qty;
      };
    }

    if (addBtn) {
      addBtn.onclick = () => {
        if (window.BLUEZONE_CART) {
          window.BLUEZONE_CART.add(productId, qty);
        }
        closeQuickView();
      };
    }
  }

  function closeQuickView() {
    const modal = document.getElementById('quick-view-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex', 'bz-modal-open');
      lockBodyScroll(false);
    }
  }

  // Checkout Modal Controller
  function openCheckout() {
    if (window.BLUEZONE_CART) {
      window.BLUEZONE_CART.close();
    }
    const modal = document.getElementById('checkout-modal');
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex', 'bz-modal-open');
      lockBodyScroll(true);
      renderCheckoutSummary();
    }
  }

  function closeCheckout() {
    const modal = document.getElementById('checkout-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex', 'bz-modal-open');
      lockBodyScroll(false);
    }
  }

  function renderCheckoutSummary() {
    const subtotal = window.BLUEZONE_CART ? window.BLUEZONE_CART.getSubtotal() : 0;
    const isFree = subtotal >= 75.00;
    const shipping = isFree ? 0 : 9.99;
    const total = subtotal + shipping;

    const subtotalEl = document.getElementById('checkout-subtotal');
    const shippingEl = document.getElementById('checkout-shipping');
    const totalEl = document.getElementById('checkout-total');
    const submitBtn = document.getElementById('checkout-submit-btn');

    if (subtotalEl) subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
    if (shippingEl) shippingEl.textContent = isFree ? 'FREE' : `$${shipping.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `$${total.toFixed(2)}`;
    if (submitBtn) submitBtn.textContent = `PLACE ORDER ($${total.toFixed(2)})`;
  }

  function handleCheckoutSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const name = form.name.value.trim();
    const email = form.email.value.trim();
    const address = form.address.value.trim();

    if (!name || !email || !address) {
      showToast("Please fill in all required fields.", "error");
      return;
    }

    const orderId = 'BZ-' + Math.floor(100000 + Math.random() * 900000);
    const formStep = document.getElementById('checkout-form-step');
    const confirmedStep = document.getElementById('checkout-confirmed-step');
    const orderIdSpan = document.getElementById('checkout-order-id');
    const orderEmailSpan = document.getElementById('checkout-order-email');

    if (orderIdSpan) orderIdSpan.textContent = orderId;
    if (orderEmailSpan) orderEmailSpan.textContent = email;

    if (formStep) formStep.classList.add('hidden');
    if (confirmedStep) confirmedStep.classList.remove('hidden');

    if (window.BLUEZONE_CART) {
      window.BLUEZONE_CART.clear();
    }
    showToast("Order placed successfully!", "success");
  }

  // Mobile Drawer Navigation Toggle
  function toggleMobileMenu() {
    const menu = document.getElementById('mobile-nav-drawer');
    if (menu) {
      const isHidden = menu.classList.contains('hidden');
      if (isHidden) {
        menu.classList.remove('hidden');
        menu.classList.add('bz-modal-open');
        lockBodyScroll(true);
      } else {
        closeMobileMenu();
      }
    }
  }

  function closeMobileMenu() {
    const menu = document.getElementById('mobile-nav-drawer');
    if (menu) {
      menu.classList.add('hidden');
      menu.classList.remove('bz-modal-open');
      lockBodyScroll(false);
    }
  }

  // Global Keydown Handler (ESC key closes all modals/drawers)
  function initKeyboardListeners() {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeQuickView();
        closeCheckout();
        closeMobileMenu();
        if (window.BLUEZONE_CART) window.BLUEZONE_CART.close();
        if (window.BLUEZONE_WISHLIST) window.BLUEZONE_WISHLIST.close();
        if (window.BLUEZONE_SEARCH) window.BLUEZONE_SEARCH.close();
      }
    });
  }

  
  // Live Screen Reader Announcer
  function announceToSR(message, priority) {
    if (!message) return;
    let announcer = document.getElementById('bz-sr-announcer');
    if (!announcer) {
      announcer = document.createElement('div');
      announcer.id = 'bz-sr-announcer';
      announcer.className = 'sr-only';
      announcer.setAttribute('aria-live', priority || 'polite');
      announcer.setAttribute('aria-atomic', 'true');
      document.body.appendChild(announcer);
    }
    announcer.textContent = '';
    setTimeout(() => {
      announcer.textContent = message;
    }, 50);
  }

  // Focus Trapping for Accessible Dialogs & Drawers
  function trapFocus(modalEl) {
    if (!modalEl) return () => {};
    const focusable = modalEl.querySelectorAll(
      'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    );
    if (!focusable.length) return () => {};

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    function handleTab(e) {
      if (e.key !== 'Tab') return;
      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    }

    modalEl.addEventListener('keydown', handleTab);
    return () => modalEl.removeEventListener('keydown', handleTab);
  }

  window.BLUEZONE_APP = {
    announce: announceToSR,
    trapFocus: trapFocus,
    openQuickView: openQuickView,
    closeQuickView: closeQuickView,
    openCheckout: openCheckout,
    closeCheckout: closeCheckout,
    submitCheckout: handleCheckoutSubmit,
    toggleMobileMenu: toggleMobileMenu,
    closeMobileMenu: closeMobileMenu,
    highlightNav: highlightActiveNavLink,
    initScrollReveal: initScrollReveal,
    showToast: showToast,
    lockBodyScroll: lockBodyScroll,
    handleImageFallback: handleImageFallback
  };

  document.addEventListener('DOMContentLoaded', () => {
    highlightActiveNavLink();
    initHeaderScroll();
    initScrollReveal();
    initKeyboardListeners();

    const mobileBtn = document.getElementById('mobile-menu-btn');
    if (mobileBtn) {
      mobileBtn.onclick = toggleMobileMenu;
    }

    // Auto-close mobile drawer when clicking links
    const mobileLinks = document.querySelectorAll('#mobile-nav-drawer a');
    mobileLinks.forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });
  });

})();
