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

  // Synchronous early hydration from localStorage for instantaneous response
  try {
    const cachedItems = getLocalCart();
    if (cachedItems.length > 0) {
      cartData.items = cachedItems;
      cartData.count = cachedItems.reduce((sum, i) => sum + (parseInt(i.quantity, 10) || 1), 0);
      cartData.subtotal = cachedItems.reduce((sum, i) => sum + (parseFloat(i.price) || 0) * (parseInt(i.quantity, 10) || 1), 0);
      cartData.free_shipping_unlocked = cartData.subtotal >= cartData.free_shipping_threshold;
      cartData.needed_for_free_shipping = Math.max(0, cartData.free_shipping_threshold - cartData.subtotal);
    }
  } catch (e) {}

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
    cartData.count = local.reduce((sum, i) => sum + (parseInt(i.quantity, 10) || 1), 0);
    cartData.subtotal = local.reduce((sum, i) => sum + (parseFloat(i.price) || 0) * (parseInt(i.quantity, 10) || 1), 0);
    cartData.shipping = cartData.subtotal >= 75 ? 0.0 : (cartData.subtotal > 0 ? 9.99 : 0.0);
    cartData.tax = Math.round(cartData.subtotal * 0.15 * 100) / 100;
    cartData.total = cartData.subtotal + cartData.shipping + cartData.tax;
    cartData.free_shipping_unlocked = cartData.subtotal >= 75;
    cartData.needed_for_free_shipping = Math.max(0, 75 - cartData.subtotal);

    updateCartBadge();
    renderCartDrawer();
  }

  function updateCartBadge() {
    const totalCount = cartData.count || (cartData.items || []).reduce((sum, i) => sum + (parseInt(i.quantity, 10) || 1), 0);
    const badges = document.querySelectorAll('.cart-badge-count');
    badges.forEach(b => {
      b.textContent = totalCount;
      if (totalCount > 0) {
        b.classList.remove('hidden');
        b.style.display = 'flex';
        b.classList.remove('animate-badge-pop');
        requestAnimationFrame(() => {
          b.classList.add('animate-badge-pop');
        });
      } else {
        b.classList.add('hidden');
        b.style.display = 'none';
      }
    });

    const drawerBadge = document.getElementById('cart-drawer-badge');
    if (drawerBadge) {
      drawerBadge.textContent = totalCount;
    }
  }

  async function addToCart(productId, quantity = 1, extraData = {}) {
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';

    // Normalize productId
    if (!productId && extraData && (extraData.id || extraData.slug)) {
      productId = extraData.id || extraData.slug;
    }

    // Auto-resolve metadata from window.BLUEZONE_PRODUCTS if not provided
    if (window.BLUEZONE_PRODUCTS && Array.isArray(window.BLUEZONE_PRODUCTS)) {
      const found = window.BLUEZONE_PRODUCTS.find(p => p.id === productId || String(p.id) === String(productId) || p.slug === productId);
      if (found) {
        if (!extraData.name) extraData.name = found.name;
        if (!extraData.name_en) extraData.name_en = found.name;
        if (!extraData.name_ar) extraData.name_ar = found.name_ar || found.name;
        if (!extraData.price && found.price) extraData.price = found.price;
        if (!extraData.image && found.image) extraData.image = found.image;
        if (!extraData.slug) extraData.slug = found.slug || found.id;
      }
    }

    try {
      const res = await fetch('/cart/add', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          product_id: productId,
          id: productId,
          quantity: quantity || 1,
          name: extraData.name || (typeof productId === 'string' ? productId.replace(/[-_]/g, ' ').toUpperCase() : 'BLUE ZONE Formulation'),
          name_en: extraData.name_en || extraData.name || (typeof productId === 'string' ? productId.replace(/[-_]/g, ' ').toUpperCase() : 'BLUE ZONE Formulation'),
          name_ar: extraData.name_ar || extraData.name || null,
          price: extraData.price || 68.00,
          image: extraData.image || 'assets/logo/logo-main.png',
          slug: extraData.slug || (typeof productId === 'string' ? productId : ('product-' + productId))
        })
      });

      if (res.status === 429) {
        const data = await res.json().catch(() => ({}));
        const msg = data.message || (isAr ? 'تم تجاوز حد الطلبات السريعة للسلة. يرجى الانتظار ثوانٍ معدودة.' : 'Cart request limit reached. Please wait a moment.');
        if (window.toast) {
          window.toast.warning(msg);
        } else {
          alert(msg);
        }
        return;
      }

      const data = await res.json().catch(() => ({}));
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
      } else {
        console.warn("Cart add server response:", res.status, data);
        if (data && data.message && res.status !== 422) {
          if (window.toast) window.toast.error(data.message);
        }
      }
    } catch (e) {
      console.warn("Server add error, using local fallback:", e);
    }

    // Fallback: Local array
    const products = window.BLUEZONE_PRODUCTS || [];
    let product = products.find(p => p.id === productId || String(p.id) === String(productId) || p.slug === productId);
    if (!product && extraData && extraData.name) {
      product = {
        id: productId || ('p-' + Date.now()),
        name: extraData.name,
        name_en: extraData.name,
        name_ar: extraData.name,
        price: extraData.price || 0,
        image: extraData.image || '',
        slug: 'product-' + productId
      };
    }
    if (!product) return;

    let items = getLocalCart();
    const existingIndex = items.findIndex(item => item.id === product.id || String(item.id) === String(product.id));

    if (existingIndex > -1) {
      items[existingIndex].quantity = (items[existingIndex].quantity || 1) + quantity;
    } else {
      items.push({ ...product, quantity });
    }

    saveLocalCart(items);
    cartData.items = items;
    cartData.count = items.reduce((sum, i) => sum + (parseInt(i.quantity, 10) || 1), 0);
    cartData.subtotal = items.reduce((sum, i) => sum + (parseFloat(i.price) || 0) * (parseInt(i.quantity, 10) || 1), 0);
    cartData.free_shipping_unlocked = cartData.subtotal >= 75;
    cartData.needed_for_free_shipping = Math.max(0, 75 - cartData.subtotal);

    updateCartBadge();
    renderCartDrawer();
    openCartDrawer();

    const prodName = isAr && product.name_ar ? product.name_ar : (product.name || product.name_en);
    if (window.toast) {
      window.toast.success(isAr ? `تمت إضافة [${prodName}] إلى سلة التسوق` : `Added [${prodName}] to your protocol cart`);
    }

    syncWithServer();
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
        body: JSON.stringify({ product_id: productId, id: productId })
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
        if (window.location.pathname === '/cart' || window.location.pathname.endsWith('/cart')) {
          window.location.reload();
        }
        return;
      }
    } catch (e) {
      console.warn("Server remove error, local fallback:", e);
    }

    let items = getLocalCart();
    items = items.filter(item => item.id !== productId && String(item.id) !== String(productId));
    saveLocalCart(items);
    cartData.items = items;
    cartData.count = items.reduce((sum, i) => sum + (parseInt(i.quantity, 10) || 1), 0);
    cartData.subtotal = items.reduce((sum, i) => sum + (parseFloat(i.price) || 0) * (parseInt(i.quantity, 10) || 1), 0);
    cartData.free_shipping_unlocked = cartData.subtotal >= 75;
    cartData.needed_for_free_shipping = Math.max(0, 75 - cartData.subtotal);

    updateCartBadge();
    renderCartDrawer();
    syncWithServer();
  }

  async function updateQuantity(productId, change) {
    let items = getLocalCart();
    const item = items.find(i => i.id === productId || String(i.id) === String(productId));
    const currentQty = item ? (parseInt(item.quantity, 10) || 1) : 1;
    const newQty = Math.max(0, currentQty + change);

    if (newQty <= 0) {
      return removeFromCart(productId);
    }

    try {
      const res = await fetch('/cart/update', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, id: productId, quantity: newQty })
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
      console.warn("Server update error, local fallback:", e);
    }

    if (item) {
      item.quantity = newQty;
      saveLocalCart(items);
      cartData.items = items;
      cartData.count = items.reduce((sum, i) => sum + (parseInt(i.quantity, 10) || 1), 0);
      cartData.subtotal = items.reduce((sum, i) => sum + (parseFloat(i.price) || 0) * (parseInt(i.quantity, 10) || 1), 0);
      cartData.free_shipping_unlocked = cartData.subtotal >= 75;
      cartData.needed_for_free_shipping = Math.max(0, 75 - cartData.subtotal);

      updateCartBadge();
      renderCartDrawer();
      syncWithServer();
    }
  }

  async function clearCart() {
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';

    try {
      const res = await fetch('/cart/clear', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json'
        }
      });
      if (res.ok) {
        const data = await res.json();
        cartData = data.cart;
        saveLocalCart([]);
        updateCartBadge();
        renderCartDrawer();
        if (window.toast) {
          window.toast.info(isAr ? 'تم تفريغ السلة بالكامل' : 'Cart cleared');
        }
        if (window.location.pathname === '/cart' || window.location.pathname.endsWith('/cart')) {
          window.location.reload();
        }
        return;
      }
    } catch (e) {
      console.warn("Server clear error, local fallback:", e);
    }

    saveLocalCart([]);
    cartData.items = [];
    cartData.count = 0;
    cartData.subtotal = 0;
    cartData.free_shipping_unlocked = false;
    cartData.needed_for_free_shipping = 75;

    updateCartBadge();
    renderCartDrawer();
    syncWithServer();
  }

  function openCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    const overlay = document.getElementById('cart-overlay');
    
    // Always re-render latest data upon opening
    renderCartDrawer();

    if (drawer) {
      drawer.classList.remove('hidden');
      drawer.classList.add('flex');
      drawer.classList.remove('pointer-events-none');
      drawer.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');
    }
    if (overlay) {
      overlay.classList.remove('opacity-0', 'pointer-events-none');
    }
  }

  function closeCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    const overlay = document.getElementById('cart-overlay');
    if (drawer) {
      drawer.classList.add('hidden');
      drawer.classList.remove('flex');
      drawer.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('overflow-hidden');
    }
    if (overlay) {
      overlay.classList.add('opacity-0', 'pointer-events-none');
    }
  }

  function renderCartDrawer() {
    const container = document.getElementById('cart-items-container') || document.getElementById('cart-drawer-items');
    const subtotalEl = document.getElementById('cart-subtotal') || document.getElementById('cart-drawer-subtotal');
    const freeShippingText = document.getElementById('free-shipping-text');
    const freeShippingBar = document.getElementById('free-shipping-bar');
    const clearBtn = document.getElementById('cart-drawer-clear-btn');
    const footer = document.getElementById('cart-drawer-footer');
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';

    if (!container) return;

    const items = cartData.items || [];
    const subtotal = cartData.subtotal || 0;
    const threshold = cartData.free_shipping_threshold || 75.0;

    if (subtotalEl) {
      subtotalEl.textContent = window.BLUEZONE_CURRENCY 
        ? window.BLUEZONE_CURRENCY.format(subtotal) 
        : `$${subtotal.toFixed(2)}`;
    }

    const needed = Math.max(0, threshold - subtotal);
    const progress = Math.min(100, (subtotal / threshold) * 100);

    if (freeShippingText) {
      if (needed > 0) {
        const neededFormatted = window.BLUEZONE_CURRENCY 
          ? window.BLUEZONE_CURRENCY.format(needed) 
          : `$${needed.toFixed(2)}`;
        freeShippingText.innerHTML = isAr 
          ? `<i class="fa-solid fa-truck-fast text-[#0A4F78] dark:text-[#2A8FC2]"></i> أضف <strong class="text-[#0A4F78] dark:text-[#2A8FC2]">${neededFormatted}</strong> إضافية للحصول على <strong>شحن سريع مجاني</strong>`
          : `<i class="fa-solid fa-truck-fast text-[#0A4F78] dark:text-[#2A8FC2]"></i> Add <strong class="text-[#0A4F78] dark:text-[#2A8FC2]">${neededFormatted}</strong> more for <strong>FREE EXPRESS SHIPPING</strong>`;
      } else {
        freeShippingText.innerHTML = isAr
          ? `<span class="text-[#67B34A] font-extrabold flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> تم تفعيل الشحن المبرد المجاني!</span>`
          : `<span class="text-[#67B34A] font-extrabold flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> FREE COLD-CHAIN SHIPPING UNLOCKED!</span>`;
      }
    }

    if (freeShippingBar) {
      freeShippingBar.style.width = `${progress}%`;
    }

    if (clearBtn) {
      clearBtn.style.display = items.length > 0 ? 'inline-flex' : 'none';
    }

    if (footer) {
      footer.style.display = items.length > 0 ? 'block' : 'none';
    }

    const drawerBadge = document.getElementById('cart-drawer-badge');
    if (drawerBadge) {
      drawerBadge.textContent = cartData.count || items.reduce((sum, i) => sum + (parseInt(i.quantity, 10) || 1), 0);
    }

    if (items.length === 0) {
      container.innerHTML = `
        <div class="text-center py-12 px-4 space-y-4">
          <div class="w-16 h-16 rounded-2xl bg-[#0A4F78]/10 dark:bg-[#0A4F78]/30 flex items-center justify-center mx-auto text-[#0A4F78] dark:text-[#2A8FC2] shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          </div>
          <div class="space-y-1">
            <p class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">${isAr ? 'سلة البروتوكول فارغة' : 'Your Protocol Cart is Empty'}</p>
            <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 max-w-xs mx-auto">${isAr ? 'استكشف تركيباتنا الخلوية الإكلينيكية المصممة لتعزيز الصحة والنشاط اليومي.' : 'Explore our clinical formulations designed for cellular longevity and daily vitality.'}</p>
          </div>
          <a href="/shop" onclick="if(window.BLUEZONE_CART){BLUEZONE_CART.close();}" class="inline-flex items-center gap-2 mt-2 px-6 py-3 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-wider transition-all shadow-md hover:shadow-lg cursor-pointer btn-sheen">
            <span>${isAr ? 'استكشف جميع التركيبات' : 'EXPLORE FORMULATIONS'}</span>
            <span class="rtl:rotate-180">→</span>
          </a>
        </div>
      `;
      return;
    }

    container.innerHTML = items.map(item => {
      const itemName = isAr 
        ? (item.name_ar || item.name || item.name_en || 'تركيبة إكلينيكية')
        : (item.name_en || item.name || 'Clinical Formulation');

      let rawImg = item.image || '/assets/logo/logo-main.png';
      if (!rawImg.startsWith('/') && !rawImg.startsWith('http') && !rawImg.startsWith('assets/')) {
        rawImg = '/assets/' + rawImg;
      }
      if (rawImg.startsWith('assets/')) {
        rawImg = '/' + rawImg;
      }

      const itemPrice = parseFloat(item.price) || 0;
      const itemQty = parseInt(item.quantity, 10) || 1;
      const lineTotal = itemPrice * itemQty;

      const formattedPrice = window.BLUEZONE_CURRENCY 
        ? window.BLUEZONE_CURRENCY.format(itemPrice) 
        : `$${itemPrice.toFixed(2)}`;

      const formattedLineTotal = window.BLUEZONE_CURRENCY 
        ? window.BLUEZONE_CURRENCY.format(lineTotal) 
        : `$${lineTotal.toFixed(2)}`;

      const detailUrl = item.slug ? `/products/${item.slug}` : (item.id ? `/products/${item.id}` : '#');

      return `
        <div data-testid="cart-item" data-product-id="${item.id}" class="group relative flex items-center gap-3.5 p-3.5 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-xs hover:border-[#0A4F78]/35 transition-all">
          <a href="${detailUrl}" onclick="if(window.BLUEZONE_CART){BLUEZONE_CART.close();}" class="w-16 h-16 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] p-2 flex-shrink-0 flex items-center justify-center overflow-hidden border border-[#0A4F78]/10 dark:border-[#0A4F78]/25 group-hover:scale-105 transition-transform duration-300">
            <img src="${rawImg}" alt="${itemName}" onerror="this.onerror=null; this.src='/assets/logo/logo-main.png';" class="w-full h-full object-contain" />
          </a>
          <div class="flex-1 min-w-0 space-y-1">
            <div class="flex items-start justify-between gap-2">
              <a href="${detailUrl}" onclick="if(window.BLUEZONE_CART){BLUEZONE_CART.close();}" class="text-xs sm:text-sm font-black text-[#031827] dark:text-[#F6F5EF] leading-tight truncate hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-colors" title="${itemName}">
                ${itemName}
              </a>
              <button type="button" onclick="BLUEZONE_CART.remove('${item.id}')" aria-label="${isAr ? 'إزالة من السلة' : 'Remove from cart'}" class="p-1 text-[#031827]/40 dark:text-white/40 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-500/10 cursor-pointer transition-colors rounded-lg shrink-0" title="${isAr ? 'إزالة' : 'Remove'}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
            <div class="flex items-baseline gap-2">
              <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2]">${formattedPrice}</span>
              ${itemQty > 1 ? `<span class="text-[10px] text-[#031827]/50 dark:text-white/50">× ${itemQty}</span>` : ''}
            </div>
            <div class="flex items-center justify-between gap-2 pt-1">
              <div class="flex items-center border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 rounded-lg bg-[#F6F5EF] dark:bg-[#031827] overflow-hidden text-[#031827] dark:text-white shadow-xs">
                <button type="button" onclick="BLUEZONE_CART.updateQty('${item.id}', -1)" aria-label="${isAr ? 'إنقاص الكمية' : 'Decrease quantity'}" class="w-7 h-6 flex items-center justify-center text-xs font-black cursor-pointer hover:bg-[#0A4F78]/15 text-[#031827] dark:text-white transition-colors">-</button>
                <span class="w-7 text-center text-xs font-bold select-none text-[#031827] dark:text-white">${itemQty}</span>
                <button type="button" onclick="BLUEZONE_CART.updateQty('${item.id}', 1)" aria-label="${isAr ? 'زيادة الكمية' : 'Increase quantity'}" class="w-7 h-6 flex items-center justify-center text-xs font-black cursor-pointer hover:bg-[#0A4F78]/15 text-[#031827] dark:text-white transition-colors">+</button>
              </div>
              <span class="text-xs font-black text-[#031827] dark:text-white">${formattedLineTotal}</span>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  // Keyboard Escape listener to close drawer
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' || e.keyCode === 27) {
      const drawer = document.getElementById('cart-drawer');
      if (drawer && !drawer.classList.contains('hidden')) {
        closeCartDrawer();
      }
    }
  });

  const previousQueue = (window.BLUEZONE_CART && window.BLUEZONE_CART._queue) ? window.BLUEZONE_CART._queue : [];

  window.BLUEZONE_CART = {
    get: () => cartData.items || [],
    getData: () => cartData,
    add: addToCart,
    addItem: (id, name, price, image, qty = 1) => addToCart(id, qty || 1, { name, price, image }),
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

  // Drain any queued calls before script load
  if (Array.isArray(previousQueue) && previousQueue.length > 0) {
    previousQueue.forEach(call => {
      const fnName = call[0];
      const args = call[1];
      if (typeof window.BLUEZONE_CART[fnName] === 'function') {
        window.BLUEZONE_CART[fnName].apply(window.BLUEZONE_CART, args);
      }
    });
  }

  // Immediately render badge from cached state
  updateCartBadge();

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      updateCartBadge();
      syncWithServer();
    });
  } else {
    syncWithServer();
  }
})();
