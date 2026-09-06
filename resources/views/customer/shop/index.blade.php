<x-layouts.customer :title="'SHOP CATALOG — ' . __('app.brand_name')" :description="'Explore clinical dietary formulations by category, rating, and price. Science-backed nutrition for cognitive health, energy, immunity, and joint mobility.'">
    <div class="py-12 bg-[#F6F5EF] dark:bg-[#031827] min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Title & Filters Bar -->
        <div class="space-y-6">
          <div class="space-y-2 text-center sm:text-left">
            <span class="text-xs font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">FORMULATION CATALOG</span>
            <h1 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF]">EXPLORE ALL PRODUCTS</h1>
          </div>

          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-[#062B49] p-4 rounded-2xl border border-[#0A4F78]/15 shadow-sm">
            
            <!-- Category Tabs -->
            <div class="flex flex-wrap gap-2" id="shop-category-tabs">
              <button onclick="filterShopCategory('ALL')" class="shop-cat-btn px-4 py-2 rounded-xl text-xs uppercase font-extrabold tracking-wider bg-[#0A4F78] text-white cursor-pointer transition-all">ALL</button>
              <button onclick="filterShopCategory('COGNITIVE')" class="shop-cat-btn px-4 py-2 rounded-xl text-xs uppercase font-extrabold tracking-wider bg-transparent text-[#031827] dark:text-white border border-[#0A4F78]/20 hover:border-[#0A4F78] cursor-pointer transition-all">COGNITIVE</button>
              <button onclick="filterShopCategory('ENERGY')" class="shop-cat-btn px-4 py-2 rounded-xl text-xs uppercase font-extrabold tracking-wider bg-transparent text-[#031827] dark:text-white border border-[#0A4F78]/20 hover:border-[#0A4F78] cursor-pointer transition-all">ENERGY</button>
              <button onclick="filterShopCategory('IMMUNITY')" class="shop-cat-btn px-4 py-2 rounded-xl text-xs uppercase font-extrabold tracking-wider bg-transparent text-[#031827] dark:text-white border border-[#0A4F78]/20 hover:border-[#0A4F78] cursor-pointer transition-all">IMMUNITY</button>
              <button onclick="filterShopCategory('WELLNESS')" class="shop-cat-btn px-4 py-2 rounded-xl text-xs uppercase font-extrabold tracking-wider bg-transparent text-[#031827] dark:text-white border border-[#0A4F78]/20 hover:border-[#0A4F78] cursor-pointer transition-all">WELLNESS</button>
            </div>

            <!-- Sort Selector -->
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
              <span class="text-xs font-bold uppercase text-[#031827]/60 dark:text-[#F6F5EF]/60">SORT BY:</span>
              <select id="shop-sort-select" onchange="sortShopProducts(this.value)" class="bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/30 rounded-xl p-2.5 text-xs font-extrabold text-[#031827] dark:text-white focus:outline-none focus:border-[#2A8FC2] cursor-pointer">
                <option value="FEATURED">FEATURED</option>
                <option value="PRICE_LOW">PRICE: LOW → HIGH</option>
                <option value="PRICE_HIGH">PRICE: HIGH → LOW</option>
                <option value="RATING">HIGHEST RATING</option>
              </select>
            </div>

          </div>
        </div>

        <!-- Product Grid (2 columns on mobile, 3 on desktop) -->
        <div id="shop-products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 transition-opacity duration-300">
          <!-- Fallback Server-Rendered Cards while JS initializes -->
          @foreach($products ?? [] as $product)
            <div class="bg-white dark:bg-[#062B49] rounded-3xl p-6 border border-[#0A4F78]/15 shadow-xl flex flex-col justify-between card-hover-lift">
              <div class="space-y-4">
                <div class="aspect-square rounded-2xl overflow-hidden bg-[#031827]/5 flex items-center justify-center relative p-6">
                  <span class="absolute top-3 left-3 text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">
                    {{ $product->category->name ?? 'SUPPLEMENT' }}
                  </span>
                  <a href="{{ route('customer.product.show', $product->slug) }}" class="w-full h-full flex items-center justify-center">
                    <img src="{{ $product->primary_image_url ?? asset('assets/products/blue-mind.jpg') }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.jpg') }}';" class="max-h-56 object-contain hover:scale-105 transition-transform" />
                  </a>
                </div>
                <div>
                  <div class="flex items-center justify-between text-xs font-bold text-[#67B34A] mb-1">
                    <span>★ 4.9 (120+ Reviews)</span>
                    <span class="text-[#031827]/50 dark:text-[#F6F5EF]/50 font-mono">60 CAPS</span>
                  </div>
                  <h3 class="text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                    <a href="{{ route('customer.product.show', $product->slug) }}" class="hover:text-[#67B34A] transition-colors">
                      {{ $product->name }}
                    </a>
                  </h3>
                  <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 line-clamp-2 mt-1 font-medium">
                    {{ $product->short_description ?? $product->description }}
                  </p>
                </div>
              </div>

              <div class="pt-6 border-t border-[#0A4F78]/10 mt-6 flex items-center justify-between gap-4">
                <div class="text-2xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                  ${{ number_format($product->price ?? 79, 2) }}
                </div>
                <div class="flex gap-2">
                  <button onclick="BLUEZONE_WISHLIST.toggle('{{ $product->slug }}')" class="p-3 rounded-xl border border-[#0A4F78]/30 hover:border-[#67B34A] text-[#0A4F78] dark:text-[#2A8FC2] hover:text-[#67B34A] transition-colors cursor-pointer">
                    ♥
                  </button>
                  <button onclick="BLUEZONE_CART.add('{{ $product->slug }}', 1)" class="px-5 py-3 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-wider transition-all btn-sheen cursor-pointer">
                    ADD TO CART
                  </button>
                </div>
              </div>
            </div>
          @endforeach
        </div>

      </div>
    </div>

    <!-- Client-Side Catalog Controller Script -->
    <script>
      let currentCategory = 'ALL';
      let currentSort = 'FEATURED';

      function renderShopGrid() {
        if (!window.BLUEZONE_PRODUCTS || !window.BLUEZONE_PRODUCTS.length) return;
        const grid = document.getElementById('shop-products-grid');
        if (!grid) return;

        let list = [...window.BLUEZONE_PRODUCTS];

        if (currentCategory !== 'ALL') {
          list = list.filter(p => p.category.toUpperCase() === currentCategory.toUpperCase());
        }

        if (currentSort === 'PRICE_LOW') {
          list.sort((a, b) => a.price - b.price);
        } else if (currentSort === 'PRICE_HIGH') {
          list.sort((a, b) => b.price - a.price);
        } else if (currentSort === 'RATING') {
          list.sort((a, b) => b.rating - a.rating);
        }

        grid.innerHTML = list.map(p => `
          <div class="bg-white dark:bg-[#062B49] rounded-3xl p-6 border border-[#0A4F78]/15 shadow-xl flex flex-col justify-between card-hover-lift">
            <div class="space-y-4">
              <div class="aspect-square rounded-2xl overflow-hidden bg-[#031827]/5 dark:bg-[#031827]/40 flex items-center justify-center relative p-6">
                <span class="absolute top-3 left-3 text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">
                  ${p.category}
                </span>
                <a href="/products/${p.id}" class="w-full h-full flex items-center justify-center">
                  <img src="${p.image}" alt="${p.name}" onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.jpg') }}';" class="max-h-56 object-contain hover:scale-105 transition-transform" />
                </a>
              </div>
              <div>
                <div class="flex items-center justify-between text-xs font-bold text-[#67B34A] mb-1">
                  <span>★ ${p.rating} (${p.reviewsCount} Reviews)</span>
                  <span class="text-[#031827]/50 dark:text-[#F6F5EF]/50 font-mono">60 CAPS</span>
                </div>
                <h3 class="text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                  <a href="/products/${p.id}" class="hover:text-[#67B34A] transition-colors">
                    ${p.name}
                  </a>
                </h3>
                <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 line-clamp-2 mt-1 font-medium">
                  ${p.description}
                </p>
              </div>
            </div>

            <div class="pt-6 border-t border-[#0A4F78]/10 mt-6 flex items-center justify-between gap-4">
              <div class="text-2xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                $${p.price.toFixed(2)}
              </div>
              <div class="flex gap-2">
                <button onclick="BLUEZONE_WISHLIST.toggle('${p.id}')" class="p-3 rounded-xl border border-[#0A4F78]/30 hover:border-[#67B34A] text-[#0A4F78] dark:text-[#2A8FC2] hover:text-[#67B34A] transition-colors cursor-pointer">
                  ♥
                </button>
                <button onclick="BLUEZONE_CART.add('${p.id}', 1)" class="px-5 py-3 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-wider transition-all btn-sheen cursor-pointer">
                  ADD TO CART
                </button>
              </div>
            </div>
          </div>
        `).join('');
      }

      function filterShopCategory(cat) {
        currentCategory = cat;
        const btns = document.querySelectorAll('#shop-category-tabs .shop-cat-btn');
        btns.forEach(b => {
          if (b.textContent.trim().toUpperCase() === cat.toUpperCase()) {
            b.className = 'shop-cat-btn px-4 py-2 rounded-xl text-xs uppercase font-extrabold tracking-wider bg-[#0A4F78] text-white cursor-pointer transition-all';
          } else {
            b.className = 'shop-cat-btn px-4 py-2 rounded-xl text-xs uppercase font-extrabold tracking-wider bg-transparent text-[#031827] dark:text-white border border-[#0A4F78]/20 hover:border-[#0A4F78] cursor-pointer transition-all';
          }
        });
        renderShopGrid();
      }

      function sortShopProducts(val) {
        currentSort = val;
        renderShopGrid();
      }

      document.addEventListener('DOMContentLoaded', () => {
        if (window.BLUEZONE_PRODUCTS && window.BLUEZONE_PRODUCTS.length) {
          renderShopGrid();
        }
      });
    </script>
</x-layouts.customer>
