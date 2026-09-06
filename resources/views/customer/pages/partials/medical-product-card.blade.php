@php
    $isRtl = app()->getLocale() === 'ar';
    $name = $isRtl ? (data_get($product, 'name_ar') ?: data_get($product, 'name_en')) : data_get($product, 'name_en');
    $tagline = $isRtl ? (data_get($product, 'tagline_ar') ?: data_get($product, 'tagline_en')) : (data_get($product, 'tagline_en') ?: data_get($product, 'short_description_en'));
    $slug = data_get($product, 'slug');
    $sku = data_get($product, 'sku', 'BZ-PROT-001');
    $price = (float) data_get($product, 'price', 0);
    $salePrice = data_get($product, 'sale_price');
    $rating = data_get($product, 'rating', 4.9);
    $reviewsCount = data_get($product, 'reviews_count', 128);
    $size = data_get($product, 'product_size', ($isRtl ? '60 كبسولة نباتية' : '60 Veg Capsules'));
    
    // Category label
    $catObj = data_get($product, 'category');
    $catName = $catObj 
        ? ($isRtl ? data_get($catObj, 'name_ar') : data_get($catObj, 'name_en'))
        : (data_get($product, 'category_en') ?: ($isRtl ? 'تركيبة طول العمر' : 'Longevity Formulation'));

    // Image resolution
    $rawImage = data_get($product, 'image', '');
    $imagePath = $rawImage ? (str_starts_with($rawImage, 'http') ? $rawImage : asset(ltrim($rawImage, '/'))) : asset('assets/products/blue-mind.jpg');

    // Clinical / Medical Information
    $mechanism = data_get($product, 'clinical_mechanism') 
        ?: ($isRtl 
            ? 'تستهدف هذه التركيبة تنشيط مسارات الاستقلاب الخلوي وتعزيز كفاءة الميتوكوندريا مع توفير حماية حيوية ضد الإجهاد التأكسدي.' 
            : 'Targeted biochemical mechanism modulating cellular metabolic pathways, optimizing mitochondrial ATP synthesis, and shielding against oxidative stress.');

    $ingredients = data_get($product, 'ingredients') ?: [];
    if (!is_array($ingredients) && is_string($ingredients)) {
        $ingredients = json_decode($ingredients, true) ?: [];
    }

    $benefits = $isRtl ? data_get($product, 'benefits_ar') : data_get($product, 'benefits_en');
    if (!is_array($benefits) && is_string($benefits)) {
        $benefits = json_decode($benefits, true) ?: [];
    }
    if (empty($benefits)) {
        $benefits = data_get($product, 'benefits_en') ?: [
            $isRtl ? 'دعم حيوي معتمد سريرياً' : 'Clinically validated biomarker support',
            $isRtl ? 'امتصاص فائق التوافر الحيوي' : 'Enhanced bioavailability matrix',
            $isRtl ? 'حماية خلوية مستدامة' : 'Sustained cellular protection'
        ];
    }

    $usage = $isRtl ? (data_get($product, 'usage_ar') ?: data_get($product, 'usage_en')) : data_get($product, 'usage_en');
    if (!$usage) {
        $usage = $isRtl ? 'تناول كبسولتين يومياً صباحاً مع كوب ماء ومصدر دهون صحية.' : 'Take 2 capsules daily every morning with 250ml water and healthy fats.';
    }

    $warnings = data_get($product, 'warnings') 
        ?: ($isRtl 
            ? 'استشر طبيبك في حال تناول أدوية سيولة الدم أو أثناء الحمل. خالٍ من المواد المعدلة وراثياً والمواد الحافظة.' 
            : 'Consult healthcare practitioner if taking anticoagulants or prescription medications. Non-GMO, 100% clean label.');

    $productId = data_get($product, 'id', 1);
@endphp

<article class="medical-product-card bg-white dark:bg-[#062B49] rounded-3xl border border-[#0A4F78]/20 dark:border-[#0A4F78]/35 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between overflow-hidden p-5 sm:p-7 relative group hover:border-[#67B34A]/50">
    <!-- Top System Badge & Verified Rating -->
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-2">
            <span class="text-[9px] sm:text-[10px] font-mono font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#0A4F78]/40 dark:text-[#2A8FC2] border border-[#0A4F78]/20">
                {{ $catName }}
            </span>
            <div class="flex items-center gap-1 text-[#67B34A] text-xs font-bold font-mono">
                <svg class="w-3.5 h-3.5 fill-current text-[#67B34A]" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                <span>{{ number_format($rating, 1) }}</span>
                <span class="text-[#031827]/40 dark:text-[#F6F5EF]/40 font-normal">({{ $reviewsCount }})</span>
            </div>
        </div>

        <!-- Product Image Box -->
        <a href="{{ route('customer.product.show', $slug) }}" class="block aspect-square sm:aspect-[4/3] rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] p-4 relative overflow-hidden group/img transition-all border border-[#0A4F78]/10 dark:border-[#0A4F78]/25">
            <img src="{{ $imagePath }}" 
                 alt="{{ $name }}" 
                 onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';" 
                 loading="lazy" 
                 decoding="async" 
                 class="w-full h-full object-contain group-hover/img:scale-108 transition-transform duration-500" />
            
            <div class="absolute bottom-2.5 inset-x-2.5 flex items-center justify-between pointer-events-none">
                <span class="text-[9px] font-mono font-bold px-2 py-0.5 rounded-md bg-white/90 dark:bg-[#031827]/90 text-[#031827] dark:text-[#F6F5EF] border border-[#0A4F78]/15 shadow-sm backdrop-blur-sm">
                    {{ $sku }}
                </span>
                <span class="text-[9px] font-bold px-2 py-0.5 rounded-md bg-[#67B34A]/15 text-[#67B34A] border border-[#67B34A]/30 backdrop-blur-sm">
                    {{ $size }}
                </span>
            </div>
        </a>

        <!-- Identity & Header -->
        <div class="space-y-1">
            <div class="flex items-baseline justify-between gap-2">
                <h3 class="text-xl sm:text-2xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight hover:text-[#67B34A] transition-colors">
                    <a href="{{ route('customer.product.show', $slug) }}">
                        {{ $name }}
                    </a>
                </h3>
                <div class="text-right">
                    @if($salePrice && $salePrice < $price)
                        <span class="text-xs text-gray-400 line-through mr-1">${{ number_format($price, 2) }}</span>
                        <span class="text-lg sm:text-xl font-black text-[#67B34A]">${{ number_format($salePrice, 2) }}</span>
                    @else
                        <span class="text-lg sm:text-xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">${{ number_format($price, 2) }}</span>
                    @endif
                </div>
            </div>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed line-clamp-2">
                {{ $tagline }}
            </p>
        </div>

        <!-- MEDICAL INFORMATION DOSSIER -->
        <div class="space-y-3 pt-2 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/25">
            <!-- 1. Clinical Mechanism of Action -->
            <div class="p-3.5 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827]/70 border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 space-y-1.5">
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-dna text-[#0A4F78] dark:text-[#2A8FC2] text-xs"></i>
                    <span class="text-[9px] font-mono font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">
                        {{ $isRtl ? 'آلية العمل الإكلينيكية (Biochemical Pathway)' : 'CLINICAL MECHANISM OF ACTION' }}
                    </span>
                </div>
                <p class="text-[11px] sm:text-xs text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-normal">
                    {{ $mechanism }}
                </p>
            </div>

            <!-- 2. Standardized Bioactive Ingredients -->
            @if(!empty($ingredients))
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-mono font-extrabold uppercase tracking-widest text-[#67B34A] flex items-center gap-1">
                            <i class="fa-solid fa-vial-circle-check text-xs"></i>
                            {{ $isRtl ? 'المكونات الفعالة المعايرة سريرياً:' : 'STANDARDIZED BIOACTIVES:' }}
                        </span>
                        <span class="text-[9px] text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono font-semibold">
                            {{ count($ingredients) }} {{ $isRtl ? 'مركبات' : 'Actives' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(array_slice($ingredients, 0, 4) as $ing)
                            @php
                                $ingName = $isRtl ? (data_get($ing, 'name_ar') ?: data_get($ing, 'name_en')) : data_get($ing, 'name_en');
                                $dose = data_get($ing, 'dose', '');
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 text-[#031827] dark:text-[#F6F5EF]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#67B34A]"></span>
                                <span>{{ $ingName }}</span>
                                @if($dose)
                                    <span class="font-mono text-[#0A4F78] dark:text-[#2A8FC2] font-black">({{ $dose }})</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 3. Key Clinical Endpoints / Biomarkers -->
            @if(!empty($benefits))
                <div class="space-y-1 pt-1">
                    <span class="text-[9px] font-mono font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] block">
                        {{ $isRtl ? 'الأهداف البيولوجية والنتائج:' : 'TARGET BIOMARKERS & ENDPOINTS:' }}
                    </span>
                    <ul class="space-y-1">
                        @foreach(array_slice($benefits, 0, 2) as $benefit)
                            <li class="flex items-start gap-1.5 text-[11px] text-[#031827]/80 dark:text-[#F6F5EF]/80 leading-snug">
                                <i class="fa-solid fa-circle-check text-[#67B34A] text-xs mt-0.5 shrink-0"></i>
                                <span>{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 4. Clinical Protocol & Safety Alert -->
            <div class="space-y-2 pt-1">
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#031827]/60 border border-gray-200/80 dark:border-gray-800 text-[10px] text-[#031827]/75 dark:text-[#F6F5EF]/75 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-[#0A4F78] dark:text-[#2A8FC2] text-xs shrink-0"></i>
                    <span class="font-semibold">{{ $isRtl ? 'البروتوكول اليومي:' : 'Daily Protocol:' }}</span>
                    <span class="truncate">{{ $usage }}</span>
                </div>

                <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[10px] text-amber-800 dark:text-amber-200 flex items-start gap-2">
                    <i class="fa-solid fa-shield-halved text-amber-500 text-xs mt-0.5 shrink-0"></i>
                    <span class="line-clamp-2 leading-relaxed">{{ $warnings }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Controls Footer -->
    <div class="pt-5 mt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 flex flex-col sm:flex-row items-center gap-2.5">
        <a href="{{ route('customer.science.product', $slug) }}" 
           class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-[#0A4F78]/10 hover:bg-[#0A4F78] dark:bg-[#2A8FC2]/15 dark:hover:bg-[#2A8FC2] text-[#0A4F78] hover:text-white dark:text-[#2A8FC2] dark:hover:text-white border border-[#0A4F78]/25 dark:border-[#2A8FC2]/30 text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2 text-center shadow-sm">
            <i class="fa-solid fa-flask-vial"></i>
            <span>{{ $isRtl ? 'تفاصيل العلوم والتركيبة' : 'Our Science Details' }}</span>
        </a>

        <button type="button" 
                onclick="if(window.BLUEZONE_CART){ BLUEZONE_CART.add({{ $productId }}, 1); if(window.toast){ window.toast.success('{{ $isRtl ? 'تمت إضافة ' . $name . ' إلى البروتوكول' : 'Added ' . $name . ' to Clinical Protocol' }}'); } } else { window.location.href='{{ route('customer.product.show', $slug) }}'; }"
                class="w-full sm:w-auto py-3 px-5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer btn-sheen whitespace-nowrap">
            <i class="fa-solid fa-cart-plus"></i>
            <span>{{ $isRtl ? 'أضف' : 'ADD' }}</span>
        </button>
    </div>
</article>
