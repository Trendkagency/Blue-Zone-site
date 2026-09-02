// BLUE ZONE Live Search Overlay Manager

(function () {
  function openSearch() {
    const overlay = document.getElementById('search-overlay');
    if (overlay) {
      overlay.classList.remove('hidden');
      overlay.classList.add('flex', 'bz-modal-open');
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.lockBodyScroll) {
        window.BLUEZONE_APP.lockBodyScroll(true);
      }
      const input = document.getElementById('search-input');
      if (input) {
        input.value = '';
        setTimeout(() => input.focus(), 50);
        renderSearchQuery('');
      }
    }
  }

  function closeSearch() {
    const overlay = document.getElementById('search-overlay');
    if (overlay) {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex', 'bz-modal-open');
      if (window.BLUEZONE_APP && window.BLUEZONE_APP.lockBodyScroll) {
        window.BLUEZONE_APP.lockBodyScroll(false);
      }
    }
  }

  function renderSearchQuery(query) {
    const container = document.getElementById('search-results-container');
    if (!container) return;

    const products = window.BLUEZONE_PRODUCTS || [];
    const q = (query || '').toLowerCase().trim();

    const filtered = products.filter(p =>
      p.name.toLowerCase().includes(q) ||
      p.category.toLowerCase().includes(q) ||
      p.description.toLowerCase().includes(q) ||
      p.shortDesc.toLowerCase().includes(q)
    );

    if (filtered.length === 0) {
      container.innerHTML = `
        <div class="text-center py-16 text-[#E8DCC4]/70 space-y-3 col-span-2">
          <svg class="w-12 h-12 text-[#2A8FC2]/40 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <p class="text-base font-bold text-white">No formulations matched "${query}"</p>
          <p class="text-xs text-[#E8DCC4]/60 max-w-sm mx-auto">Try searching for "BLUE MIND", "Cognitive", "Immunity", "Energy", or "Joint".</p>
        </div>
      `;
      return;
    }

    container.innerHTML = filtered.map(p => `
      <div class="bg-[#062B49] p-4 rounded-xl border border-[#0A4F78]/40 hover:border-[#2A8FC2] flex items-center gap-4 transition-all shadow-md">
        <img src="${p.image}" alt="${p.name}" onerror="BLUEZONE_APP.handleImageFallback(this)" class="w-16 h-16 object-contain flex-shrink-0" />
        <div class="flex-1 min-w-0">
          <span class="text-[9px] font-bold uppercase tracking-widest text-[#2A8FC2]">${p.category}</span>
          <h4 class="text-sm font-black text-white truncate">${p.name}</h4>
          <p class="text-xs font-bold text-[#67B34A]">$${(p.price || 0).toFixed(2)}</p>
        </div>
        <a href="product.html?id=${p.id}" onclick="BLUEZONE_SEARCH.close()" class="px-4 py-2 rounded-lg bg-[#0A4F78] hover:bg-[#2A8FC2] text-white text-xs cursor-pointer font-extrabold uppercase transition-colors">
          VIEW
        </a>
      </div>
    `).join('');
  }

  window.BLUEZONE_SEARCH = {
    open: openSearch,
    close: closeSearch,
    query: renderSearchQuery
  };

  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('search-input');
    if (input) {
      input.addEventListener('input', (e) => {
        renderSearchQuery(e.target.value);
      });
    }
  });
})();
