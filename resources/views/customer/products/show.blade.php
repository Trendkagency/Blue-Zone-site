<x-layouts.customer :title="($product->name ?? 'PRODUCT') . ' — ' . __('app.brand_name')" :description="$product->short_description ?? 'Clinical formulation inspired by Blue Zone longevity research.'">
    <div class="py-12 bg-[#F6F5EF] dark:bg-[#031827] min-h-screen">
      <div id="product-detail-container" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- Top Back Navigation & Breadcrumb -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-[#0A4F78]/15">
          <a href="{{ route('customer.shop') }}" class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] hover:text-[#67B34A] transition-colors">
            ← BACK TO PRODUCTS
          </a>
          <div class="text-[11px] font-bold uppercase tracking-widest text-[#031827]/60 dark:text-[#F6F5EF]/60">
            <a href="{{ route('customer.home') }}" class="hover:text-[#0A4F78]">HOME</a> / 
            <a href="{{ route('customer.shop') }}" class="hover:text-[#0A4F78]">PRODUCTS</a> / 
            <span class="text-[#0A4F78] dark:text-[#2A8FC2]">{{ $product->name ?? 'BLUE MIND' }}</span>
          </div>
        </div>

        <!-- 1. PRODUCT HEADER & PURCHASE BOX -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
          
          <!-- Image Gallery Container -->
          <div class="lg:col-span-6 bg-white dark:bg-[#062B49] rounded-3xl p-8 border border-[#0A4F78]/20 shadow-2xl flex items-center justify-center relative img-zoom-container">
            <span class="absolute top-6 left-6 text-xs font-extrabold uppercase tracking-widest px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2]">
              {{ $product->category->name ?? 'COGNITIVE' }}
            </span>
            <img 
              id="product-main-img"
              src="{{ $product->primary_image_url ?? asset('assets/products/blue-mind.jpg') }}" 
              alt="{{ $product->name ?? 'Blue Mind' }}" 
              onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.jpg') }}';" 
              class="w-full max-h-[450px] object-contain filter drop-shadow-2xl hover:scale-105 transition-transform duration-500" 
            />
          </div>

          <!-- Product Buy Box -->
          <div class="lg:col-span-6 space-y-6">
            <div class="space-y-2">
              <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-[#67B34A]">★ 4.95</span>
                <span class="text-xs text-[#031827]/50 dark:text-[#F6F5EF]/50">(340+ Verified Clinical Reviews)</span>
              </div>
              <h1 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF]">
                {{ $product->name ?? 'BLUE MIND' }}
              </h1>
              <p class="text-xs uppercase font-extrabold tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">
                SYNAPTIC PLASTICITY & NEURAL LONGEVITY
              </p>
            </div>

            <div class="text-3xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
              ${{ number_format($product->price ?? 79, 2) }}
            </div>

            <p class="text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 leading-relaxed font-medium">
              {{ $product->description ?? 'BLUE MIND is a bio-identical nootropic matrix formulated with standardized Bacopa Monnieri, Centella Asiatica, and Phosphatidylserine to sustain neuroplasticity, memory recall, and deep daily focus.' }}
            </p>

            <!-- Quantity & Actions -->
            <div class="space-y-4 pt-4 border-t border-[#0A4F78]/20">
              <div class="flex items-center gap-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">QUANTITY:</span>
                <div class="flex items-center border border-[#0A4F78]/30 rounded-lg overflow-hidden bg-white dark:bg-[#031827]">
                  <button onclick="updateQty(-1)" class="px-3 py-2 cursor-pointer font-bold hover:bg-[#0A4F78]/10 text-[#031827] dark:text-white">-</button>
                  <span id="detail-qty-val" class="px-4 text-xs font-bold text-[#031827] dark:text-white">1</span>
                  <button onclick="updateQty(1)" class="px-3 py-2 cursor-pointer font-bold hover:bg-[#0A4F78]/10 text-[#031827] dark:text-white">+</button>
                </div>
              </div>

              <div class="flex gap-4">
                <button onclick="BLUEZONE_CART.add('{{ $product->slug ?? 'blue-mind' }}', currentDetailQty)" class="flex-1 py-4 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs uppercase font-extrabold tracking-widest shadow-xl cursor-pointer transition-transform active:scale-95 btn-sheen">
                  ADD TO CART
                </button>
                <button onclick="BLUEZONE_WISHLIST.toggle('{{ $product->slug ?? 'blue-mind' }}')" aria-label="Wishlist" class="p-4 rounded-xl border border-[#0A4F78] text-[#0A4F78] dark:text-[#2A8FC2] hover:bg-[#0A4F78]/10 font-bold cursor-pointer transition-all">
                  ♥
                </button>
              </div>
            </div>

            <!-- Clinical Features Badge List -->
            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-[#0A4F78]/10 text-xs font-bold text-[#031827]/70 dark:text-[#F6F5EF]/70">
              <div class="flex items-center gap-2">
                <span class="text-[#67B34A]">✓</span> 100% Bio-Identical Extracts
              </div>
              <div class="flex items-center gap-2">
                <span class="text-[#67B34A]">✓</span> Third-Party Heavy Metal Screened
              </div>
              <div class="flex items-center gap-2">
                <span class="text-[#67B34A]">✓</span> Zero Artificial Fillers
              </div>
              <div class="flex items-center gap-2">
                <span class="text-[#67B34A]">✓</span> Cold-Chain Sealed
              </div>
            </div>

          </div>
        </div>

        <!-- 2. INGREDIENTS & DOSAGE GROUP -->
        <div class="bg-white dark:bg-[#062B49] rounded-3xl p-8 sm:p-12 border border-[#0A4F78]/15 shadow-xl space-y-8">
          <div class="space-y-2">
            <span class="text-xs font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">STANDARDIZED PROFILE</span>
            <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF]">ACTIVE BIO-COMPOUNDS</h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl space-y-2 border border-[#0A4F78]/10">
              <div class="flex justify-between items-center text-xs font-bold">
                <span class="text-[#031827] dark:text-[#F6F5EF]">Bacopa Monnieri (55% Bacosides)</span>
                <span class="text-[#0A4F78] dark:text-[#2A8FC2] font-mono">350mg</span>
              </div>
              <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">Standardized for dendritic arborization and synaptic acetylcholine preservation.</p>
            </div>

            <div class="p-4 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl space-y-2 border border-[#0A4F78]/10">
              <div class="flex justify-between items-center text-xs font-bold">
                <span class="text-[#031827] dark:text-[#F6F5EF]">Phosphatidylserine Matrix</span>
                <span class="text-[#0A4F78] dark:text-[#2A8FC2] font-mono">150mg</span>
              </div>
              <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">Sunflower-derived phospholipid protecting neuronal cell membrane fluidity.</p>
            </div>

            <div class="p-4 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl space-y-2 border border-[#0A4F78]/10">
              <div class="flex justify-between items-center text-xs font-bold">
                <span class="text-[#031827] dark:text-[#F6F5EF]">Gotu Kola (Centella Asiatica)</span>
                <span class="text-[#0A4F78] dark:text-[#2A8FC2] font-mono">200mg</span>
              </div>
              <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">Okinawan centenarian botanical promoting cerebral microcirculation.</p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Product Detail Script -->
    <script>
      let currentDetailQty = 1;
      function updateQty(delta) {
        currentDetailQty = Math.max(1, currentDetailQty + delta);
        const qtyEl = document.getElementById('detail-qty-val');
        if (qtyEl) qtyEl.textContent = currentDetailQty;
      }
    </script>
</x-layouts.customer>
