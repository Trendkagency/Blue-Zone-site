// BLUE ZONE Wishlist Manager (Vanilla JS + LocalStorage)

(function () {
  const WISHLIST_KEY = 'bluezone_wishlist';

  function getWishlist() {
    try {
      const saved = localStorage.getItem(WISHLIST_KEY);
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed)) return parsed;
      }
    } catch (e) {
      console.warn("Wishlist read error:", e);
    }
    return [];
  }

  function saveWishlist(list) {
    try {
      localStorage.setItem(WISHLIST_KEY, JSON.stringify(list || []));
    } catch (e) {
      console.warn("Wishlist save error:", e);
    }
    updateWishlistBadge();
    renderWishlistDrawer();
  }

  function updateWishlistBadge() {
    const list = getWishlist();
    const count = list.length;
    const badges = document.querySelectorAll('.wishlist-badge-count');
    badges.forEach(b => {
      b.textContent = count;
      b.style.display = count > 0 ? 'flex' : 'none';
      if (count > 0) {
        b.classList.remove('animate-badge-pop');
        void b.offsetWidth;
        b.classList.add('animate-badge-pop');
      }
    });
  }

  function toggleWishlist(productId) {
    const products = window.BLUEZONE_PRODUCTS || [];
    const product = products.find(p => p.id === productId);
    if (!product) return;

    let list = getWishlist();
    const exists = list.some(item => item.id === productId);

<<<<<<< HEAD
    if (exists) {
      list = list.filter(item => item.id !== productId);
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.showToast) {
=======
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';
    const prodName = isAr && product.name_ar ? product.name_ar : (product.name || product.name_en);

    if (exists) {
      list = list.filter(item => item.id !== productId);
      if (window.toast) {
        window.toast.info(isAr ? `تمت إزالة [${prodName}] من قائمة الرغبات` : `Removed [${prodName}] from wishlist`);
      } else if (window.BLUEZONE_APP && window.BLUEZONE_APP.showToast) {
>>>>>>> origin/main
        window.BLUEZONE_APP.showToast(`${product.name} removed from wishlist.`, 'info');
      }
    } else {
      list.push(product);
<<<<<<< HEAD
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.showToast) {
=======
      if (window.toast) {
        window.toast.success(isAr ? `تمت إضافة [${prodName}] إلى قائمة الرغبات` : `Added [${prodName}] to your wishlist`);
      } else if (window.BLUEZONE_APP && window.BLUEZONE_APP.showToast) {
>>>>>>> origin/main
        window.BLUEZONE_APP.showToast(`${product.name} added to wishlist!`, 'success');
      }
    }

    saveWishlist(list);
  }

  function isInWishlist(productId) {
    return getWishlist().some(item => item.id === productId);
  }

  function openWishlistDrawer() {
    const drawer = document.getElementById('wishlist-drawer');
    if (drawer) {
      drawer.classList.remove('hidden');
      drawer.classList.add('flex', 'bz-modal-open');
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.lockBodyScroll) {
        window.BLUEZONE_APP.lockBodyScroll(true);
      }
    }
  }

  function closeWishlistDrawer() {
    const drawer = document.getElementById('wishlist-drawer');
    if (drawer) {
      drawer.classList.add('hidden');
      drawer.classList.remove('flex', 'bz-modal-open');
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.lockBodyScroll) {
        window.BLUEZONE_APP.lockBodyScroll(false);
      }
    }
  }

  function renderWishlistDrawer() {
    const container = document.getElementById('wishlist-items-container');
    if (!container) return;

    const list = getWishlist();

    if (list.length === 0) {
      container.innerHTML = `
        <div class="text-center py-16 space-y-4">
          <svg class="w-12 h-12 text-[#0A4F78]/40 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          <p class="text-base font-bold text-[#031827] dark:text-[#F6F5EF]">Your wishlist is currently empty.</p>
          <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 max-w-xs mx-auto">Save your favorite formulations for quick future reference.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = list.map(item => `
      <div class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm">
        <div class="w-16 h-16 rounded-lg bg-[#F6F5EF] dark:bg-[#031827] p-2 flex-shrink-0 flex items-center justify-center">
          <img src="${item.image}" alt="${item.name}" onerror="BLUEZONE_APP.handleImageFallback(this)" class="w-full h-full object-contain" />
        </div>
        <div class="flex-1 min-w-0 space-y-1">
          <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF] truncate">${item.name}</h4>
          <p class="text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2]">$${(item.price || 0).toFixed(2)}</p>
          <div class="flex items-center gap-2 pt-1">
            <button onclick="BLUEZONE_CART.add('${item.id}', 1); BLUEZONE_WISHLIST.toggle('${item.id}');" class="px-3 py-1.5 rounded bg-[#0A4F78] hover:bg-[#062B49] text-white text-[10px] uppercase font-bold tracking-wider cursor-pointer transition-colors">
              MOVE TO CART
            </button>
            <button onclick="BLUEZONE_WISHLIST.toggle('${item.id}')" aria-label="Remove from wishlist" class="p-1 text-red-500 hover:text-red-700 ml-auto cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>
    `).join('');
  }

  window.BLUEZONE_WISHLIST = {
    get: getWishlist,
    toggle: toggleWishlist,
    isInWishlist: isInWishlist,
    open: openWishlistDrawer,
    close: closeWishlistDrawer,
    render: renderWishlistDrawer,
    updateBadge: updateWishlistBadge
  };

  document.addEventListener('DOMContentLoaded', () => {
    updateWishlistBadge();
    renderWishlistDrawer();
  });
})();
