    <!-- 06. FEATURED PRODUCTS SLIDER -->
    <section id="featured-products" class="py-24 bg-white dark:bg-[#062B49] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
          <div class="space-y-3">
            <span class="text-xs font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">CLINICAL FORMULATIONS</span>
            <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
              FEATURED PRODUCTS
            </h2>
          </div>
          
          <div class="flex items-center gap-3">
            <button onclick="if(window.BLUEZONE_PRODUCT_SLIDER){BLUEZONE_PRODUCT_SLIDER.prev();}" aria-label="Previous products" class="p-3.5 rounded-full bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 hover:bg-[#0A4F78] hover:text-white text-[#0A4F78] dark:text-[#2A8FC2] transition-colors cursor-pointer">
              ‹
            </button>
            <button onclick="if(window.BLUEZONE_PRODUCT_SLIDER){BLUEZONE_PRODUCT_SLIDER.next();}" aria-label="Next products" class="p-3.5 rounded-full bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 hover:bg-[#0A4F78] hover:text-white text-[#0A4F78] dark:text-[#2A8FC2] transition-colors cursor-pointer">
              ›
            </button>
          </div>
        </div>

        <div id="featured-products-container" class="relative overflow-hidden">
          <div id="featured-products-track" class="flex transition-transform duration-500 ease-out">
            <!-- Dynamically populated or rendered -->
          </div>
        </div>

        <div id="featured-products-dots" class="flex justify-center items-center gap-2 pt-4"></div>
      </div>
    </section>
