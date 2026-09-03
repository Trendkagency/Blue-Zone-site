// BLUE ZONE Cart Manager (Vanilla JS + LocalStorage)

(function () {
  const CART_KEY = 'bluezone_cart';
  const FREE_SHIPPING_THRESHOLD = 75.00;

  function getCart() {
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

  function saveCart(cart) {
    try {
      localStorage.setItem(CART_KEY, JSON.stringify(cart || []));
    } catch (e) {
      console.warn("Cart save error:", e);
    }
    updateCartBadge();
    renderCartDrawer();
  }

  function updateCartBadge() {
    const cart = getCart();
    const totalCount = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
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

  function addToCart(productId, quantity = 1) {
    const products = window.BLUEZONE_PRODUCTS || [];
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const cart = getCart();
    const existingIndex = cart.findIndex(item => item.id === productId);

    if (existingIndex > -1) {
      cart[existingIndex].quantity = (cart[existingIndex].quantity || 1) + quantity;
    } else {
      cart.push({ ...product, quantity });
    }

    saveCart(cart);
    openCartDrawer();

    if (window.BLUEZONE_APP && window.BLUEZONE_APP.showToast) {
      window.BLUEZONE_APP.showToast(`${product.name} added to cart!`, 'success');
      window.BLUEZONE_APP.announce(`${product.name} added to cart. Total items: ${cart.reduce((s, i) => s + i.quantity, 0)}.`);
    }
  }

  function removeFromCart(productId) {
    const cart = getCart();
    const item = cart.find(i => i.id === productId);
    const updated = cart.filter(i => i.id !== productId);
    saveCart(updated);

    if (item && window.BLUEZONE_APP && window.BLUEZONE_APP.showToast) {
      window.BLUEZONE_APP.showToast(`${item.name} removed from cart.`, 'info');
      window.BLUEZONE_APP.announce(`${item.name} removed from cart.`);
    }
  }

  function updateQuantity(productId, delta) {
    const cart = getCart();
    const item = cart.find(i => i.id === productId);
    if (!item) return;

    item.quantity = (item.quantity || 1) + delta;
    if (item.quantity <= 0) {
      removeFromCart(productId);
    } else {
      saveCart(cart);
    }
  }

  function clearCart() {
    saveCart([]);
  }

  function getSubtotal() {
    return getCart().reduce((sum, item) => sum + (item.price || 0) * (item.quantity || 1), 0);
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

    const cart = getCart();
    const subtotal = getSubtotal();

    if (subtotalEl) subtotalEl.textContent = `$${subtotal.toFixed(2)}`;

    const needed = Math.max(0, FREE_SHIPPING_THRESHOLD - subtotal);
    const progress = Math.min(100, (subtotal / FREE_SHIPPING_THRESHOLD) * 100);

    if (freeShippingText) {
      if (needed > 0) {
        freeShippingText.innerHTML = `Add <strong class="text-[#0A4F78] dark:text-[#2A8FC2]">$${needed.toFixed(2)}</strong> more for <strong>FREE EXPRESS SHIPPING</strong>`;
      } else {
        freeShippingText.innerHTML = `<span class="text-[#67B34A] font-extrabold">✓ FREE SHIPPING UNLOCKED!</span>`;
      }
    }

    if (freeShippingBar) {
      freeShippingBar.style.width = `${progress}%`;
    }

    if (cart.length === 0) {
      container.innerHTML = `
        <div class="text-center py-16 space-y-4">
          <div class="w-16 h-16 rounded-full bg-[#0A4F78]/10 dark:bg-[#0A4F78]/30 flex items-center justify-center mx-auto text-2xl text-[#0A4F78] dark:text-[#2A8FC2]">
            🛒
          </div>
          <p class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">Your cart is currently empty</p>
          <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 max-w-xs mx-auto">Explore our clinical formulations designed for cellular longevity and daily vitality.</p>
          <button onclick="BLUEZONE_CART.close()" class="inline-block mt-3 px-6 py-2.5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-extrabold uppercase tracking-wider transition-all shadow-md cursor-pointer btn-sheen">
            EXPLORE FORMULATIONS
          </button>
        </div>
      `;
      return;
    }

        container.innerHTML = cart.map(item => `
      <div data-testid="cart-item" data-product-id="${item.id}" class="flex items-center gap-3.5 p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm hover:border-[#0A4F78]/30 transition-all">
        <div class="w-16 h-16 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] p-2 flex-shrink-0 flex items-center justify-center overflow-hidden border border-[#0A4F78]/10 dark:border-[#0A4F78]/25">
          <img src="${item.image}" alt="${item.name}" onerror="BLUEZONE_APP.handleImageFallback(this)" class="w-full h-full object-contain hover:scale-105 transition-transform" />
        </div>
        <div class="flex-1 min-w-0 space-y-1">
          <div class="flex items-start justify-between gap-2">
            <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF] leading-tight truncate">${item.name}</h4>
            <button onclick="BLUEZONE_CART.remove('${item.id}')" aria-label="Remove item" class="p-1 text-[#031827]/40 dark:text-white/40 hover:text-red-500 dark:hover:text-red-400 cursor-pointer transition-colors rounded-lg shrink-0" title="Remove from cart">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
          <p class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2]">${(item.price || 0).toFixed(2)}</p>
          <div class="flex items-center gap-2 pt-1">
            <div class="flex items-center border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 rounded-lg bg-[#F6F5EF] dark:bg-[#031827] overflow-hidden text-[#031827] dark:text-white shadow-xs">
              <button onclick="BLUEZONE_CART.updateQty('${item.id}', -1)" aria-label="Decrease quantity" class="px-2.5 py-1 text-xs font-black cursor-pointer hover:bg-[#0A4F78]/15 text-[#031827] dark:text-white transition-colors">-</button>
              <span class="px-2.5 text-xs font-bold text-[#031827] dark:text-white min-w-[20px] text-center">${item.quantity}</span>
              <button onclick="BLUEZONE_CART.updateQty('${item.id}', 1)" aria-label="Increase quantity" class="px-2.5 py-1 text-xs font-black cursor-pointer hover:bg-[#0A4F78]/15 text-[#031827] dark:text-white transition-colors">+</button>
            </div>
            <span class="text-[11px] text-[#031827]/50 dark:text-[#F6F5EF]/50 font-bold ml-auto">${((item.price || 0) * (item.quantity || 1)).toFixed(2)}</span>
          </div>
        </div>
      </div>
    `).join('');
  }

  window.BLUEZONE_CART = {
    get: getCart,
    add: addToCart,
    remove: removeFromCart,
    updateQty: updateQuantity,
    clear: clearCart,
    getSubtotal: getSubtotal,
    open: openCartDrawer,
    close: closeCartDrawer,
    render: renderCartDrawer,
    updateBadge: updateCartBadge
  };

  document.addEventListener('DOMContentLoaded', () => {
    updateCartBadge();
    renderCartDrawer();
  });
})();
