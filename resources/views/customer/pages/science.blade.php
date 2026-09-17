<x-layouts.customer :title="__('app.our_science') . ' — ' . __('app.brand_name')" :description="__('app.science_page_description')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 space-y-16 sm:space-y-24">


        {{-- =========================================================
    3. CLINICAL FORMULATIONS
    ========================================================== --}}
        <section class="space-y-8 pt-4" id="bz-clinical-formulations-section"
            aria-labelledby="bz-clinical-formulations-title">

            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto space-y-3">

                <span
                    class="text-[10px] font-extrabold
                           uppercase tracking-[0.3em]
                           text-[#0A4F78]
                           dark:text-[#2A8FC2]">
                    {{ __('app.clinical_pharmacology_formulations') }}
                </span>

                <h2 id="bz-clinical-formulations-title"
                    class="text-3xl sm:text-4xl lg:text-5xl
                           font-light
                           text-[#031827]
                           dark:text-[#F6F5EF]
                           tracking-tight">
                    {{ __('app.all_longevity_formulations') }}
                    <span class="font-bold text-[#67B34A]">
                        {{ __('app.medical_data') }}
                    </span>
                </h2>

                <p
                    class="text-xs sm:text-sm
                           text-[#031827]/75
                           dark:text-[#F6F5EF]/75
                           font-medium
                           leading-relaxed
                           max-w-2xl mx-auto">
                    {{ __('app.clinical_formulations_description') }}
                </p>

            </div>


            {{-- =====================================================
      Product List
      Product 1 = Details Left / Image Right
      Product 2 = Image Left / Details Right
      Product 3 = Details Left / Image Right
      ...
      ====================================================== --}}
            <div id="bz-clinical-products-list" class="space-y-8">

                @forelse($products as $product)
                    @php
                        $pSlug = is_array($product) ? $product['slug'] ?? '' : $product->slug ?? '';
                        $pId = is_array($product) ? $product['id'] ?? 0 : $product->id ?? 0;
                        $fallback = \App\View\ViewModels\ProductViewModel::find($pSlug) ?? [];

                        $rawImage = data_get($product, 'image');
                        if ($product instanceof \App\Models\Product && !empty($product->primary_image_url)) {
                            $image = $product->primary_image_url;
                        } elseif (!empty($rawImage)) {
                            $image = str_starts_with($rawImage, 'http') ? $rawImage : asset(ltrim($rawImage, '/'));
                        } else {
                            $image = $fallback['image'] ?? asset('assets/products/blue-mind.webp');
                        }

                        $isRtl = app()->getLocale() === 'ar';

                        $pName = is_array($product) 
                            ? ($isRtl ? ($product['name_ar'] ?? $product['name_en'] ?? '') : ($product['name_en'] ?? ''))
                            : ($isRtl ? ($product->name_ar ?: $product->name) : ($product->name_en ?: $product->name));
                        $pName = $pName ?: ($isRtl ? ($fallback['name_ar'] ?? $fallback['name_en'] ?? '') : ($fallback['name_en'] ?? ''));

                        $tagline = is_array($product)
                            ? ($isRtl ? ($product['tagline_ar'] ?? $product['tagline_en'] ?? '') : ($product['tagline_en'] ?? ''))
                            : ($isRtl ? ($product->tagline_ar ?: $product->tagline_en) : $product->tagline_en);
                        $tagline = $tagline ?: ($isRtl ? ($fallback['tagline_ar'] ?? $fallback['tagline_en'] ?? '') : ($fallback['tagline_en'] ?? ''));

                        $sku = data_get($product, 'sku') ?: data_get($fallback, 'sku', 'BZ-PROT-001');
                        $price = is_array($product) ? (float)($product['sale_price'] ?? $product['price'] ?? 0) : (float)($product->sale_price ?? $product->price ?? 0);

                        $clinicalMech = data_get($product, 'clinical_mechanism') 
                            ?: data_get($product, 'professional_info.clinical_mechanism') 
                            ?: data_get($fallback, 'professional_info.clinical_mechanism') 
                            ?: ($isRtl ? 'تستهدف هذه التركيبة تنشيط مسارات الاستقلاب الخلوي وتعزيز كفاءة الميتوكوندريا مع توفير حماية حيوية ضد الإجهاد التأكسدي.' : 'Targeted biochemical mechanism modulating cellular metabolic pathways, optimizing mitochondrial ATP synthesis, and shielding against oxidative stress.');

                        $formulaDetails = data_get($product, 'formula_details') 
                            ?: data_get($product, 'professional_info.formula_details') 
                            ?: data_get($fallback, 'professional_info.formula_details');

                        $ingredients = data_get($product, 'ingredients') ?: data_get($fallback, 'ingredients', []);
                        if (is_string($ingredients)) { $ingredients = json_decode($ingredients, true) ?: []; }

                        $benefits = $isRtl ? data_get($product, 'benefits_ar') : data_get($product, 'benefits_en');
                        if (empty($benefits)) { $benefits = $isRtl ? data_get($fallback, 'benefits_ar') : data_get($fallback, 'benefits_en'); }
                        if (is_string($benefits)) { $benefits = json_decode($benefits, true) ?: []; }

                        $usage = $isRtl ? (data_get($product, 'usage_ar') ?: data_get($product, 'usage_en')) : data_get($product, 'usage_en');
                        if (empty($usage)) { $usage = $isRtl ? (data_get($fallback, 'usage_ar') ?: data_get($fallback, 'usage_en')) : data_get($fallback, 'usage_en'); }
                        if (empty($usage)) { $usage = $isRtl ? 'تناول كبسولتين يومياً كل صباح مع كوب ماء ومصدر دهون صحية كزيت الزيتون البكر الممتاز.' : 'Take 2 capsules daily every morning with 250ml mineral water alongside healthy fats.'; }

                        $contraindications = data_get($product, 'contraindications') ?: data_get($product, 'professional_info.contraindications') ?: data_get($fallback, 'professional_info.contraindications');
                        $warnings = data_get($product, 'warnings') ?: data_get($product, 'professional_info.warnings') ?: data_get($fallback, 'professional_info.warnings');
                        if (empty($warnings)) { $warnings = $isRtl ? 'يُحفظ بعيداً عن متناول الأطفال في مكان بارد وجاف. استشر طبيبك في حال تناول أدوية سيولة الدم أو أثناء الحمل.' : 'Keep out of reach of children. Store in a cool, dry place. Consult your physician if taking blood thinners or if pregnant.'; }

                        $collapseId = 'science-collapse-' . ($pSlug ? \Illuminate\Support\Str::slug($pSlug) : $loop->index);

                        /*
                         * Presentation-only layout state.
                         *
                         * Odd:
                         * Details = left
                         * Image   = right
                         *
                         * Even:
                         * Image   = left
                         * Details = right
                         */
                        $detailsOrder = $loop->even ? 'lg:order-2' : 'lg:order-1';

                        $imageOrder = $loop->even ? 'lg:order-1' : 'lg:order-2';
                    @endphp


                    <article
                        class="bz-clinical-product
                                                                   group relative overflow-hidden
                                                                   rounded-[28px]
                                                                   bg-white dark:bg-[#062B49]
                                                                   border border-[#0A4F78]/15
                                                                   dark:border-[#0A4F78]/30
                                                                   shadow-sm
                                                                   hover:shadow-2xl
                                                                   transition-all duration-500
                                                                   hover:-translate-y-1">

                        {{-- Accent --}}
                        <div class="absolute inset-x-0 top-0 h-[3px]
                                                                       bg-gradient-to-r
                                                                       from-[#0A4F78]
                                                                       via-[#2A8FC2]
                                                                       to-[#67B34A]"
                            aria-hidden="true"></div>


                        <div
                            class="grid grid-cols-1 lg:grid-cols-2
                                                                       min-h-[430px]">

                            {{-- =================================================
                  DETAILS
                  Mobile: order 2
                  Desktop: alternating
                  ================================================== --}}
                            <div
                                class="bz-details
                                                                           order-2 {{ $detailsOrder }}
                                                                           flex flex-col justify-between
                                                                           p-6 sm:p-8 lg:p-12 xl:p-14">

                                <div>

                                    {{-- Clinical Label --}}
                                    <div
                                        class="flex items-center gap-2 mb-3
                                                                                   text-[10px] font-black
                                                                                   uppercase tracking-[0.22em]
                                                                                   text-[#67B34A]">
                                        <span
                                            class="w-2 h-2 rounded-full
                                                                                       bg-[#67B34A]
                                                                                       shadow-[0_0_0_4px_rgba(103,179,74,0.12)]"
                                            aria-hidden="true"></span>

                                        {{ $isRtl ? __('app.clinical_formulation') : __('app.clinical_formulation') }}
                                    </div>


                                    {{-- Product Name --}}
                                    <h3
                                        class="text-2xl sm:text-3xl lg:text-[36px]
                                                                                   leading-tight
                                                                                   font-black tracking-tight
                                                                                   text-[#031827]
                                                                                   dark:text-[#F6F5EF]">
                                        {{ $product->name }}
                                    </h3>


                                    {{-- Description --}}
                                    @if ($product->description)
                                        <p
                                            class="mt-5 max-w-2xl
                                                                                                   text-sm leading-7
                                                                                                   text-[#031827]/65
                                                                                                   dark:text-[#F6F5EF]/65">
                                            {{ Str::limit(strip_tags($product->description), 260) }}
                                        </p>
                                    @endif


                                    {{-- Clinical Data --}}
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-2
                                                                                   gap-3 mt-7">

                                        {{-- Active Ingredients --}}
                                        <div
                                            class="rounded-2xl p-4
                                                                                       bg-[#F6F5EF]/70
                                                                                       dark:bg-[#031827]/60
                                                                                       border border-[#0A4F78]/10
                                                                                       dark:border-[#2A8FC2]/15">

                                            <div class="flex items-center gap-2 mb-2">

                                                <i class="fa-solid fa-flask text-sm
                                                                                               text-[#0A4F78]
                                                                                               dark:text-[#2A8FC2]"
                                                    aria-hidden="true"></i>

                                                <span
                                                    class="text-[9px] font-black
                                                                                               uppercase tracking-wider
                                                                                               text-[#031827]/50
                                                                                               dark:text-[#F6F5EF]/50">
                                                    {{ __('app.active_ingredients') }}
                                                </span>

                                            </div>

                                            <p
                                                class="text-xs font-bold leading-5
                                                                                           text-[#031827]
                                                                                           dark:text-[#F6F5EF]">
                                                {{ $product->active_ingredients ?? __('app.standardized_formula') }}
                                            </p>

                                        </div>


                                        {{-- Biomarkers --}}
                                        <div
                                            class="rounded-2xl p-4
                                                                                       bg-[#F6F5EF]/70
                                                                                       dark:bg-[#031827]/60
                                                                                       border border-[#0A4F78]/10
                                                                                       dark:border-[#2A8FC2]/15">

                                            <div class="flex items-center gap-2 mb-2">

                                                <i class="fa-solid fa-dna text-sm
                                                                                               text-[#0A4F78]
                                                                                               dark:text-[#2A8FC2]"
                                                    aria-hidden="true"></i>

                                                <span
                                                    class="text-[9px] font-black
                                                                                               uppercase tracking-wider
                                                                                               text-[#031827]/50
                                                                                               dark:text-[#F6F5EF]/50">
                                                    {{ __('app.target_biomarkers') }}
                                                </span>

                                            </div>

                                            <p
                                                class="text-xs font-bold leading-5
                                                                                           text-[#031827]
                                                                                           dark:text-[#F6F5EF]">
                                                {{ $product->target_biomarkers ?? __('app.clinical_biomarker_profile') }}
                                            </p>

                                        </div>


                                        {{-- Dosage --}}
                                        <div
                                            class="sm:col-span-2
                                                                                       rounded-2xl p-4
                                                                                       bg-[#F6F5EF]/70
                                                                                       dark:bg-[#031827]/60
                                                                                       border border-[#0A4F78]/10
                                                                                       dark:border-[#2A8FC2]/15">

                                            <div class="flex items-center gap-2 mb-2">

                                                <i class="fa-solid fa-prescription-bottle-medical
                                                                                               text-sm
                                                                                               text-[#0A4F78]
                                                                                               dark:text-[#2A8FC2]"
                                                    aria-hidden="true"></i>

                                                <span
                                                    class="text-[9px] font-black
                                                                                               uppercase tracking-wider
                                                                                               text-[#031827]/50
                                                                                               dark:text-[#F6F5EF]/50">
                                                    {{ __('app.dosage_protocol') }}
                                                </span>

                                            </div>

                                            <p
                                                class="text-xs font-bold leading-5
                                                                                           text-[#031827]
                                                                                           dark:text-[#F6F5EF]">
                                                {{ $product->dosage_protocol ?? __('app.clinical_protocol') }}
                                            </p>

                                        </div>

                                    </div>

                                </div>


                                {{-- Product Footer --}}
                                <div
                                    class="mt-8 pt-6
                                           border-t border-[#0A4F78]/10
                                           dark:border-[#2A8FC2]/15 space-y-3.5">

                                    {{-- Row 1: Price & Primary Add-to-Cart Action --}}
                                    <div class="flex items-center justify-between gap-3 flex-wrap">
                                        {{-- Price --}}
                                        <div>
                                            <span
                                                class="block mb-0.5
                                                       text-[9px] font-black
                                                       uppercase tracking-[0.16em]
                                                       text-[#031827]/45
                                                       dark:text-[#F6F5EF]/45">
                                                {{ __('app.formulation_price') }}
                                            </span>

                                            <div
                                                class="text-base sm:text-xl font-black
                                                       text-[#0A4F78]
                                                       dark:text-[#2A8FC2]">
                                                @currency($price)
                                            </div>
                                        </div>

                                        {{-- Add to Cart Action --}}
                                        <button type="button"
                                            onclick="addScienceCardToCart(this, '{{ $pSlug }}', {{ $pId }})"
                                            class="bz-add-to-cart-btn inline-flex items-center
                                                   justify-center gap-2
                                                   min-h-[44px]
                                                   px-5 sm:px-6 py-2.5
                                                   rounded-xl
                                                   bg-[#0A4F78]
                                                   hover:bg-[#062B49]
                                                   dark:bg-[#2A8FC2]
                                                   dark:hover:bg-[#1b6b94]
                                                   text-white
                                                   text-xs font-black
                                                   uppercase tracking-wider
                                                   transition-all
                                                   shadow-md hover:shadow-lg
                                                   whitespace-nowrap btn-sheen cursor-pointer"
                                            aria-label="{{ $isRtl ? 'أضف ' . $pName . ' إلى السلة' : 'Add ' . $pName . ' to Cart' }}">
                                            <i class="fa-solid fa-cart-plus text-sm" aria-hidden="true"></i>
                                            <span class="btn-text">{{ __('app.add_to_cart') }}</span>
                                        </button>
                                    </div>

                                    {{-- Row 2: Balanced 2-Column Actions for Collapse Toggle & Product Details --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                        {{-- Full Science Details Collapse Toggle --}}
                                        <button type="button"
                                            id="toggle-btn-{{ $collapseId }}"
                                            onclick="toggleScienceCollapse('{{ $collapseId }}', this)"
                                            aria-expanded="false"
                                            aria-controls="{{ $collapseId }}"
                                            class="bz-collapse-toggle-btn w-full inline-flex items-center
                                                   justify-center gap-2
                                                   min-h-[42px]
                                                   px-3 py-2.5
                                                   rounded-xl
                                                   bg-[#0A4F78]/10
                                                   hover:bg-[#0A4F78]
                                                   dark:bg-[#2A8FC2]/15
                                                   dark:hover:bg-[#2A8FC2]
                                                   text-[#0A4F78]
                                                   hover:text-white
                                                   dark:text-[#2A8FC2]
                                                   dark:hover:text-white
                                                   border border-[#0A4F78]/20
                                                   dark:border-[#2A8FC2]/25
                                                   text-[10px] sm:text-[11px] font-black
                                                   uppercase tracking-wider
                                                   transition-all
                                                   whitespace-nowrap cursor-pointer">
                                            <i class="fa-solid fa-flask-vial" aria-hidden="true"></i>
                                            <span class="toggle-text">{{ __('app.full_science_details') }}</span>
                                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 chevron-icon" aria-hidden="true"></i>
                                        </button>

                                        {{-- Product Details Page Link --}}
                                        <a href="{{ route('customer.product.show', $pSlug) }}"
                                            class="w-full inline-flex items-center
                                                   justify-center gap-1.5
                                                   min-h-[42px]
                                                   px-3 py-2.5
                                                   rounded-xl
                                                   bg-gray-100 hover:bg-gray-200
                                                   dark:bg-white/5 dark:hover:bg-white/10
                                                   text-[#031827]/80 hover:text-[#031827]
                                                   dark:text-[#F6F5EF]/80 dark:hover:text-white
                                                   border border-gray-200/80 dark:border-white/10
                                                   text-[10px] sm:text-[11px] font-black
                                                   uppercase tracking-wider
                                                   transition-all
                                                   whitespace-nowrap text-center">
                                            <span>{{ __('app.view_product') }}</span>
                                            <i class="fa-solid {{ $isRtl ? 'fa-arrow-left' : 'fa-arrow-right' }} text-[9px]"
                                                aria-hidden="true"></i>
                                        </a>
                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                  IMAGE
                  Mobile: order 1
                  Desktop: alternating
                  ================================================== --}}
                            <div
                                class="bz-image
                                                                           order-1 {{ $imageOrder }}
                                                                           relative
                                                                           min-h-[320px] lg:min-h-[430px]
                                                                           overflow-hidden
                                                                           bg-[#F6F5EF]
                                                                           dark:bg-[#031827]">

                                {{-- Decorative Background --}}
                                <div class="absolute -top-24 -right-24
                                                                               w-72 h-72 rounded-full
                                                                               bg-[#0A4F78]/5
                                                                               dark:bg-[#2A8FC2]/5
                                                                               blur-3xl pointer-events-none"
                                    aria-hidden="true">
                                </div>

                                <div class="absolute -bottom-24 -left-24
                                                                               w-72 h-72 rounded-full
                                                                               bg-[#67B34A]/5
                                                                               blur-3xl pointer-events-none"
                                    aria-hidden="true">
                                </div>


                                {{-- Category --}}
                                @if ($product->category)
                                    <div class="absolute top-6 right-6 z-10">
                                        <span
                                            class="inline-flex items-center gap-2
                                                                                                   px-3.5 py-2 rounded-xl
                                                                                                   bg-white/90
                                                                                                   dark:bg-[#062B49]/90
                                                                                                   backdrop-blur-md
                                                                                                   border border-[#0A4F78]/10
                                                                                                   dark:border-[#2A8FC2]/20
                                                                                                   shadow-sm
                                                                                                   text-[10px] font-black
                                                                                                   uppercase tracking-wider
                                                                                                   text-[#0A4F78]
                                                                                                   dark:text-[#2A8FC2]">
                                            <i class="fa-solid fa-layer-group" aria-hidden="true"></i>

                                            {{ $product->category->name }}
                                        </span>
                                    </div>
                                @endif


                                {{-- Product Number --}}
                                <div class="absolute top-6 left-6 z-10
                                                                               w-9 h-9 rounded-full
                                                                               flex items-center justify-center
                                                                               bg-white/80
                                                                               dark:bg-[#062B49]/80
                                                                               backdrop-blur-md
                                                                               border border-[#0A4F78]/10
                                                                               dark:border-[#2A8FC2]/20
                                                                               text-[10px] font-black
                                                                               text-[#0A4F78]
                                                                               dark:text-[#2A8FC2]"
                                    aria-label="{{ __('app.product_number') }} {{ $loop->iteration }}">
                                    {{ sprintf('%02d', $loop->iteration) }}
                                </div>


                                {{-- Product Image --}}
                                <a href="{{ route('customer.product.show', $pSlug) }}"
                                    class="relative z-[1]
                                                                               flex items-center justify-center
                                                                               w-full h-full
                                                                               min-h-[320px] lg:min-h-[430px]
                                                                               p-8 sm:p-12 lg:p-16
                                                                               focus:outline-none
                                                                               focus-visible:ring-2
                                                                               focus-visible:ring-inset
                                                                               focus-visible:ring-[#67B34A]"
                                    aria-label="{{ __('app.view_product') }}: {{ $product->name }}">

                                    <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy"
                                        decoding="async"
                                        onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.webp') }}';"
                                        class="max-w-full
                                                                                   max-h-[340px]
                                                                                   sm:max-h-[370px]
                                                                                   lg:max-h-[400px]
                                                                                   w-auto h-auto
                                                                                   object-contain
                                                                                   transition-transform
                                                                                   duration-700
                                                                                   ease-out
                                                                                   group-hover:scale-[1.06]" />

                                </a>

                            </div>

                        </div>

                        {{-- =========================================================
                             COLLAPSIBLE FULL OUR SCIENCE DETAILS DOSSIER
                             ========================================================= --}}
                        <div id="{{ $collapseId }}" 
                             class="bz-science-collapse hidden border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 bg-[#F6F5EF]/60 dark:bg-[#031827]/70 p-6 sm:p-10 transition-all duration-500">
                            
                            <div class="max-w-6xl mx-auto space-y-8">
                                {{-- Dossier Header Strip --}}
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#0A4F78]/10 dark:border-[#2A8FC2]/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-[#0A4F78]/15 dark:bg-[#2A8FC2]/20 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center justify-center text-lg shadow-sm shrink-0">
                                            <i class="fa-solid fa-microscope"></i>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-mono font-black uppercase tracking-[0.2em] text-[#0A4F78] dark:text-[#2A8FC2] block">
                                                {{ $isRtl ? 'الملف العلمي والسريري الشامل' : 'FULL CLINICAL SCIENCE DOSSIER' }}
                                            </span>
                                            <h4 class="text-lg sm:text-2xl font-black text-[#031827] dark:text-white tracking-tight">
                                                {{ $pName }} &bull; {{ $isRtl ? 'التحقق الجزيئي والدراسات السريرية' : 'Molecular Validation & Pharmacology' }}
                                            </h4>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-start sm:self-auto">
                                        <button type="button" 
                                                onclick="toggleScienceCollapse('{{ $collapseId }}')" 
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[10px] font-extrabold uppercase tracking-wider bg-white dark:bg-[#062B49] text-[#031827]/70 dark:text-white/70 hover:text-red-500 dark:hover:text-red-400 border border-[#0A4F78]/15 dark:border-[#2A8FC2]/20 shadow-sm transition-colors cursor-pointer">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                            <span>{{ $isRtl ? 'إغلاق التفاصيل' : 'Close Details' }}</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Grid of Science Content --}}
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                                    {{-- Left: Biochemical Pathway & Mechanism (7 cols) --}}
                                    <div class="lg:col-span-7 space-y-6">
                                        <div class="p-6 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 text-xs font-mono font-black uppercase tracking-wider text-[#589c3e] dark:text-[#67B34A]">
                                                <i class="fa-solid fa-dna"></i>
                                                <span>{{ $isRtl ? 'آلية التأثير الإكلينيكي على الخلايا' : 'Cellular Mechanism of Action' }}</span>
                                            </div>
                                            <p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium">
                                                {{ $clinicalMech }}
                                            </p>

                                            @if($formulaDetails)
                                                <div class="pt-4 border-t border-[#0A4F78]/10 dark:border-white/10 space-y-1.5">
                                                    <span class="block text-[10px] font-mono font-bold text-[#0A4F78] dark:text-[#2A8FC2] uppercase tracking-wider">
                                                        {{ $isRtl ? 'معايير النقاء والاستخلاص المعياري' : 'Extraction Purity & Bio-Standardization' }}:
                                                    </span>
                                                    <p class="text-xs text-[#031827]/75 dark:text-white/75 font-mono leading-relaxed">
                                                        {{ $formulaDetails }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Documented Biomarkers & Endpoints --}}
                                        @if(!empty($benefits))
                                            <div class="p-6 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm space-y-3">
                                                <div class="flex items-center gap-2 text-xs font-mono font-black uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2]">
                                                    <i class="fa-solid fa-chart-line"></i>
                                                    <span>{{ $isRtl ? 'المؤشرات الحيوية والنتائج المثبتة سريرياً' : 'Documented Biomarkers & Clinical Endpoints' }}</span>
                                                </div>
                                                <div class="space-y-2.5 pt-1">
                                                    @foreach($benefits as $b)
                                                        <div class="flex items-start gap-2.5 text-xs text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium">
                                                            <span class="w-4 h-4 rounded-full bg-[#67B34A]/20 text-[#589c3e] dark:text-[#67B34A] flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                                                                <i class="fa-solid fa-check"></i>
                                                            </span>
                                                            <span>{{ $b }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Right: Quality Standards, Ingredients & Safety Protocol (5 cols) --}}
                                    <div class="lg:col-span-5 space-y-6">
                                        {{-- Molecular Quality Certifications --}}
                                        <div class="p-6 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm space-y-3">
                                            <span class="text-[10px] font-mono font-black uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] block">
                                                {{ $isRtl ? 'معايير الجودة الجزيئية المعتمدة' : 'Molecular Quality Standards' }}
                                            </span>
                                            <ul class="space-y-2.5 text-xs text-[#031827]/80 dark:text-[#F6F5EF]/80 font-semibold">
                                                <li class="flex items-center gap-2">
                                                    <i class="fa-solid fa-shield-check text-[#67B34A]"></i>
                                                    <span>{{ $isRtl ? 'مطابقة الهوية الجزيئية 100% (HPLC Verified)' : '100% Bio-Identical HPLC Verified' }}</span>
                                                </li>
                                                <li class="flex items-center gap-2">
                                                    <i class="fa-solid fa-shield-check text-[#67B34A]"></i>
                                                    <span>{{ $isRtl ? 'خالٍ من الكافيين والمنشطات الصناعية' : 'Zero Synthetic Stimulants & Fillers' }}</span>
                                                </li>
                                                <li class="flex items-center gap-2">
                                                    <i class="fa-solid fa-shield-check text-[#67B34A]"></i>
                                                    <span>{{ $isRtl ? 'مطابق لمواصفات cGMP الأوروبية' : 'European cGMP Certified Facility' }}</span>
                                                </li>
                                                <li class="flex items-center gap-2">
                                                    <i class="fa-solid fa-shield-check text-[#67B34A]"></i>
                                                    <span>{{ $isRtl ? 'مفحوص نقاوة ضد المعادن الثقيلة' : 'Heavy Metal & Contaminant Screened' }}</span>
                                                </li>
                                            </ul>
                                        </div>

                                        {{-- Standardized Bioactives List --}}
                                        @if(!empty($ingredients))
                                            <div class="p-6 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm space-y-3">
                                                <div class="flex items-center gap-2 text-xs font-mono font-black uppercase tracking-wider text-[#589c3e] dark:text-[#67B34A]">
                                                    <i class="fa-solid fa-leaf"></i>
                                                    <span>{{ $isRtl ? 'المكونات النشطة والجرعات المحددة' : 'Standardized Active Bioactives' }}</span>
                                                </div>
                                                <div class="space-y-2">
                                                    @foreach($ingredients as $ing)
                                                        @php
                                                            $ingName = is_array($ing) ? ($isRtl && !empty($ing['name_ar']) ? $ing['name_ar'] : ($ing['name_en'] ?? $ing['name'] ?? 'Bioactive')) : (string)$ing;
                                                            $ingDose = is_array($ing) ? ($ing['dose'] ?? $ing['amount'] ?? '') : '';
                                                        @endphp
                                                        <div class="p-2.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/10 dark:border-white/10 flex items-center justify-between text-xs font-bold">
                                                            <span class="text-[#031827] dark:text-white truncate">{{ $ingName }}</span>
                                                            @if($ingDose)
                                                                <span class="font-mono text-[11px] font-black text-[#0A4F78] dark:text-[#2A8FC2] px-2 py-0.5 rounded-md bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#2A8FC2]/30 shrink-0">{{ $ingDose }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Usage Protocol & Warnings --}}
                                        <div class="p-6 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm space-y-3">
                                            <div class="flex items-center gap-2 text-xs font-mono font-black uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2]">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                                <span>{{ $isRtl ? 'البروتوكول السريري والجرعة اليومية' : 'Clinical Protocol & Dosage' }}</span>
                                            </div>
                                            <p class="text-xs text-[#031827]/80 dark:text-[#F6F5EF]/80 leading-relaxed font-medium">
                                                {{ $usage }}
                                            </p>

                                            @if($contraindications)
                                                <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-800 dark:text-amber-200 flex items-start gap-2">
                                                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xs mt-0.5 shrink-0"></i>
                                                    <div class="leading-relaxed">
                                                        <strong class="block text-[10px] font-mono uppercase">{{ $isRtl ? 'موانع الاستخدام:' : 'Contraindications:' }}</strong>
                                                        {{ $contraindications }}
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-[#031827]/60 border border-gray-200/80 dark:border-gray-800 text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 flex items-start gap-2">
                                                <i class="fa-solid fa-shield-halved text-[#0A4F78] dark:text-[#2A8FC2] text-xs mt-0.5 shrink-0"></i>
                                                <span class="leading-relaxed">{{ $warnings }}</span>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                {{-- Bottom Quick Action Inside Collapse --}}
                                <div class="pt-4 border-t border-[#0A4F78]/10 dark:border-[#2A8FC2]/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 text-xs text-[#031827]/70 dark:text-white/70 font-mono">
                                        <span>SKU: <strong class="text-[#0A4F78] dark:text-[#2A8FC2]">{{ $sku }}</strong></span>
                                        <span>&bull;</span>
                                        <span>{{ $isRtl ? 'السعر السريري:' : 'Formulation Price:' }} <strong class="text-base font-black text-[#0A4F78] dark:text-[#2A8FC2]">@currency($price)</strong></span>
                                    </div>
                                    <div class="flex items-center gap-3 w-full sm:w-auto">
                                        <button type="button"
                                                onclick="addScienceCardToCart(this, '{{ $pSlug }}', {{ $pId }})"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-wider transition-all shadow-md cursor-pointer">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            <span>{{ __('app.add_to_cart') }}</span>
                                        </button>
                                        <button type="button"
                                                onclick="toggleScienceCollapse('{{ $collapseId }}')"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-gray-200/70 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/15 text-[#031827] dark:text-white text-xs font-bold uppercase tracking-wider transition-colors cursor-pointer">
                                            <i class="fa-solid fa-chevron-up text-xs"></i>
                                            <span>{{ $isRtl ? 'إغلاق التفاصيل' : 'Close Details' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </article>

                @empty

                    {{-- Empty State --}}
                    <div
                        class="rounded-[28px]
                                           bg-white dark:bg-[#062B49]
                                           border border-[#0A4F78]/15
                                           dark:border-[#0A8FC2]/20
                                           p-10 sm:p-14
                                           text-center">
                        <div
                            class="w-14 h-14 mx-auto mb-5
                                               rounded-2xl
                                               bg-[#0A4F78]/10
                                               dark:bg-[#2A8FC2]/15
                                               flex items-center justify-center">
                            <i class="fa-solid fa-flask-vial
                                                   text-xl
                                                   text-[#0A4F78]
                                                   dark:text-[#2A8FC2]"
                                aria-hidden="true"></i>
                        </div>

                        <h3
                            class="text-xl font-bold
                                               text-[#031827]
                                               dark:text-[#F6F5EF]">
                            {{ __('app.no_formulations_available') }}
                        </h3>

                        <p
                            class="mt-2 text-sm
                                               text-[#031827]/60
                                               dark:text-[#F6F5EF]/60">
                            {{ __('app.no_formulations_description') }}
                        </p>
                    </div>
                @endforelse

            </div>


            {{-- =====================================================
      Pagination
      ====================================================== --}}
            @if (method_exists($products, 'hasPages') && $products->hasPages())
                <div class="pt-6">
                    {{ $products->onEachSide(1)->links() }}
                </div>
            @endif

        </section>


        {{-- =========================================================
    4. FORMULATED WITH PURPOSE
    ========================================================== --}}
        <section class="space-y-8 pt-4" aria-labelledby="bz-philosophy-title">

            <div class="text-center max-w-2xl mx-auto space-y-3">

                <span
                    class="text-[11px] font-extrabold
                           uppercase tracking-[0.3em]
                           text-[#0A4F78]
                           dark:text-[#2A8FC2]">
                    {{ __('app.our_philosophy') }}
                </span>

                <h2 id="bz-philosophy-title"
                    class="text-3xl sm:text-4xl
                           font-light
                           text-[#031827]
                           dark:text-[#F6F5EF]
                           tracking-tight">
                    {{ __('app.formulated_with') }}
                    <span class="font-bold text-[#67B34A]">
                        {{ __('app.purpose') }}
                    </span>.
                </h2>

                <p
                    class="text-xs sm:text-sm
                           text-[#031827]/75
                           dark:text-[#F6F5EF]/75
                           font-medium">
                    {{ __('app.philosophy_description') }}
                </p>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Point 01 --}}
                <div class="space-y-3 p-2">

                    <div class="flex items-center gap-3">

                        <span
                            class="w-8 h-8 rounded-full
                                   bg-[#67B34A]/15
                                   text-[#67B34A]
                                   flex items-center justify-center
                                   font-mono font-bold text-xs">
                            01
                        </span>

                        <h3
                            class="text-sm font-bold
                                   uppercase tracking-wider
                                   text-[#031827]
                                   dark:text-[#F6F5EF]">
                            {{ __('app.ingredient_selection') }}
                        </h3>

                    </div>

                    <p
                        class="text-xs
                               text-[#031827]/70
                               dark:text-[#F6F5EF]/70
                               font-medium leading-relaxed">
                        {{ __('app.ingredient_selection_description') }}
                    </p>

                </div>


                {{-- Point 02 --}}
                <div class="space-y-3 p-2">

                    <div class="flex items-center gap-3">

                        <span
                            class="w-8 h-8 rounded-full
                                   bg-[#0A4F78]/15
                                   dark:bg-[#0A4F78]/40
                                   text-[#0A4F78]
                                   dark:text-[#2A8FC2]
                                   flex items-center justify-center
                                   font-mono font-bold text-xs">
                            02
                        </span>

                        <h3
                            class="text-sm font-bold
                                   uppercase tracking-wider
                                   text-[#031827]
                                   dark:text-[#F6F5EF]">
                            {{ __('app.formulation_thinking') }}
                        </h3>

                    </div>

                    <p
                        class="text-xs
                               text-[#031827]/70
                               dark:text-[#F6F5EF]/70
                               font-medium leading-relaxed">
                        {{ __('app.formulation_thinking_description') }}
                    </p>

                </div>


                {{-- Point 03 --}}
                <div class="space-y-3 p-2">

                    <div class="flex items-center gap-3">

                        <span
                            class="w-8 h-8 rounded-full
                                   bg-[#2A8FC2]/15
                                   text-[#2A8FC2]
                                   flex items-center justify-center
                                   font-mono font-bold text-xs">
                            03
                        </span>

                        <h3
                            class="text-sm font-bold
                                   uppercase tracking-wider
                                   text-[#031827]
                                   dark:text-[#F6F5EF]">
                            {{ __('app.everyday_wellness') }}
                        </h3>

                    </div>

                    <p
                        class="text-xs
                               text-[#031827]/70
                               dark:text-[#F6F5EF]/70
                               font-medium leading-relaxed">
                        {{ __('app.everyday_wellness_description') }}
                    </p>

                </div>

            </div>

        </section>


        {{-- =========================================================
    5. SCIENCE IN PRACTICE
    ========================================================== --}}
        <section
            class="bg-white dark:bg-[#062B49]
                   text-[#031827] dark:text-white
                   rounded-3xl
                   p-8 sm:p-12
                   border border-[#0A4F78]/20
                   dark:border-[#0A4F78]/40
                   shadow-xl
                   flex flex-col sm:flex-row
                   items-center justify-between
                   gap-6
                   transition-colors">

            <div class="space-y-2 text-center sm:text-left">

                <span
                    class="text-[10px] font-extrabold
                           uppercase tracking-[0.25em]
                           text-[#67B34A]">
                    {{ __('app.science_in_practice') }}
                </span>

                <h2
                    class="text-2xl sm:text-3xl
                           font-bold tracking-tight
                           text-[#031827]
                           dark:text-white">
                    {{ __('app.see_the_science_in_blue_mind') }}
                </h2>

                <p
                    class="text-xs sm:text-sm
                           text-[#031827]/75
                           dark:text-[#F6F5EF]/75
                           font-medium max-w-xl">
                    {{ __('app.blue_mind_science_description') }}
                </p>

            </div>


            <a href="{{ route('customer.product.show', 'blue-mind') }}"
                class="px-7 py-3.5
                       bg-[#67B34A]
                       hover:bg-[#589c3e]
                       text-white
                       text-xs font-extrabold
                       uppercase tracking-widest
                       rounded-xl
                       transition-all
                       shadow-md
                       shrink-0
                       whitespace-nowrap
                       hover:scale-105">
                {{ __('app.explore_blue_mind') }}

                <i class="fa-solid fa-arrow-right
                           rtl:rotate-180 ml-1.5"
                    aria-hidden="true"></i>
            </a>

        </section>

    </div>


    {{-- =============================================================
  SCIENCE CONTROLLER
  ============================================================= --}}
    <script>
        (() => {
            'use strict';

            const SCIENCE_DATA = [{
                    num: "01",
                    code: "01/04",
                    stage: @json(__('app.source')),
                    title: @json(__('app.from_nature')),
                    desc: @json(__('app.source_stage_description')),
                    img: @json(asset('assets/images/hero_longevity.jpg')),
                    chips: [
                        @json(__('app.standardized_botanical_extraction')),
                        @json(__('app.peak_potency_sourcing'))
                    ],
                    flowStep: 1
                },
                {
                    num: "02",
                    code: "02/04",
                    stage: @json(__('app.formulation')),
                    title: @json(__('app.precision_in_every_formula')),
                    desc: @json(__('app.formulation_stage_description')),
                    img: @json(asset('assets/products/blue-mind.webp')),
                    chips: [
                        @json(__('app.bio_identical_nutrient_ratios')),
                        @json(__('app.cellular_absorption_focus'))
                    ],
                    flowStep: 2
                },
                {
                    num: "03",
                    code: "03/04",
                    stage: @json(__('app.validation')),
                    title: @json(__('app.quality_you_can_trust')),
                    desc: @json(__('app.validation_stage_description')),
                    img: @json(asset('assets/images/blog-1.jpg')),
                    chips: [
                        @json(__('app.third_party_quality_verified')),
                        @json(__('app.zero_synthetic_additives'))
                    ],
                    flowStep: 3
                },
                {
                    num: "04",
                    code: "04/04",
                    stage: @json(__('app.wellness')),
                    title: @json(__('app.designed_for_daily_life')),
                    desc: @json(__('app.wellness_stage_description')),
                    img: @json(asset('assets/images/blog-2.jpg')),
                    chips: [
                        @json(__('app.cognitive_resilience')),
                        @json(__('app.daily_vitality_support'))
                    ],
                    flowStep: 4
                }
            ];


            const SELECTORS = {
                progress: '#bz-timeline-progress',
                desktopNodes: '#bz-science-desktop-timeline .bz-timeline-node',
                mobileNodes: '#bz-science-mobile-timeline .bz-mobile-node',
                panel: '#bz-science-panel',
                image: '#bz-science-active-img',
                code: '#bz-science-stage-code',
                number: '#bz-science-active-num',
                stage: '#bz-science-active-stage',
                title: '#bz-science-active-title',
                description: '#bz-science-active-desc',
                chips: '#bz-science-active-chips'
            };


            const getElement = (selector) => {
                return document.querySelector(selector);
            };


            const getElements = (selector) => {
                return document.querySelectorAll(selector);
            };


            function updateTimeline(index) {
                const progress = getElement(SELECTORS.progress);

                if (progress) {
                    const progressValues = [0, 33.3, 66.6, 100];
                    progress.style.width = `${progressValues[index]}%`;
                }


                getElements(SELECTORS.desktopNodes).forEach((node, nodeIndex) => {
                    const circle = node.querySelector('.node-circle');
                    const title = node.querySelector('.node-title');

                    if (nodeIndex === index) {
                        if (circle) {
                            circle.className =
                                'node-circle w-11 h-11 rounded-full ' +
                                'bg-[#67B34A] text-white flex items-center justify-center ' +
                                'font-mono text-xs font-black ' +
                                'shadow-[0_0_15px_rgba(103,179,74,0.4)] ' +
                                'scale-110 border-2 border-[#67B34A] transition-all';
                        }

                        if (title) {
                            title.className =
                                'node-title text-xs font-extrabold ' +
                                'uppercase tracking-widest text-[#67B34A]';
                        }

                    } else {

                        if (circle) {
                            circle.className =
                                'node-circle w-9 h-9 rounded-full ' +
                                'bg-[#F6F5EF] dark:bg-[#031827] ' +
                                'border-2 border-[#0A4F78]/30 ' +
                                'flex items-center justify-center ' +
                                'font-mono text-xs font-bold ' +
                                'text-[#031827]/50 dark:text-[#F6F5EF]/50 ' +
                                'transition-all group-hover:border-[#67B34A]';
                        }

                        if (title) {
                            title.className =
                                'node-title text-xs font-semibold ' +
                                'uppercase tracking-widest ' +
                                'text-[#031827]/50 dark:text-[#F6F5EF]/50 ' +
                                'group-hover:text-[#67B34A] ' +
                                'transition-colors';
                        }
                    }
                });


                getElements(SELECTORS.mobileNodes).forEach((node, nodeIndex) => {

                    if (nodeIndex === index) {
                        node.className =
                            'bz-mobile-node flex items-center gap-3 ' +
                            'py-2 text-xs font-bold text-[#67B34A]';
                    } else {
                        node.className =
                            'bz-mobile-node flex items-center gap-3 ' +
                            'py-2 text-xs font-medium ' +
                            'text-[#031827]/60 dark:text-[#F6F5EF]/60';
                    }
                });
            }


            function updateFlow(flowStep) {
                for (let step = 1; step <= 4; step++) {

                    const element = document.getElementById(
                        `bz-flow-step-${step}`
                    );

                    if (!element) continue;

                    element.className =
                        step <= flowStep ?
                        'text-[#67B34A] font-bold' :
                        'text-white/40 font-normal';
                }
            }


            function updateChips(chips) {
                const chipsContainer = getElement(SELECTORS.chips);

                if (!chipsContainer) return;

                const fragment = document.createDocumentFragment();

                chips.forEach((chip, index) => {

                    const span = document.createElement('span');

                    span.className =
                        index === 0 ?
                        'px-3 py-1.5 rounded-lg bg-[#67B34A]/15 text-[#67B34A] text-xs font-bold' :
                        'px-3 py-1.5 rounded-lg bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 text-[#031827] dark:text-[#F6F5EF] text-xs font-bold';

                    span.textContent = chip;

                    fragment.appendChild(span);
                });

                chipsContainer.replaceChildren(fragment);
            }


            function updatePanel(stage) {

                const image = getElement(SELECTORS.image);
                const code = getElement(SELECTORS.code);
                const number = getElement(SELECTORS.number);
                const stageElement = getElement(SELECTORS.stage);
                const title = getElement(SELECTORS.title);
                const description = getElement(SELECTORS.description);

                if (image) {
                    image.src = stage.img;
                    image.alt = stage.title;
                }

                if (code) {
                    code.textContent = stage.code;
                }

                if (number) {
                    number.textContent = stage.num;
                }

                if (stageElement) {
                    stageElement.textContent = stage.stage;
                }

                if (title) {
                    title.textContent = stage.title;
                }

                if (description) {
                    description.textContent = stage.desc;
                }

                updateChips(stage.chips);
            }


            function selectScience(index) {

                if (
                    !Number.isInteger(index) ||
                    index < 0 ||
                    index >= SCIENCE_DATA.length
                ) {
                    return;
                }

                const stage = SCIENCE_DATA[index];
                const panel = getElement(SELECTORS.panel);

                updateTimeline(index);
                updateFlow(stage.flowStep);

                if (!panel) {
                    updatePanel(stage);
                    return;
                }

                panel.style.opacity = '0.3';

                window.setTimeout(() => {
                    updatePanel(stage);
                    panel.style.opacity = '1';
                }, 180);
            }


            window.BLUEZONE_SCIENCE = {
                select: selectScience
            };


            // Initialize first stage.
            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    () => selectScience(0), {
                        once: true
                    }
                );
            } else {
                selectScience(0);
            }

        })();

        // Science Card Collapse & Protocol Cart Logic
        function toggleScienceCollapse(id, triggerBtn) {
            const collapseEl = document.getElementById(id);
            if (!collapseEl) return;
            const isHidden = collapseEl.classList.contains('hidden');
            const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';

            const btn = triggerBtn || document.getElementById('toggle-btn-' + id);

            if (isHidden) {
                collapseEl.classList.remove('hidden');
                collapseEl.style.maxHeight = '0px';
                collapseEl.style.opacity = '0';
                collapseEl.style.overflow = 'hidden';

                requestAnimationFrame(() => {
                    collapseEl.style.transition = 'max-height 0.45s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease-out';
                    collapseEl.style.maxHeight = (collapseEl.scrollHeight + 60) + 'px';
                    collapseEl.style.opacity = '1';
                });

                setTimeout(() => {
                    collapseEl.style.maxHeight = '';
                    collapseEl.style.overflow = '';
                }, 480);

                if (btn) {
                    btn.setAttribute('aria-expanded', 'true');
                    const chevron = btn.querySelector('.chevron-icon');
                    if (chevron) chevron.classList.add('rotate-180');
                    const textSpan = btn.querySelector('.toggle-text');
                    if (textSpan) textSpan.textContent = isAr ? 'إخفاء تفاصيل العلوم' : 'Hide Science Details';
                }
            } else {
                collapseEl.style.overflow = 'hidden';
                collapseEl.style.transition = 'max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease-in';
                collapseEl.style.maxHeight = collapseEl.scrollHeight + 'px';

                requestAnimationFrame(() => {
                    collapseEl.style.maxHeight = '0px';
                    collapseEl.style.opacity = '0';
                });

                setTimeout(() => {
                    collapseEl.classList.add('hidden');
                    collapseEl.style.maxHeight = '';
                    collapseEl.style.opacity = '';
                    collapseEl.style.overflow = '';
                }, 370);

                if (btn) {
                    btn.setAttribute('aria-expanded', 'false');
                    const chevron = btn.querySelector('.chevron-icon');
                    if (chevron) chevron.classList.remove('rotate-180');
                    const textSpan = btn.querySelector('.toggle-text');
                    if (textSpan) textSpan.textContent = isAr ? 'تفاصيل العلوم السريرية' : 'Full Science Details';
                }
            }
        }

        function addScienceCardToCart(btn, slug, id) {
            if (!btn) return;
            const origHtml = btn.innerHTML;
            const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';

            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-sm"></i> <span>${isAr ? 'جاري الإضافة...' : 'Adding...'}</span>`;

            const identifier = slug || id;

            if (window.BLUEZONE_CART && typeof window.BLUEZONE_CART.add === 'function') {
                window.BLUEZONE_CART.add(identifier, 1);
                setTimeout(() => {
                    btn.innerHTML = `<i class="fa-solid fa-check text-green-400 text-sm"></i> <span>${isAr ? 'تمت الإضافة ✓' : 'Added ✓'}</span>`;
                    setTimeout(() => {
                        btn.innerHTML = origHtml;
                        btn.disabled = false;
                    }, 1800);
                }, 250);
            } else {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                fetch('{{ route('customer.cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: id || slug, quantity: 1 })
                })
                .then(res => res.json())
                .then(() => {
                    btn.innerHTML = `<i class="fa-solid fa-check text-green-400 text-sm"></i> <span>${isAr ? 'تمت الإضافة ✓' : 'Added ✓'}</span>`;
                    if (window.toast) {
                        window.toast.success(isAr ? 'تمت إضافة التركيبة إلى السلة بنجاح' : 'Added to Clinical Protocol Cart');
                    }
                    setTimeout(() => {
                        btn.innerHTML = origHtml;
                        btn.disabled = false;
                    }, 1800);
                })
                .catch(() => {
                    btn.innerHTML = origHtml;
                    btn.disabled = false;
                    window.location.href = '/products/' + slug;
                });
            }
        }
    </script>

</x-layouts.customer>
