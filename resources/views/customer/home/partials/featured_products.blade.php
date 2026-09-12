{{-- ================================================================
    06. FEATURED PRODUCTS — 3x2 PAGINATED GRID
================================================================ --}}

<section id="featured-products"
    class="relative overflow-hidden border-b border-[#0A4F78]/10 bg-white py-20 transition-colors dark:border-[#0A4F78]/30 dark:bg-[#062B49] sm:py-24">
    {{-- Ambient background --}}
    <div aria-hidden="true"
        class="pointer-events-none absolute -right-40 top-20 h-80 w-80 rounded-full bg-[#2A8FC2]/5 blur-3xl dark:bg-[#2A8FC2]/10">
    </div>

    <div aria-hidden="true"
        class="pointer-events-none absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-[#0A4F78]/5 blur-3xl dark:bg-[#0A4F78]/10">
    </div>


    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- =========================================================
            SECTION HEADER
        ========================================================== --}}
        <div class="mb-12 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">

            <div class="max-w-2xl space-y-3">

                <span
                    class="inline-block text-xs font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">
                    {{ __('app.clinical_formulations') }}
                </span>

                <h2 class="text-3xl font-black tracking-tight text-[#031827] dark:text-[#F6F5EF] sm:text-5xl">
                    {{ __('app.featured_products') }}
                </h2>

                <p class="max-w-xl text-sm leading-6 text-[#031827]/60 dark:text-[#F6F5EF]/60">
                    {{ __('app.featured_products_description', [], false) ?: 'Explore our science-backed formulations engineered for targeted health and performance.' }}
                </p>

            </div>

            {{-- View catalog --}}
            <a href="'{{ route('customer.products') }}"
                class="inline-flex w-fit items-center gap-2 rounded-xl border border-[#0A4F78]/20 px-5 py-3 text-xs font-black uppercase tracking-widest text-[#0A4F78] transition-all hover:border-[#0A4F78] hover:bg-[#0A4F78] hover:text-white dark:border-[#2A8FC2]/30 dark:text-[#2A8FC2] dark:hover:border-[#2A8FC2] dark:hover:bg-[#2A8FC2] dark:hover:text-white">
                <span>
                    {{ __('app.view_all_products') }}
                </span>

                <i class="fa-solid fa-arrow-right text-[10px] rtl:rotate-180" aria-hidden="true"></i>
            </a>

        </div>

        {{-- =========================================================
            PRODUCTS GRID
        ========================================================== --}}
        {{-- =========================================================
    FEATURED PRODUCTS
    Grid layout — 3 products per row
========================================================= --}}
        @if (!empty($featuredProducts) && count($featuredProducts) > 0)

            <section id="featured-products"
                class="border-b border-[#0A4F78]/10 bg-white py-24 transition-colors dark:bg-[#062B49]">

                <div class="mx-auto max-w-7xl space-y-12 px-4 sm:px-6 lg:px-8">
  

                    {{-- =====================================================
                PRODUCTS GRID
            ====================================================== --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3" aria-live="polite">

                        @foreach ($featuredProducts as $product)
                            @php
                                /*
                        |--------------------------------------------------------------------------
                        | Product data
                        |--------------------------------------------------------------------------
                        | $featuredProducts is currently an array of arrays.
                        | Therefore use array syntax instead of object syntax.
                        */

                                $locale = app()->getLocale();

                                $productName =
                                    $locale === 'ar'
                                        ? $product['name_ar'] ?? ($product['name_en'] ?? '')
                                        : $product['name_en'] ?? ($product['name_ar'] ?? '');

                                $categoryName =
                                    $locale === 'ar'
                                        ? $product['category_ar'] ?? ($product['category_en'] ?? '')
                                        : $product['category_en'] ?? ($product['category_ar'] ?? '');

                                $shortDescription =
                                    $locale === 'ar'
                                        ? $product['short_description_ar'] ?? ($product['short_description_en'] ?? '')
                                        : $product['short_description_en'] ?? ($product['short_description_ar'] ?? '');

                                $slug = $product['slug'] ?? '';

                                $image = !empty($product['image'])
                                    ? asset(ltrim($product['image'], '/'))
                                    : asset('assets/products/blue-mind.webp');

                                $price = $product['price'] ?? 0;

                                $salePrice = $product['sale_price'] ?? null;

                                $hasSale =
                                    $salePrice !== null && $price !== null && (float) $salePrice < (float) $price;

                                $displayPrice = $hasSale ? $salePrice : $price;

                                $rating = (float) ($product['rating'] ?? 0);

                                $reviewsCount = (int) ($product['reviews_count'] ?? 0);

                                $productUrl = route('customer.product.show', $slug);
                            @endphp


                            {{-- =================================================
                        PRODUCT
                    ================================================== --}}
                            <article
                                class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-[#0A4F78]/10 bg-[#F6F5EF] transition-all duration-300 hover:-translate-y-1 hover:border-[#2A8FC2]/30 hover:shadow-xl dark:border-[#2A8FC2]/20 dark:bg-[#031827]">

                                {{-- =================================================
                            PRODUCT IMAGE
                        ================================================== --}}
                                <a href="{{ $productUrl }}"
                                    class="relative block aspect-[4/3] overflow-hidden bg-[#031827]"
                                    aria-label="{{ $productName }}">

                                    {{-- Product image edge-to-edge --}}
                                    <img src="{{ $image }}" alt="{{ $productName }}" width="600"
                                        height="450" loading="lazy" decoding="async"
                                        class="h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-108"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/products/blue-mind.webp') }}';">

                                    {{-- Ambient gradient & glass ring --}}
                                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#031827]/60 via-transparent to-black/20"></div>
                                    <div class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-white/10"></div>

                                    {{-- Clinical formulation badge --}}
                                    <div
                                        class="absolute left-4 top-4 z-20 inline-flex items-center gap-1.5 rounded-lg border border-white/30 bg-white/90 px-2.5 py-1.5 text-[9px] font-black uppercase tracking-wider text-[#0A4F78] shadow-md backdrop-blur-md dark:border-[#2A8FC2]/40 dark:bg-[#031827]/90 dark:text-[#2A8FC2]">
                                        <i class="fa-solid fa-flask-vial" aria-hidden="true"></i>
                                        {{ __('app.clinical_formulation') }}
                                    </div>

                                    {{-- Sale badge --}}
                                    @if ($hasSale)
                                        <div
                                            class="absolute right-4 top-4 z-20 rounded-lg bg-[#67B34A] px-2.5 py-1.5 text-[9px] font-black uppercase tracking-wider text-white shadow-md">
                                            {{ __('app.sale') }}
                                        </div>
                                    @endif

                                </a>


                                {{-- =================================================
                            PRODUCT CONTENT
                        ================================================== --}}
                                <div class="flex flex-1 flex-col p-5">

                                    {{-- Category --}}
                                    @if ($categoryName)
                                        <span
                                            class="mb-2 text-[9px] font-black uppercase tracking-[0.18em] text-[#0A4F78]/60 dark:text-[#7EA5B8]">
                                            {{ $categoryName }}
                                        </span>
                                    @endif


                                    {{-- Product name --}}
                                    <h3 class="text-xl font-black tracking-tight text-[#031827] dark:text-white">

                                        <a href="{{ $productUrl }}" class="transition-colors hover:text-[#2A8FC2]">
                                            {{ $productName }}
                                        </a>

                                    </h3>


                                    {{-- =================================================
                                RATING
                            ================================================== --}}
                                    <div class="mt-2 flex items-center gap-2">

                                        <div class="flex items-center gap-0.5"
                                            aria-label="{{ number_format($rating, 1) }} out of 5">

                                            @for ($star = 1; $star <= 5; $star++)
                                                <i class="fa-solid fa-star text-[10px] {{ $star <= round($rating) ? 'text-[#F2B84B]' : 'text-[#CBD5E1]' }}"
                                                    aria-hidden="true"></i>
                                            @endfor

                                        </div>


                                        <span class="text-[11px] font-bold text-[#031827]/60 dark:text-white/50">
                                            {{ number_format($rating, 1) }}
                                        </span>


                                        @if ($reviewsCount > 0)
                                            <span class="text-[10px] text-[#031827]/40 dark:text-white/40">
                                                ({{ number_format($reviewsCount) }})
                                            </span>
                                        @endif

                                    </div>


                                    {{-- =================================================
                                DESCRIPTION
                            ================================================== --}}
                                    @if ($shortDescription)
                                        <p
                                            class="mt-4 line-clamp-3 text-xs font-medium leading-5 text-[#031827]/65 dark:text-[#F6F5EF]/60">
                                            {{ $shortDescription }}
                                        </p>
                                    @endif


                                    {{-- =================================================
                                PRODUCT FOOTER
                            ================================================== --}}
                                    <div
                                        class="mt-auto flex items-end justify-between gap-4 border-t border-[#0A4F78]/10 pt-5 dark:border-[#2A8FC2]/20">

                                        {{-- Price --}}
                                        <div>

                                            @if ($hasSale)
                                                <span
                                                    class="block text-[10px] font-bold text-[#031827]/35 line-through dark:text-white/35">
                                                    @currency($price)
                                                </span>
                                            @endif


                                            <span class="text-xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                                                @currency($displayPrice)
                                            </span>

                                        </div>


                                        {{-- Actions --}}
                                        <div class="flex items-center gap-2">

                                            {{-- Product details --}}
                                            <a href="{{ $productUrl }}"
                                                aria-label="{{ __('app.quick_view') }} {{ $productName }}"
                                                title="{{ __('app.quick_view') }}"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#0A4F78]/15 text-[#0A4F78] transition-all hover:border-[#2A8FC2] hover:bg-[#2A8FC2] hover:text-white dark:border-[#2A8FC2]/30 dark:text-[#2A8FC2]">

                                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"
                                                    aria-hidden="true"></i>

                                            </a>


                                            {{-- Add to cart --}}
                                            <button type="button"
                                                onclick="if(window.BLUEZONE_CART){window.BLUEZONE_CART.add('{{ $slug }}', 1);}"
                                                aria-label="{{ __('app.add_to_cart') }} {{ $productName }}"
                                                title="{{ __('app.add_to_cart') }}"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#2A8FC2] text-white shadow-sm transition-all hover:bg-[#0A4F78] hover:shadow-md">

                                                <i class="fa-solid fa-cart-plus text-xs" aria-hidden="true"></i>

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </article>
                        @endforeach

                    </div>


                    {{-- =========================================================
                CURRENT DATA INFORMATION
            ========================================================== --}}
                    <div class="flex items-center justify-center pt-4">
                        <span
                            class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#0A4F78]/50 dark:text-[#7EA5B8]">
                            {{ count($featuredProducts) }}
                            {{ __('app.featured_products') }}
                        </span>
                    </div>

                </div>

            </section>

    @else
        {{-- =========================================================
                EMPTY STATE
            ========================================================== --}}
        <div
            class="rounded-2xl border border-dashed border-[#0A4F78]/20 bg-[#F6F5EF]/50 px-6 py-16 text-center dark:border-[#2A8FC2]/20 dark:bg-[#031827]/30">
            <div
                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#2A8FC2]/10 text-[#2A8FC2]">
                <i class="fa-solid fa-flask-vial text-xl" aria-hidden="true"></i>
            </div>

            <h3 class="text-lg font-black text-[#031827] dark:text-white">
                {{ __('app.no_products_available') }}
            </h3>

            <p class="mt-2 text-sm text-[#031827]/50 dark:text-white/50">
                {{ __('app.check_back_soon') }}
            </p>
        </div>

        @endif

    </div>
</section>


{{-- ============================================================
     FEATURED PRODUCTS DATA
     ============================================================ --}}

@php
    $featuredProducts = [];

    foreach ($products as $product) {
        $featuredProducts[] = [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category?->name ?? '',
            'rating' => $product->rating ?? 0,
            'image' => $product->image ?? asset('assets/images/products/blue-mind.jpg.png'),
            'shortDesc' => $product->description ?? '',
            'price' => $product->price,
            'url' => route('customer.product.show', $product->slug),
        ];
    }
@endphp

<script>
    window.BLUEZONE_TRANSLATIONS =
        {{ Illuminate\Support\Js::from([
            'previousProducts' => __('app.previous_products'),
            'nextProducts' => __('app.next_products'),
            'goToPage' => __('app.go_to_page'),
            'toggleWishlist' => __('app.toggle_wishlist'),
            'addToCart' => __('app.add_to_cart'),
            'quickView' => __('app.quick_view'),
            'clinicalFormulation' => __('app.clinical_formulation'),
            'rating' => __('app.rating'),
            'noProducts' => __('app.no_products_available'),
        ]) }};

    window.BLUEZONE_PRODUCTS = {{ Illuminate\Support\Js::from($featuredProducts) }};
</script>
