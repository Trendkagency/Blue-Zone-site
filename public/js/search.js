// BLUE ZONE Precision Bio-Compound & Ingredient Live Search System (Vanilla JS)

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
    if (!q) {
      return products.map(p => ({ product: p, matchedIngredient: null }));
    }

    const matches = [];

    products.forEach(p => {
      const matchName = (p.name || '').toLowerCase().includes(q);
      const matchTag = (p.tagline || '').toLowerCase().includes(q);
      const matchDesc = (p.description || '').toLowerCase().includes(q);
      const matchCat = (p.category || '').toLowerCase().includes(q);
      const matchScience = (p.science || '').toLowerCase().includes(q);

      // Search through individual ingredients and bio-compounds
      let matchedIng = null;
      if (p.ingredients) {
        let allIng = [];
        if (Array.isArray(p.ingredients)) {
          allIng = p.ingredients;
        } else if (typeof p.ingredients === 'object') {
          Object.values(p.ingredients).forEach(val => {
            if (Array.isArray(val)) {
              allIng.push(...val);
            }
          });
        }
        
        const found = allIng.find(ing => {
          const name = (typeof ing === 'string' ? ing : (ing.name || ing.name_en || ing.name_ar || '')).toLowerCase();
          return name.includes(q);
        });
        if (found) {
          matchedIng = typeof found === 'string' ? { name: found, dose: '' } : found;
        }
      }

      if (matchedIng || matchName || matchTag || matchDesc || matchCat || matchScience) {
        matches.push({
          product: p,
          matchedIngredient: matchedIng
        });
      }
    });

    return matches;
  }

  function renderSearchResults(query) {
    const resultsContainer = document.getElementById('search-results-container');
    if (!resultsContainer) return;

    const results = searchProducts(query);
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';

    if (results.length === 0) {
      resultsContainer.innerHTML = `
        <div class="col-span-full p-10 text-center space-y-3 bg-[#062B49]/50 rounded-2xl border border-[#0A4F78]/20">
          <div class="text-4xl">🧬</div>
          <p class="font-extrabold text-base text-[#031827] dark:text-[#F6F5EF]">
            ${isAr ? `لم يتم العثور على تركيبات تحتوي على "${query}"` : `No formulations found containing "${query}"`}
          </p>
          <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 max-w-sm mx-auto">
            ${isAr 
              ? 'جرّب البحث عن: Bacopa, Co-Q10, Curcumin, PQQ, L-Theanine, Zinc, Ginkgo.'
              : 'Try searching by active ingredient: Bacopa, Co-Q10, Curcumin, PQQ, L-Theanine, Zinc, Ginkgo.'}
          </p>
        </div>
      `;
      return;
    }

    resultsContainer.innerHTML = results.map(({ product, matchedIngredient }) => `
      <div data-testid="search-result-item" data-product-id="${product.id}" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 hover:border-[#2A8FC2] hover:shadow-lg transition-all">
        <a href="/products/${product.slug || product.id}" onclick="BLUEZONE_SEARCH.close()" class="flex items-center gap-3.5 min-w-0 flex-1">
          <div class="w-16 h-16 p-1.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] flex items-center justify-center shrink-0 border border-[#0A4F78]/10">
            <img src="${product.image || '/assets/logo/logo-main.png'}" alt="${product.name}" onerror="this.onerror=null; this.src='/assets/logo/logo-main.png';" class="w-full h-full object-contain" />
          </div>
          <div class="min-w-0 space-y-1">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-[#0A4F78]/10 dark:bg-[#0A4F78]/30 text-[#0A4F78] dark:text-[#2A8FC2]">${product.category}</span>
              <span class="text-[10px] text-[#67B34A] font-bold">★ ${product.rating || 4.9}</span>
              ${matchedIngredient ? `
                <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded bg-[#67B34A]/15 text-[#67B34A] border border-[#67B34A]/30">
                  🧬 Active: ${matchedIngredient.name} (${matchedIngredient.dose})
                </span>
              ` : ''}
            </div>
            <h4 class="font-black text-sm text-[#031827] dark:text-[#F6F5EF] truncate hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-colors">${product.name}</h4>
            <p class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 line-clamp-1 font-medium">${product.shortDesc || product.tagline}</p>
          </div>
        </a>

        <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#0A4F78]/10">
          <span class="font-black text-base text-[#0A4F78] dark:text-[#2A8FC2]">$${(product.price || 0).toFixed(2)}</span>
          <button onclick="BLUEZONE_CART.add('${product.slug || product.id}', 1); BLUEZONE_SEARCH.close();" class="px-4 py-2 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-wider transition-all cursor-pointer shadow-sm btn-sheen">
            + ADD
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

    // Global keyboard shortcut (Ctrl+K or /)
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
