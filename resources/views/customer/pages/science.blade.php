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
                        $image =
                            $product->image ?:
                            asset('assets/images/products/blue-mind.jpg.png');

                        $pSlug = is_array($product) ? $product['slug'] ?? '' : $product->slug ?? '';

                        $isRtl = app()->getLocale() === 'ar';

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
                                                                               dark:border-[#2A8FC2]/15">

                                    <div
                                        class="flex flex-col
                                                                                   sm:flex-row
                                                                                   sm:items-end
                                                                                   sm:justify-between
                                                                                   gap-5">

                                        {{-- Price --}}
                                        <div>

                                            <span
                                                class="block mb-1
                                                                                           text-[9px] font-black
                                                                                           uppercase tracking-[0.16em]
                                                                                           text-[#031827]/45
                                                                                           dark:text-[#F6F5EF]/45">
                                                {{ __('app.formulation_price') }}
                                            </span>

                                            <div
                                                class="text-sm font-black
                                                                                           text-[#031827]
                                                                                           dark:text-[#F6F5EF]">
                                                @currency($product['price'])
                                            </div>

                                        </div>


                                        {{-- Actions --}}
                                        <div
                                            class="flex flex-col sm:flex-row
                                                                                       gap-2 w-full sm:w-auto">

                                            {{-- Science Details --}}
                                            <a href="{{ route('customer.science.product', $pSlug) }}"
                                                class="inline-flex items-center
                                                                                           justify-center gap-2
                                                                                           min-h-[46px]
                                                                                           px-5 py-3
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
                                                                                           text-[10px] font-black
                                                                                           uppercase tracking-wider
                                                                                           transition-all
                                                                                           whitespace-nowrap">
                                                <i class="fa-solid fa-flask-vial" aria-hidden="true"></i>

                                                <span>
                                                    {{ __('app.our_science_details') }}
                                                </span>
                                            </a>


                                            {{-- Product --}}
                                            <a href="{{ route('customer.products', $product) }}"
                                                class="inline-flex items-center
                                                                                           justify-center gap-2
                                                                                           min-h-[46px]
                                                                                           px-5 py-3
                                                                                           rounded-xl
                                                                                           bg-[#0A4F78]
                                                                                           hover:bg-[#083D5D]
                                                                                           dark:bg-[#2A8FC2]
                                                                                           dark:hover:bg-[#67B34A]
                                                                                           text-white
                                                                                           text-[10px] font-black
                                                                                           uppercase tracking-wider
                                                                                           transition-all
                                                                                           shadow-sm hover:shadow-lg
                                                                                           whitespace-nowrap">
                                                <span>
                                                    {{ __('app.view_product') }}
                                                </span>

                                                <i class="fa-solid {{ $isRtl ? 'fa-arrow-left' : 'fa-arrow-right' }}"
                                                    aria-hidden="true"></i>
                                            </a>

                                        </div>

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
                                <a href="{{ route('customer.products', $product) }}"
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
    </script>

</x-layouts.customer>
