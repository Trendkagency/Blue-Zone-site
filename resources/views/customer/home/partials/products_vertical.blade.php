    <!-- 06.5 PRODUCTS VERTICAL SECTION (STRUCTURED VERTICAL SHOWCASE) -->
    <section id="products-vertical" class="py-24 bg-[#F6F5EF] dark:bg-[#031827] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-3xl mx-auto space-y-4">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#2A8FC2]/20 dark:text-[#2A8FC2] text-xs font-black uppercase tracking-widest border border-[#0A4F78]/20">
            {{ app()->getLocale() === 'ar' ? 'الدليل المنظم للمنتجات' : 'STRUCTURED FORMULATION DIRECTORY' }}
          </div>
          <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
            {{ app()->getLocale() === 'ar' ? 'قائمة المنتجات المنظمة' : 'PRODUCTS VERTICAL DIRECTORY' }}
          </h2>
          <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
            {{ app()->getLocale() === 'ar' ? 'عرض رأسي منظم لكل منتج يوضح المكونات الفعالة، الجرعات السريرية، مسارات التأثير البيولوجي، ومواصفات الدفعة.' : 'A structured vertical breakdown of our clinical formulations—highlighting exact active compound potencies, cellular targets, and daily dosage protocols.' }}
          </p>
        </div>

        <!-- Vertical Products List -->
        <div class="space-y-6">
          @foreach($allProducts ?? [] as $product)
            <div class="bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 p-6 sm:p-8 hover:shadow-2xl transition-all duration-300 card-hover-lift group">
              <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center">
                
                <!-- Left: Product Image & Badges (Col 1-3) -->
                <div class="lg:col-span-3 flex flex-col items-center">
                  <div class="w-full aspect-square max-w-[220px] p-4 bg-[#F6F5EF] dark:bg-[#031827] rounded-2xl border border-[#0A4F78]/15 relative overflow-hidden flex items-center justify-center group-hover:border-[#67B34A]/50 transition-colors">
                    <img
                      src="{{ asset('assets/products/' . $product['slug'] . '.webp') }}"
                      alt="{{ $product['name_en'] }}"
                      onerror="this.onerror=null; this.src='{{ asset('assets/products/' . $product['slug'] . '.jpg') }}';"
                      width="200" height="200"
                      loading="lazy" decoding="async"
                      class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                    />
                    @if($product['is_new'] ?? false)
                      <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-[#67B34A] text-white text-[9px] font-black uppercase tracking-wider">
                        NEW
                      </span>
                    @elseif($product['is_best_seller'] ?? false)
                      <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-[#0A4F78] text-white text-[9px] font-black uppercase tracking-wider">
                        BESTSELLER
                      </span>
                    @endif
                  </div>
                  <div class="mt-3 flex items-center gap-2 text-[10px] font-bold text-slate-500">
                    <span>SKU: {{ $product['sku'] ?? 'BZ-001' }}</span>
                    <span>•</span>
                    <span class="text-[#67B34A]">HPLC Tested</span>
                  </div>
                </div>

                <!-- Center: Clinical Formula Details (Col 4-8) -->
                <div class="lg:col-span-6 space-y-4">
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-bold text-[#2A8FC2] uppercase tracking-wider">
                        {{ $product['category_en'] ?? 'Cellular Health' }}
                      </span>
                      <span class="text-slate-400">•</span>
                      <span class="text-xs font-medium text-slate-500">
                        {{ $product['package_size_en'] ?? '60 Veg Capsules' }}
                      </span>
                    </div>
                    <h3 class="text-2xl font-black text-[#031827] dark:text-[#F6F5EF] group-hover:text-[#67B34A] transition-colors mt-0.5">
                      <a href="{{ route('customer.product.show', $product['slug']) }}">
                        {{ app()->getLocale() === 'ar' ? ($product['name_ar'] ?? $product['name_en']) : $product['name_en'] }}
                      </a>
                    </h3>
                    <p class="text-xs text-[#0A4F78] dark:text-[#2A8FC2] font-semibold mt-0.5">
                      {{ app()->getLocale() === 'ar' ? ($product['tagline_ar'] ?? $product['tagline_en']) : $product['tagline_en'] }}
                    </p>
                  </div>

                  <p class="text-xs sm:text-sm text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? ($product['description_ar'] ?? $product['description_en']) : $product['description_en'] }}
                  </p>

                  <!-- Standardized Active Ingredients List -->
                  @if(!empty($product['ingredients']))
                    <div class="space-y-1.5">
                      <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">
                        {{ app()->getLocale() === 'ar' ? 'المكونات الفعالة المعايرة سريرياً:' : 'STANDARDIZED ACTIVE INGREDIENTS:' }}
                      </span>
                      <div class="flex flex-wrap gap-2">
                        @foreach($product['ingredients'] as $ing)
                          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 text-xs font-semibold text-[#031827] dark:text-[#F6F5EF]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#67B34A]"></span>
                            <span>{{ app()->getLocale() === 'ar' ? ($ing['name_ar'] ?? $ing['name_en']) : $ing['name_en'] }}</span>
                            <span class="text-[#0A4F78] dark:text-[#2A8FC2] font-mono font-bold">({{ $ing['dose'] }})</span>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  @endif

                  <!-- Dosage Protocol -->
                  <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#67B34A]/10 text-[#67B34A] text-xs font-bold border border-[#67B34A]/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? ($product['dosage_ar'] ?? $product['dosage_en']) : $product['dosage_en'] }}</span>
                  </div>
                </div>

                <!-- Right: Commerce & Actions (Col 9-12) -->
                <div class="lg:col-span-3 flex flex-col justify-between h-full bg-[#F6F5EF] dark:bg-[#031827] p-5 rounded-2xl border border-[#0A4F78]/15 space-y-4">
                  <div class="space-y-2">
                    <div class="flex items-center justify-between">
                      <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">PRICE</span>
                      <div class="flex items-center gap-1 text-xs font-bold text-[#67B34A]">
                        ★ {{ number_format($product['rating'] ?? 4.9, 1) }}
                        <span class="text-slate-400 text-[10px]">({{ $product['reviews_count'] ?? 100 }})</span>
                      </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                      <span class="text-3xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                        ${{ number_format($product['sale_price'] ?? $product['price'], 2) }}
                      </span>
                      @if(!empty($product['sale_price']) && $product['sale_price'] < $product['price'])
                        <span class="text-sm text-slate-400 line-through">
                          ${{ number_format($product['price'], 2) }}
                        </span>
                      @endif
                    </div>
                    <span class="text-[10px] font-bold text-[#67B34A] block">
                      ✓ {{ app()->getLocale() === 'ar' ? 'جاهز للشحن الفوري' : 'Ready to Dispatch' }}
                    </span>
                  </div>

                  <div class="space-y-2 pt-2 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/30">
                    <button
                      onclick="if(window.BLUEZONE_CART){window.BLUEZONE_CART.addItem({{ $product['id'] }}, '{{ addslashes($product['name_en']) }}', {{ $product['sale_price'] ?? $product['price'] }}, '{{ asset('assets/products/' . $product['slug'] . '.jpg') }}', 1);}"
                      class="w-full py-3 px-4 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md hover:scale-102 flex items-center justify-center gap-2 cursor-pointer btn-sheen">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                      <span>{{ app()->getLocale() === 'ar' ? 'أضف للسلة' : 'ADD TO CART' }}</span>
                    </button>
                    <a href="{{ route('customer.product.show', $product['slug']) }}" class="block w-full py-2.5 px-4 text-center text-xs font-bold rounded-xl border border-[#0A4F78]/30 hover:border-[#0A4F78] text-[#031827] dark:text-[#F6F5EF] hover:bg-[#0A4F78]/5 transition-colors">
                      {{ app()->getLocale() === 'ar' ? 'الملف الطبي الكامل' : 'CLINICAL MONOGRAPH →' }}
                    </a>
                  </div>
                </div>

              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
