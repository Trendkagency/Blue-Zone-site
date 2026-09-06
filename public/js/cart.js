// BLUE ZONE Intelligent Protocol Cart Manager
// Seamless synchronization between frontend client and Laravel backend session

(function () {
  const CART_KEY = 'bluezone_cart';
  let cartData = {
    items: [],
    count: 0,
    subtotal: 0.0,
    discount: 0.0,
    coupon: null,
    shipping: 0.0,
    tax: 0.0,
    total: 0.0,
    free_shipping_threshold: 75.0,
    needed_for_free_shipping: 75.0,
    free_shipping_unlocked: false
  };

  function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function getLocalCart() {
    try {
      const saved = localStorage.getItem(CART_KEY);
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed)) return parsed;
      }
    } catch (e) {
      console.warn("Cart read error:", e);
    }
    return [];
  }

  function saveLocalCart(items) {
    try {
      localStorage.setItem(CART_KEY, JSON.stringify(items || []));
    } catch (e) {
      console.warn("Cart local save error:", e);
    }
  }

  async function syncWithServer() {
    try {
      const res = await fetch('/cart/items', {
        headers: { 'Accept': 'application/json' }
      });
      if (res.ok) {
        const data = await res.json();
        cartData = data;
        saveLocalCart(data.items || []);
        updateCartBadge();
        renderCartDrawer();
        return;
      }
    } catch (e) {
      console.debug("Cart offline fallback:", e);
    }

    // Local fallback if offline
    const local = getLocalCart();
    cartData.items = local;
    cartData.count = local.reduce((sum, i) => sum + (i.quantity || 1), 0);
    cartData.subtotal = local.reduce((sum, i) => sum + (i.price || 0) * (i.quantity || 1), 0);
    cartData.shipping = cartData.subtotal >= 75 ? 0.0 : (cartData.subtotal > 0 ? 9.99 : 0.0);
    cartData.tax = Math.round(cartData.subtotal * 0.15 * 100) / 100;
    cartData.total = cartData.subtotal + cartData.shipping + cartData.tax;
    cartData.free_shipping_unlocked = cartData.subtotal >= 75;
    cartData.needed_for_free_shipping = Math.max(0, 75 - cartData.subtotal);

    updateCartBadge();
    renderCartDrawer();
  }

  function updateCartBadge() {
    const totalCount = cartData.count || (cartData.items || []).reduce((sum, i) => sum + (i.quantity || 1), 0);
    const badges = document.querySelectorAll('.cart-badge-count');
    badges.forEach(b => {
      b.textContent = totalCount;
      b.style.display = totalCount > 0 ? 'flex' : 'none';
      if (totalCount > 0) {
        b.classList.remove('animate-badge-pop');
        void b.offsetWidth;
        b.classList.add('animate-badge-pop');
      }
    });
  }

  async function addToCart(productId, quantity = 1) {
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';

    try {
      const res = await fetch('/cart/add', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
      });

      const data = await res.json();
      if (res.ok && data.success) {
        cartData = data.cart;
        saveLocalCart(cartData.items || []);
        updateCartBadge();
        renderCartDrawer();
        openCartDrawer();

        if (window.toast) {
          window.toast.success(data.message || (isAr ? 'تمت إضافة التركيبة إلى السلة' : 'Added formulation to cart'));
        }
        return;
      } else if (data.message) {
        if (window.toast) window.toast.error(data.message);
        return;
      }
    } catch (e) {
      console.warn("Server add error, using local fallback:", e);
    }

    // Fallback: Local array
    const products = window.BLUEZONE_PRODUCTS || [];
    const product = products.find(p => p.id === productId || String(p.id) === String(productId) || p.slug === productId);
    if (!product) return;

    let items = getLocalCart();
    const existingIndex = items.findIndex(item => item.id === product.id || String(item.id) === String(product.id));

    if (existingIndex > -1) {
      items[existingIndex].quantity = (items[existingIndex].quantity || 1) + quantity;
    } else {
      items.push({ ...product, quantity });
    }

    saveLocalCart(items);
    syncWithServer();
    openCartDrawer();

    const prodName = isAr && product.name_ar ? product.name_ar : (product.name || product.name_en);
    if (window.toast) {
      window.toast.success(isAr ? `تمت إضافة [${prodName}] إلى سلة التسوق` : `Added [${prodName}] to your protocol cart`);
    }
  }

  async function removeFromCart(productId) {
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';

    try {
      const res = await fetch('/cart/remove', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
      });

      if (res.ok) {
        const data = await res.json();
        cartData = data.cart;
        saveLocalCart(cartData.items || []);
        updateCartBadge();
        renderCartDrawer();
        if (window.toast) {
          window.toast.info(isAr ? 'تمت إزالة التركيبة من السلة' : 'Removed formulation from cart');
        }
        // If on /cart page, reload to reflect server table
        if (window.location.pathname === '/cart' || window.location.pathname.endsWith('/cart')) {
          window.location.reload();
        }
        return;
      }
    } catch (e) {
      console.warn("Server remove error:", e);
    }

    let items = getLocalCart().filter(i => i.id !== productId && String(i.id) !== String(productId));
    saveLocalCart(items);
    syncWithServer();
  }

  async function updateQuantity(productId, delta) {
    const items = cartData.items || getLocalCart();
    const item = items.find(i => i.id === productId || String(i.id) === String(productId) || i.slug === productId);
    if (!item) return;

    const newQty = (item.quantity || 1) + delta;
    if (newQty <= 0) {
      removeFromCart(productId);
      return;
    }

    try {
      const res = await fetch('/cart/update', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity: newQty })
      });

      if (res.ok) {
        const data = await res.json();
        cartData = data.cart;
        saveLocalCart(cartData.items || []);
        updateCartBadge();
        renderCartDrawer();
        if (window.location.pathname === '/cart' || window.location.pathname.endsWith('/cart')) {
          window.location.reload();
        }
        return;
      }
    } catch (e) {
      console.warn("Server update error:", e);
    }

    item.quantity = newQty;
    saveLocalCart(items);
    syncWithServer();
  }

  async function clearCart() {
    try {
      await fetch('/cart/clear', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json'
        }
      });
    } catch (e) {}

    saveLocalCart([]);
    syncWithServer();
    if (window.location.pathname === '/cart' || window.location.pathname.endsWith('/cart')) {
      window.location.reload();
    }
  }

  function openCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    if (drawer) {
      drawer.classList.remove('hidden');
      drawer.classList.add('flex', 'bz-modal-open');
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.trapFocus) { window._bzCartTrap = window.BLUEZONE_APP.trapFocus(drawer); }
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.lockBodyScroll) {
        window.BLUEZONE_APP.lockBodyScroll(true);
      }
    }
  }

  function closeCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    if (drawer) {
      drawer.classList.add('hidden');
      drawer.classList.remove('flex', 'bz-modal-open');
      if (window._bzCartTrap) { window._bzCartTrap(); window._bzCartTrap = null; }
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.lockBodyScroll) {
        window.BLUEZONE_APP.lockBodyScroll(false);
      }
    }
  }

  function renderCartDrawer() {
    const container = document.getElementById('cart-items-container');
    const subtotalEl = document.getElementById('cart-subtotal');
    const freeShippingText = document.getElementById('free-shipping-text');
    const freeShippingBar = document.getElementById('free-shipping-bar');

    if (!container) return;

    const items = cartData.items || [];
    const subtotal = cartData.subtotal || 0;
    const threshold = cartData.free_shipping_threshold || 75.0;

    if (subtotalEl) subtotalEl.textContent = `$${subtotal.toFixed(2)}`;

    const needed = Math.max(0, threshold - subtotal);
    const progress = Math.min(100, (subtotal / threshold) * 100);

    if (freeShippingText) {
      if (needed > 0) {
        freeShippingText.innerHTML = `Add <strong class="text-[#0A4F78] dark:text-[#2A8FC2]">$${needed.toFixed(2)}</strong> more for <strong>FREE EXPRESS SHIPPING</strong>`;
      } else {
        freeShippingText.innerHTML = `<span class="text-[#67B34A] font-extrabold">✓ FREE COLD-CHAIN SHIPPING UNLOCKED!</span>`;
      }
    }

    if (freeShippingBar) {
      freeShippingBar.style.width = `${progress}%`;
    }

    if (items.length === 0) {
      container.innerHTML = `
        <div class="text-center py-16 space-y-4">
          <div class="w-16 h-16 rounded-full bg-[#0A4F78]/10 dark:bg-[#0A4F78]/30 flex items-center justify-center mx-auto text-2xl text-[#0A4F78] dark:text-[#2A8FC2]">
            🛒
          </div>
          <p class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">Your protocol cart is empty</p>
          <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 max-w-xs mx-auto">Explore our clinical formulations designed for cellular longevity and daily vitality.</p>
          <a href="/shop" onclick="BLUEZONE_CART.close()" class="inline-block mt-3 px-6 py-2.5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-extrabold uppercase tracking-wider transition-all shadow-md cursor-pointer btn-sheen">
            EXPLORE FORMULATIONS
          </a>
        </div>
      `;
      return;
    }

    container.innerHTML = items.map(item => `
      <div data-testid="cart-item" data-product-id="${item.id}" class="flex items-center gap-3.5 p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm hover:border-[#0A4F78]/30 transition-all">
        <div class="w-16 h-16 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] p-2 flex-shrink-0 flex items-center justify-center overflow-hidden border border-[#0A4F78]/10 dark:border-[#0A4F78]/25">
          <img src="${item.image || '/assets/logo/logo-main.png'}" alt="${item.name_en || item.name || 'Product'}" onerror="this.onerror=null; this.src='/assets/logo/logo-main.png';" class="w-full h-full object-contain hover:scale-105 transition-transform" />
        </div>
        <div class="flex-1 min-w-0 space-y-1">
          <div class="flex items-start justify-between gap-2">
            <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF] leading-tight truncate">${item.name_en || item.name || 'Product'}</h4>
            <button onclick="BLUEZONE_CART.remove('${item.id}')" aria-label="Remove item" class="p-1 text-[#031827]/40 dark:text-white/40 hover:text-red-500 dark:hover:text-red-400 cursor-pointer transition-colors rounded-lg shrink-0" title="Remove from cart">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
          <p class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2]">$${(item.price || 0).toFixed(2)}</p>
          <div class="flex items-center gap-2 pt-1">
            <div class="flex items-center border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 rounded-lg bg-[#F6F5EF] dark:bg-[#031827] overflow-hidden text-[#031827] dark:text-white shadow-xs">
              <button onclick="BLUEZONE_CART.updateQty('${item.id}', -1)" aria-label="Decrease quantity" class="px-2.5 py-1 text-xs font-black cursor-pointer hover:bg-[#0A4F78]/15 text-[#031827] dark:text-white transition-colors">-</button>
              <span class="px-2.5 text-xs font-bold text-[#031827] dark:text-white min-w-[20px] text-center">${item.quantity || 1}</span>
              <button onclick="BLUEZONE_CART.updateQty('${item.id}', 1)" aria-label="Increase quantity" class="px-2.5 py-1 text-xs font-black cursor-pointer hover:bg-[#0A4F78]/15 text-[#031827] dark:text-white transition-colors">+</button>
            </div>
            <span class="text-[11px] text-[#031827]/50 dark:text-[#F6F5EF]/50 font-bold ml-auto">$${((item.price || 0) * (item.quantity || 1)).toFixed(2)}</span>
          </div>
        </div>
      </div>
    `).join('');
  }

  window.BLUEZONE_CART = {
    get: () => cartData.items || [],
    getData: () => cartData,
    add: addToCart,
    remove: removeFromCart,
    updateQty: updateQuantity,
    clear: clearCart,
    getSubtotal: () => cartData.subtotal || 0,
    open: openCartDrawer,
    close: closeCartDrawer,
    render: renderCartDrawer,
    updateBadge: updateCartBadge,
    sync: syncWithServer
  };

  document.addEventListener('DOMContentLoaded', () => {
    syncWithServer();
  });
})();
