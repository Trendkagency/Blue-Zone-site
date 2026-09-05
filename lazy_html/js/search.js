// BLUE ZONE Real-Time Live Search System (Vanilla JS)

(function () {

  function openSearch() {
    const overlay = document.getElementById('search-overlay');
    const input = document.getElementById('search-input');
    if (overlay) {
      overlay.classList.remove('hidden');
      overlay.classList.add('bz-modal-open');
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.trapFocus) { window._bzSearchTrap = window.BLUEZONE_APP.trapFocus(overlay); }
      overlay.style.display = 'flex';
      overlay.removeAttribute('aria-hidden');

      if (window.BLUEZONE_APP && window.BLUEZONE_APP.lockBodyScroll) {
        window.BLUEZONE_APP.lockBodyScroll(true);
      }
      if (input) {
        setTimeout(() => input.focus(), 50);
        renderSearchResults(input.value || '');
      }
    }
  }

  function closeSearch() {
    const overlay = document.getElementById('search-overlay');
    if (overlay) {
      overlay.classList.add('hidden');
      overlay.classList.remove('bz-modal-open');
      if (window._bzSearchTrap) { window._bzSearchTrap(); window._bzSearchTrap = null; }
      overlay.style.display = 'none';
      overlay.setAttribute('aria-hidden', 'true');

      if (window.BLUEZONE_APP && window.BLUEZONE_APP.lockBodyScroll) {
        window.BLUEZONE_APP.lockBodyScroll(false);
      }
    }
  }

  function clearQuery() {
    const input = document.getElementById('search-input');
    if (input) {
      input.value = '';
      renderSearchResults('');
      input.focus();
    }
  }

  function searchProducts(query) {
    const products = window.BLUEZONE_PRODUCTS || [];
    const q = (query || '').toLowerCase().trim();
    if (!q) return products;

    return products.filter(p => {
      const matchName = p.name.toLowerCase().includes(q);
      const matchTag = (p.tagline || '').toLowerCase().includes(q);
      const matchDesc = (p.description || '').toLowerCase().includes(q);
      const matchCat = (p.category || '').toLowerCase().includes(q);
      
      // Also match individual ingredient names
      let matchIng = false;
      if (p.ingredients) {
        const allIng = [
          ...(p.ingredients.cognitive || []),
          ...(p.ingredients.minerals || []),
          ...(p.ingredients.vitamins || [])
        ];
        matchIng = allIng.some(ing => ing.name.toLowerCase().includes(q));
      }

      return matchName || matchTag || matchDesc || matchCat || matchIng;
    });
  }

  function renderSearchResults(query) {
    const resultsContainer = document.getElementById('search-results-container');
    const countEl = document.getElementById('search-results-count');
    if (!resultsContainer) return;

    const results = searchProducts(query);

    if (countEl) {
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.announce) { window.BLUEZONE_APP.announce(`${results.length} formulations available.`); }
    countEl.textContent = query.trim() 
        ? `${results.length} result${results.length === 1 ? '' : 's'} for "${query}"`
        : `Showing all ${results.length} formulations`;
    }

    if (results.length === 0) {
      resultsContainer.innerHTML = `
        <div class="p-8 text-center space-y-3">
          <div class="text-3xl">🔍</div>
          <p class="font-extrabold text-sm text-[#031827] dark:text-[#F6F5EF]">No formulations found matching "${query}"</p>
          <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60">Try searching for "mind", "energy", "immunity", "ginkgo", or "co-q10".</p>
        </div>
      `;
      return;
    }

    resultsContainer.innerHTML = results.map(product => `
      <div data-testid="search-result-item" data-product-id="${product.id}" class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 hover:border-[#0A4F78]/40 hover:shadow-md transition-all">
        <a href="product.html?id=${product.id}" onclick="BLUEZONE_SEARCH.close()" class="flex items-center gap-4 min-w-0 flex-1">
          <div class="w-14 h-14 p-1 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] flex items-center justify-center shrink-0">
            <img src="${product.image}" alt="${product.name}" onerror="BLUEZONE_APP.handleImageFallback(this)" class="w-full h-full object-contain" />
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded bg-[#0A4F78]/10 dark:bg-[#0A4F78]/30 text-[#0A4F78] dark:text-[#2A8FC2]">${product.category}</span>
              <span class="text-[10px] text-[#67B34A] font-bold">★ ${product.rating}</span>
            </div>
            <h4 class="font-black text-sm text-[#031827] dark:text-[#F6F5EF] truncate mt-0.5 hover:text-[#0A4F78] transition-colors">${product.name}</h4>
            <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 truncate font-medium">${product.shortDesc || product.tagline}</p>
          </div>
        </a>

        <div class="flex items-center gap-3 shrink-0">
          <span class="font-black text-sm text-[#0A4F78] dark:text-[#2A8FC2]">$${product.price.toFixed(2)}</span>
          <button onclick="BLUEZONE_CART.add('${product.id}', 1); BLUEZONE_SEARCH.close();" class="px-3.5 py-2 rounded-lg bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-bold uppercase tracking-wider transition-colors cursor-pointer shadow-sm">
            Add
          </button>
        </div>
      </div>
    `).join('');
  }

  function initSearch() {
    const input = document.getElementById('search-input');
    if (input) {
      input.addEventListener('input', (e) => {
        renderSearchResults(e.target.value);
      });
    }

    // Explicit Close Buttons and Backdrop click
    document.querySelectorAll('.bz-search-close, [data-search-close]').forEach(btn => {
      btn.onclick = function (e) {
        if (e) e.preventDefault();
        closeSearch();
      };
    });

    const overlay = document.getElementById('search-overlay');
    if (overlay) {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          closeSearch();
        }
      });
    }

    // Global keyboard shortcut
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeSearch();
        return;
      }

      const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
      if (activeTag === 'input' || activeTag === 'textarea') return;

      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        openSearch();
      } else if (e.key === '/' && activeTag !== 'input') {
        e.preventDefault();
        openSearch();
      }
    });
  }

  window.BLUEZONE_SEARCH = {
    open: openSearch,
    close: closeSearch,
    clearQuery: clearQuery,
    query: searchProducts,
    render: renderSearchResults
  };

  document.addEventListener('DOMContentLoaded', () => {
    initSearch();
  });

})();
