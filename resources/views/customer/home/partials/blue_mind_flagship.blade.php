{{-- ================================================================
    07. FLAGSHIP PRODUCT HERO
    Dynamic product-driven section
    Expected variable: $mainProduct
================================================================ --}}

@php
    /*
    |--------------------------------------------------------------------------
    | Localized Product Data
    |--------------------------------------------------------------------------
    */
    $locale = app()->getLocale();

    $mainProductName =
        $locale === 'ar'
            ? $mainProduct->name_ar ?? $mainProduct->name_en
            : $mainProduct->name_en ?? $mainProduct->name_ar;

    $tagline =
        $locale === 'ar'
            ? $mainProduct->tagline_ar ?? $mainProduct->tagline_en
            : $mainProduct->tagline_en ?? $mainProduct->tagline_ar;

    $shortDescription =
        $locale === 'ar'
            ? $mainProduct->short_description_ar ?? $mainProduct->short_description_en
            : $mainProduct->short_description_en ?? $mainProduct->short_description_ar;

    /*
    |--------------------------------------------------------------------------
    | Product Image
    |--------------------------------------------------------------------------
    */
    $mainProductImage = $mainProduct->image ?: asset('assets/products/blue-mind.webp');

    if (!str_starts_with($mainProductImage, 'http')) {
        $mainProductImage = asset(ltrim($mainProductImage, '/'));
    }

    /*
    |--------------------------------------------------------------------------
    | Product Price
    |--------------------------------------------------------------------------
    */
    $displayPrice = $mainProduct->sale_price ?? ($mainProduct->price ?? 0);

    /*
    |--------------------------------------------------------------------------
    | Benefits / Ingredients
    |--------------------------------------------------------------------------
    */
    $benefits = is_array($mainProduct->benefits ?? null)
        ? $mainProduct->benefits
        : (json_decode($mainProduct->benefits ?? '[]', true) ?:
        []);

    $ingredients = is_array($mainProduct->ingredients ?? null)
        ? $mainProduct->ingredients
        : (json_decode($mainProduct->ingredients ?? '[]', true) ?:
        []);

    /*
    |--------------------------------------------------------------------------
    | Main Product URL
    |--------------------------------------------------------------------------
    */
    $mainProductUrl = route('customer.product.show', $mainProduct->slug);

    /*
    |--------------------------------------------------------------------------
    | Cart Identifier
    |--------------------------------------------------------------------------
    */
    $cartIdentifier = $mainProduct->slug;

    /*
    |--------------------------------------------------------------------------
    | RTL
    |--------------------------------------------------------------------------
    */
    $isRtl = $locale === 'ar';
@endphp


<section id="blue-mind-flagship" dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
    class="relative overflow-hidden border-b border-[#0A4F78]/10 bg-white py-20 text-[#031827] transition-colors dark:border-[#0A4F78]/30 dark:bg-[#031827] dark:text-white sm:py-24">
    {{-- Background ambient effects --}}
    <div aria-hidden="true"
        class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-[#2A8FC2]/10 blur-3xl dark:bg-[#2A8FC2]/20">
    </div>

    <div aria-hidden="true"
        class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-[#0A4F78]/10 blur-3xl dark:bg-[#0A4F78]/30">
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12 lg:gap-16">

            {{-- =========================================================
                PRODUCT VISUAL
            ========================================================== --}}
            <div class="lg:col-span-6">

                <div class="relative mx-auto w-full max-w-xl">

                    {{-- Product frame --}}
                    <div
                        class="group relative aspect-square overflow-hidden rounded-[2.5rem] border border-[#0A4F78]/20 bg-[#031827] shadow-2xl transition-all duration-500 hover:shadow-[0_25px_50px_-12px_rgba(10,79,120,0.35)] dark:border-[#2A8FC2]/40">

                        {{-- Product image edge-to-edge --}}
                        <img src="{{ $mainProductImage }}" alt="{{ $mainProductName }}" width="800"
                            height="800" loading="lazy" decoding="async"
                            class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-106"
                            onerror="this.onerror=null;this.src='{{ asset('assets/products/blue-mind.webp') }}';">

                        {{-- Elegant ambient gradient overlays & glass ring --}}
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#031827]/75 via-[#031827]/10 to-black/20"></div>
                        <div class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-white/20 rounded-[2.5rem]"></div>

                        {{-- Subtle Scientific Reticle Overlay --}}
                        <svg aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full opacity-20 mix-blend-screen"
                            viewBox="0 0 400 400" fill="none" preserveAspectRatio="xMidYMid meet">
                            <circle cx="200" cy="200" r="160" stroke="#2A8FC2" stroke-width="1"
                                stroke-dasharray="4 4" />
                            <circle cx="200" cy="200" r="110" stroke="#2A8FC2" stroke-width="1"
                                opacity="0.6" />
                            <line x1="40" y1="200" x2="360" y2="200" stroke="#2A8FC2"
                                stroke-width="0.5" />
                            <line x1="200" y1="40" x2="200" y2="360" stroke="#2A8FC2"
                                stroke-width="0.5" />
                        </svg>

                        {{-- Top product indicator --}}
                        <div
                            class="absolute left-5 top-5 z-20 inline-flex items-center gap-2 rounded-xl border border-white/40 bg-white/90 px-3.5 py-2 text-[10px] font-black uppercase tracking-wider text-[#0A4F78] shadow-lg backdrop-blur-md dark:border-[#2A8FC2]/50 dark:bg-[#031827]/90 dark:text-[#2A8FC2] sm:left-6 sm:top-6">
                            <i class="fa-solid fa-flask-vial text-xs" aria-hidden="true"></i>
                            <span>
                                {{ $isRtl ? 'تركيبة متقدمة' : 'Advanced Formula' }}
                            </span>
                        </div>

                        {{-- Bottom product indicator --}}
                        <div
                            class="absolute bottom-5 right-5 z-20 inline-flex items-center gap-2 rounded-xl border border-[#67B34A]/40 bg-white/95 px-3.5 py-2 text-[10px] font-black uppercase tracking-wider text-[#589c3e] shadow-lg backdrop-blur-md dark:border-[#67B34A]/50 dark:bg-[#031827]/90 dark:text-[#67B34A] sm:bottom-6 sm:right-6">
                            <i class="fa-solid fa-leaf text-xs" aria-hidden="true"></i>
                            <span>
                                {{ $isRtl ? 'تركيبة نباتية' : 'Botanical Formula' }}
                            </span>
                        </div>

                    </div>

                    {{-- Decorative scientific marker --}}
                    <div aria-hidden="true"
                        class="pointer-events-none absolute -bottom-4 left-1/2 h-8 w-32 -translate-x-1/2 rounded-full bg-[#2A8FC2]/20 blur-xl">
                    </div>
                </div>
            </div>


            {{-- =========================================================
                PRODUCT INFORMATION
            ========================================================== --}}
            <div class="lg:col-span-6">

                <div class="max-w-2xl space-y-6">

                    {{-- Section label --}}
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-[#2A8FC2]/40 bg-[#2A8FC2]/10 px-3 py-1.5 text-xs font-black uppercase tracking-[0.2em] text-[#0A4F78] dark:bg-[#2A8FC2]/20 dark:text-[#2A8FC2]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#2A8FC2]" aria-hidden="true"></span>

                        {{ $isRtl ? 'التركيبة المميزة' : 'Flagship Formulation' }}
                    </div>


                    {{-- Product name --}}
                    <div class="space-y-3">

                        <h2
                            class="text-4xl font-black leading-[0.95] tracking-tight text-[#031827] dark:text-white sm:text-5xl lg:text-6xl">
                            {{ $mainProductName }}
                        </h2>

                        @if ($tagline)
                            <p class="text-base font-bold tracking-wide text-[#0A4F78] dark:text-[#2A8FC2] sm:text-lg">
                                {{ $tagline }}
                            </p>
                        @endif

                    </div>


                    {{-- Rating / product metadata --}}
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-3">

                        @if (isset($mainProduct->rating))
                            <div class="flex items-center gap-2">

                                <div class="flex items-center gap-1"
                                    aria-label="{{ $mainProduct->rating }} out of 5 stars">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <i class="fa-solid fa-star text-xs {{ $star <= round((float) $mainProduct->rating) ? 'text-[#F2B84B]' : 'text-[#CBD5E1]' }}"
                                            aria-hidden="true"></i>
                                    @endfor
                                </div>

                                <span class="text-sm font-bold text-[#031827]/70 dark:text-[#E8DCC4]/70">
                                    {{ number_format((float) $mainProduct->rating, 1) }}
                                </span>

                                @if (isset($mainProduct->reviews_count))
                                    <span class="text-xs text-[#031827]/50 dark:text-white/50">
                                        ({{ number_format($mainProduct->reviews_count) }})
                                    </span>
                                @endif

                            </div>
                        @endif

                        @if ($mainProduct->brand)
                            <span
                                class="text-xs font-bold uppercase tracking-widest text-[#0A4F78]/60 dark:text-[#7EA5B8]">
                                {{ $mainProduct->brand }}
                            </span>
                        @endif

                    </div>


                    {{-- Description --}}
                    @if ($shortDescription)
                        <p
                            class="max-w-xl text-sm font-medium leading-7 text-[#031827]/75 dark:text-[#E8DCC4] sm:text-base">
                            {{ $shortDescription }}
                        </p>
                    @endif


                    {{-- =====================================================
                        INGREDIENTS
                    ====================================================== --}}
                    @if (count($ingredients))
                        <div class="space-y-3 pt-1">

                            <span
                                class="block text-[10px] font-black uppercase tracking-[0.18em] text-[#0A4F78] dark:text-[#7EA5B8]">
                                {{ $isRtl ? 'المكونات الرئيسية' : 'Key Active Ingredients' }}
                            </span>

                            <div class="flex flex-wrap gap-2">

                                @foreach (array_slice($ingredients, 0, 6) as $ingredient)
                                    @php
                                        $ingredientName = is_array($ingredient)
                                            ? ($isRtl
                                                ? $ingredient['name_ar'] ?? ($ingredient['name_en'] ?? '')
                                                : $ingredient['name_en'] ?? ($ingredient['name_ar'] ?? ''))
                                            : $ingredient;

                                        $ingredientDose = is_array($ingredient) ? $ingredient['dose'] ?? null : null;
                                    @endphp

                                    @if ($ingredientName)
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-[#0A4F78]/15 bg-[#F6F5EF] px-3 py-1.5 text-xs font-bold text-[#031827] transition-colors dark:border-[#2A8FC2]/30 dark:bg-[#062B49] dark:text-[#F6F5EF]">
                                            <span>{{ $ingredientName }}</span>

                                            @if ($ingredientDose)
                                                <span class="text-[#0A4F78]/60 dark:text-[#7EA5B8]">
                                                    · {{ $ingredientDose }}
                                                </span>
                                            @endif
                                        </span>
                                    @endif
                                @endforeach

                            </div>
                        </div>
                    @endif


                    {{-- =====================================================
                        PRICE / ACTIONS
                    ====================================================== --}}
                    <div
                        class="flex flex-col gap-5 border-t border-[#0A4F78]/15 pt-6 dark:border-[#0A4F78]/50 sm:flex-row sm:flex-wrap sm:items-center">

                        {{-- Price --}}
                        <div class="min-w-[150px]">

                            @if (
                                $mainProduct->sale_price !== null &&
                                    $mainProduct->price !== null &&
                                    (float) $mainProduct->sale_price < (float) $mainProduct->price)
                                <div class="flex items-center gap-2">

                                    <span class="text-sm font-bold text-[#031827]/40 line-through dark:text-white/40">
                                        @currency($mainProduct->price)
                                    </span>

                                    <span
                                        class="rounded-md bg-[#67B34A]/10 px-2 py-0.5 text-[10px] font-black uppercase text-[#589c3e] dark:text-[#67B34A]">
                                        {{ $isRtl ? 'عرض' : 'Sale' }}
                                    </span>

                                </div>
                            @endif

                            <div class="flex items-baseline gap-1">

                                <span class="text-3xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                                    @currency($displayPrice)
                                </span>

                            </div>

                            @if ($mainProduct->product_size)
                                <span
                                    class="mt-1 block text-[10px] font-bold uppercase tracking-widest text-[#0A4F78]/60 dark:text-[#7EA5B8]">
                                    {{ $mainProduct->product_size }}
                                </span>
                            @endif

                        </div>


                        {{-- Actions --}}
                        <div class="flex flex-1 flex-col gap-3 sm:flex-row">

                            {{-- Add to cart --}}
                            <button type="button"
                                onclick="if(window.BLUEZONE_CART){BLUEZONE_CART.add('{{ $cartIdentifier }}', 1);}"
                                class="btn-sheen inline-flex min-h-[52px] flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#2A8FC2] px-7 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl transition-all duration-300 hover:bg-[#0A4F78] hover:shadow-2xl focus:outline-none focus:ring-2 focus:ring-[#2A8FC2] focus:ring-offset-2 dark:focus:ring-offset-[#031827]">
                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>

                                <span>
                                    {{ $isRtl ? 'أضف إلى السلة' : 'Add to Cart' }}
                                </span>
                            </button>


                            {{-- Product details --}}
                            <a href="{{ $mainProductUrl }}"
                                class="inline-flex min-h-[52px] flex-1 items-center justify-center gap-2 rounded-xl border border-[#0A4F78]/30 px-6 py-4 text-center text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] transition-colors hover:border-[#0A4F78] dark:border-[#2A8FC2]/40 dark:text-white dark:hover:border-[#2A8FC2]">
                                <span>
                                    {{ $isRtl ? 'استكشف المنتج' : 'Explore Product' }}
                                </span>

                                <i class="fa-solid fa-arrow-right rtl:rotate-180" aria-hidden="true"></i>
                            </a>

                        </div>

                    </div>


                    {{-- =====================================================
                        BENEFITS
                    ====================================================== --}}
                    @if (count($benefits))
                        <div class="grid grid-cols-1 gap-3 pt-2 sm:grid-cols-2">

                            @foreach (array_slice($benefits, 0, 4) as $benefit)
                                <div
                                    class="flex items-start gap-3 rounded-xl border border-[#0A4F78]/10 bg-[#F6F5EF]/70 p-3 dark:border-[#2A8FC2]/20 dark:bg-[#062B49]/50">
                                    <span
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#67B34A]/10 text-[#589c3e] dark:bg-[#67B34A]/10 dark:text-[#67B34A]">
                                        <i class="fa-solid fa-check text-[10px]" aria-hidden="true"></i>
                                    </span>

                                    <span class="text-xs font-semibold leading-5 text-[#031827]/75 dark:text-white/75">
                                        {{ is_array($benefit) ? $benefit['text'] ?? ($benefit['name'] ?? '') : $benefit }}
                                    </span>
                                </div>
                            @endforeach

                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</section>
