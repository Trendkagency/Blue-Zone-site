@php
    $isRtl = app()->getLocale() === 'ar';
    $isObj = is_object($product);

    $pSlug = $isObj ? ($product->slug ?? 'blue-mind') : ($product['slug'] ?? 'blue-mind');
    $pId = $isObj ? ($product->id ?? 1) : ($product['id'] ?? 1);
    
    $pName = $isObj 
        ? ($isRtl && !empty($product->name_ar) ? $product->name_ar : ($product->name_en ?? $product->name ?? 'BLUE MIND'))
        : ($isRtl && !empty($product['name_ar']) ? $product['name_ar'] : ($product['name_en'] ?? $product['name'] ?? 'BLUE MIND'));
        
    $pTagline = $isObj
        ? ($isRtl && !empty($product->tagline_ar) ? $product->tagline_ar : ($product->tagline_en ?? 'SYNAPTIC PLASTICITY & NEURAL LONGEVITY'))
        : ($isRtl && !empty($product['tagline_ar']) ? $product['tagline_ar'] : ($product['tagline_en'] ?? 'SYNAPTIC PLASTICITY & NEURAL LONGEVITY'));

    $pCategory = $isObj 
        ? (($product->relationLoaded('category') && $product->category)
            ? ($isRtl && !empty($product->category->name_ar) ? $product->category->name_ar : ($product->category->name ?? 'COGNITIVE'))
            : 'COGNITIVE')
        : ($isRtl && !empty($product['category_ar']) ? $product['category_ar'] : ($product['category_en'] ?? 'COGNITIVE'));

    $rawImageVal = $isObj ? ($product->primary_image_url ?? $product->image) : ($product['image'] ?? null);
    $pImage = \App\Models\Product::normalizeUrl($rawImageVal);

    $gallerySource = $isObj 
        ? ($product->gallery_urls ?? $product->images ?? []) 
        : ($product['gallery_urls'] ?? $product['images'] ?? []);
    if (!is_array($gallerySource)) $gallerySource = [];

    $pImages = [];
    if (!empty($pImage)) {
        $pImages[] = $pImage;
    }
    foreach ($gallerySource as $gItem) {
        $normG = \App\Models\Product::normalizeUrl($gItem);
        if (!empty($normG) && !in_array($normG, $pImages, true)) {
            $pImages[] = $normG;
        }
    }
    if (empty($pImages)) {
        $pImages = [$pImage];
    }

    $pShortDesc = $isObj 
        ? ($isRtl && !empty($product->short_description_ar) ? $product->short_description_ar : ($product->short_description_en ?? $product->short_description ?? 'Precision clinical formulation inspired by Blue Zone longevity research.'))
        : ($isRtl && !empty($product['short_description_ar']) ? $product['short_description_ar'] : ($product['short_description_en'] ?? 'Precision clinical formulation inspired by Blue Zone longevity research.'));

    $pDescription = $isObj 
        ? ($isRtl && !empty($product->description_ar) ? $product->description_ar : ($product->description_en ?? $product->description ?? ''))
        : ($isRtl && !empty($product['description_ar']) ? $product['description_ar'] : ($product['description_en'] ?? ''));

    if (empty($pDescription)) {
        $pDescription = $pShortDesc;
    }

    $pPrice = $isObj ? ($product->price ?? 79) : ($product['price'] ?? 79);
    $pSalePrice = $isObj ? ($product->sale_price ?? null) : ($product['sale_price'] ?? null);
    $hasSale = $pSalePrice !== null && $pPrice !== null && (float) $pSalePrice < (float) $pPrice;
    $displayPrice = $hasSale ? $pSalePrice : $pPrice;
    $discountPercentage = $hasSale ? round((((float) $pPrice - (float) $pSalePrice) / (float) $pPrice) * 100) : 0;

    $pSku = $isObj ? ($product->sku ?? 'BZ-' . strtoupper(substr($pSlug, 0, 3)) . '-001') : ($product['sku'] ?? 'BZ-MND-001');
    $pRating = $isObj ? ($product->rating ?? 4.95) : ($product['rating'] ?? 4.95);
    $pReviewsCount = $isObj ? ($product->reviews_count ?? 340) : ($product['reviews_count'] ?? 340);
    $pStock = $isObj ? ($product->stock_online ?? 95) : ($product['stock_online'] ?? 95);

    // Product Details Dynamic Section Settings (resolved with product-level overrides & device responsiveness)
    $getSecConf = function (string $key) use ($product, $isObj) {
        if ($isObj && method_exists($product, 'getSectionConfig')) {
            return $product->getSectionConfig($key);
        }
        $dummy = new \App\Models\Product();
        if (is_array($product) && isset($product['section_settings'])) {
            $dummy->section_settings = $product['section_settings'];
        }
        return $dummy->getSectionConfig($key);
    };

    $secDossier = $getSecConf('dossier');
    $sec1 = $getSecConf('sec1');
    $sec2 = $getSecConf('sec2');
    $sec3 = $getSecConf('sec3');
    $sec4 = $getSecConf('sec4');
    $sec5 = $getSecConf('sec5');
    $sec6 = $getSecConf('sec6');

    // Dynamic Product Buttons Customizer Settings
    $buyBtnText = \App\Models\Setting::get('btn_buy_now_text_' . ($isRtl ? 'ar' : 'en'), ($isRtl ? 'إضافة إلى السلة والبروتوكول' : 'ADD TO CLINICAL PROTOCOL'));
    $buyBtnIcon = \App\Models\Setting::get('btn_buy_now_icon', 'fa-cart-shopping');
    $buyBtnSubtext = \App\Models\Setting::get('btn_buy_now_subtext_' . ($isRtl ? 'ar' : 'en'), ($isRtl ? 'تركيبة سريرية موثقة • نقاء دوائي 100%' : 'Clinically Verified Formulation • 100% Purity'));
    $bundleBtnText = \App\Models\Setting::get('btn_bundle_buy_text_' . ($isRtl ? 'ar' : 'en'), ($isRtl ? 'إضافة البروتوكول التكاملي بالكامل للسلة' : 'Add Complete Protocol to Cart'));
    $secBuyBox = $getSecConf('buy_box');
    $secRelated = $getSecConf('related');
    $bundleData = $isObj ? $product->getSynergisticBundle() : null;

    $pdSettings = [
        'dossier_enabled' => $secDossier['is_visible'],
        'dossier_badge' => $secDossier['badge'],
        'dossier_title' => $secDossier['title'],
        
        'sec1_enabled' => $sec1['is_visible'],
        'sec1_badge' => $sec1['badge'],
        'sec1_title' => $sec1['title'],

        'sec2_enabled' => $sec2['is_visible'],
        'sec2_badge' => $sec2['badge'],
        'sec2_title' => $sec2['title'],

        'sec3_enabled' => $sec3['is_visible'],
        'sec3_badge' => $sec3['badge'],
        'sec3_title' => $sec3['title'],

        'sec4_enabled' => $sec4['is_visible'],
        'sec4_badge' => $sec4['badge'],
        'sec4_title' => $sec4['title'],

        'sec5_enabled' => $sec5['is_visible'],
        'sec5_badge' => $sec5['badge'],
        'sec5_title' => $sec5['title'],

        'sec6_enabled' => $sec6['is_visible'],
        'sec6_badge' => $sec6['badge'],
        'sec6_title' => $sec6['title'],

        'related_enabled' => $secRelated['is_visible'],
        'related_badge' => $secRelated['badge'],
        'related_title' => $secRelated['title'],
    ];

    // Dynamic resolution helper
    $getVal = function ($fieldEn, $fieldAr = null) use ($product, $isRtl, $isObj) {
        if ($isRtl && $fieldAr) {
            $arVal = $isObj ? ($product->{$fieldAr} ?? null) : ($product[$fieldAr] ?? null);
            if (!empty($arVal)) return $arVal;
        }
        $enVal = $isObj ? ($product->{$fieldEn} ?? null) : ($product[$fieldEn] ?? null);
        if (!empty($enVal)) return $enVal;
        if ($fieldAr) {
            $fallbackAr = $isObj ? ($product->{$fieldAr} ?? null) : ($product[$fieldAr] ?? null);
            if (!empty($fallbackAr)) return $fallbackAr;
        }
        return null;
    };

    // Ingredients Resolution
    $rawIngredients = $isObj ? ($product->ingredients ?? []) : ($product['ingredients'] ?? []);
    if (is_string($rawIngredients)) {
        $rawIngredients = json_decode($rawIngredients, true) ?: [];
    }

    $defaultIngredientsBySlug = [
        'blue-mind' => [
            ['name_en' => 'Ginkgo Biloba Extract (50:1 Standardized)', 'name_ar' => 'مستخلص الجنكة بيلوبا المعياري (50:1)', 'dose' => '120 mg', 'role' => 'Cerebral perfusion & microvascular blood flow', 'origin' => 'Mediterranean Leaf'],
            ['name_en' => 'Phosphatidylserine Matrix', 'name_ar' => 'مصفوفة فوسفاتيديل سيرين النشطة', 'dose' => '150 mg', 'role' => 'Neuronal synaptic membrane fluidity & acetylcholine', 'origin' => 'Sunflower Lecithin'],
            ['name_en' => 'Phosphatidylcholine Complex', 'name_ar' => 'مركب فوسفاتيديل كولين النقي', 'dose' => '100 mg', 'role' => 'Neurotransmitter precursor & memory retention', 'origin' => 'Bio-Identical'],
            ['name_en' => 'Kaneka Ubiquinol (CoQ10)', 'name_ar' => 'يوبيكوينول كانيكا المنقى (CoQ10)', 'dose' => '100 mg', 'role' => 'Cellular mitochondrial respiratory chain defense', 'origin' => 'Bio-Fermented Yeast'],
            ['name_en' => 'Setria® L-Glutathione', 'name_ar' => 'إل-جلوتاثيون نقي (Setria®)', 'dose' => '50 mg', 'role' => 'Master neural intracellular antioxidant', 'origin' => 'Fermented Bio-Active'],
        ],
        'blue-cell' => [
            ['name_en' => 'Pharmaceutical β-NMN (99.8% Pure)', 'name_ar' => 'بيتا-NMN دوائي فائق النقاء (99.8%)', 'dose' => '300 mg', 'role' => 'Direct intracellular NAD+ biosynthetic precursor', 'origin' => 'Enzymatic Bio-Synthesis'],
            ['name_en' => 'Micronized Trans-Resveratrol', 'name_ar' => 'ترانس-ريسفيراترول ميكروني مجزأ', 'dose' => '200 mg', 'role' => 'Allosteric activator of longevity sirtuins SIRT1', 'origin' => 'Japanese Knotweed'],
            ['name_en' => 'Apigenin Botanical Complex', 'name_ar' => 'مركب الأبيجينين النباتي المركز', 'dose' => '50 mg', 'role' => 'CD38 enzymatic inhibitor preserving cellular NAD+', 'origin' => 'Chamomile Flower'],
            ['name_en' => 'PQQ (Pyrroloquinoline Quinone)', 'name_ar' => 'بيرولوكينولين كينون (PQQ)', 'dose' => '20 mg', 'role' => 'Stimulates de-novo mitochondrial biogenesis', 'origin' => 'Organic Ferment'],
        ],
        'blue-sleep' => [
            ['name_en' => 'Standardized Tart Cherry (Anthocyanins)', 'name_ar' => 'مستخلص الكرز الحامض المعياري', 'dose' => '400 mg', 'role' => 'Natural circadian melatonin peptide balance', 'origin' => 'Montmorency Cherry'],
            ['name_en' => 'Magnesium Bisglycinate Chelate', 'name_ar' => 'مغنيسيوم بيسجليسينات مخلبي', 'dose' => '250 mg', 'role' => 'GABAergic receptor relaxation & muscle recovery', 'origin' => 'Fully Chelated'],
            ['name_en' => 'L-Theanine Bio-Pure', 'name_ar' => 'إل-ثيانين نقي بيولوجياً', 'dose' => '200 mg', 'role' => 'Promotes alpha-brainwave architecture', 'origin' => 'Green Tea Extract'],
            ['name_en' => 'Valerian Root Supercritical Extract', 'name_ar' => 'مستخلص جذر الناردين عالي النقاوة', 'dose' => '150 mg', 'role' => 'Enhances slow-wave delta restorative sleep', 'origin' => 'European Botanical'],
        ],
        'blue-defense' => [
            ['name_en' => 'Elderberry Extract (Sambucus Nigra)', 'name_ar' => 'مستخلص الخلسان الأسود المركز', 'dose' => '500 mg', 'role' => 'Mucosal barrier defense & viral shielding', 'origin' => 'Wild European Berry'],
            ['name_en' => 'Liposomal Vitamin C', 'name_ar' => 'فيتامين سي الجسيمي فائق الامتصاص', 'dose' => '500 mg', 'role' => 'Phagocyte stimulation & collagen protection', 'origin' => 'Liposomal Matrix'],
            ['name_en' => 'Zinc Picolinate Bio-Chelate', 'name_ar' => 'زنك بيكولينات عالي التوافر الحيوي', 'dose' => '25 mg', 'role' => 'T-lymphocyte activation & thymus support', 'origin' => 'Chelated Mineral'],
            ['name_en' => 'Optimum Vitamin D3 (Cholecalciferol)', 'name_ar' => 'فيتامين D3 بالصورة الفعالة', 'dose' => '2500 IU', 'role' => 'Innate antimicrobial peptide modulation', 'origin' => 'Wild Lanolin'],
        ],
    ];

    $ingredientsList = !empty($rawIngredients) 
        ? $rawIngredients 
        : ($defaultIngredientsBySlug[$pSlug] ?? $defaultIngredientsBySlug['blue-mind']);

    // Benefits Resolution
    $rawBenefits = $isObj ? ($product->benefits_en ?? []) : ($product['benefits_en'] ?? []);
    if ($isRtl) {
        $arBenefits = $isObj ? ($product->benefits_ar ?? []) : ($product['benefits_ar'] ?? []);
        if (!empty($arBenefits)) $rawBenefits = $arBenefits;
    }
    if (is_string($rawBenefits)) {
        $rawBenefits = json_decode($rawBenefits, true) ?: [$rawBenefits];
    }

    $defaultBenefits = $isRtl ? [
        'تعزيز سرعة الاستجابة الإدراكية والتركيز الذهني العميق دون هبوط أو توتر عصبي',
        'حماية الميتوكوندريا العصبية والأوعية الدماغية الدقيقة من الإجهاد التأكسدي والالتهابات الخلوية',
        'دعم عامل التغذية العصبية BDNF وتحفيز مرونة المشابك المسؤولة عن الذاكرة طويلة المدى',
        'توفير طاقة خلوية مستدامة لأكثر من 8 ساعات متواصلة عبر تحسين تدفق الأكسجين'
    ] : [
        'Sustained mental sharpness & deep focus without central nervous system jitters or crashes',
        'Shields neuronal mitochondria & cerebral microvessels from oxidative decay and cellular stress',
        'Supports BDNF (Brain-Derived Neurotrophic Factor) and synaptic plasticity for learning recall',
        'Optimizes cellular ATP and oxygen perfusion across blood-brain neural networks for 8+ hours'
    ];

    $benefitsList = !empty($rawBenefits) ? $rawBenefits : $defaultBenefits;

    // Usage & Protocol Resolution
    $usageText = $getVal('usage_en', 'usage_ar') ?: ($isRtl 
        ? 'تناول كبسولتين صباحاً مع 250 مل من الماء النقي، يفضل مع مصدر دهون صحية كزيت الزيتون البكر الممتاز لتعزيز الامتصاص الحيوي للمركبات النشطة.' 
        : 'Take 2 capsules every morning with 250ml of pure mineral water, preferably alongside a healthy fat source such as cold-pressed extra virgin olive oil to maximize phospholipid bioavailability.');

    $dosageText = $getVal('dosage_en', 'dosage_ar') ?: ($isRtl ? 'كبسولتان نباتيتان يومياً' : '2 Vegetable Capsules Daily');
    $packageSize = $getVal('product_size', 'package_size_ar') ?: ($isRtl ? '60 كبسولة نباتية معوية (تكفي 30 يوماً)' : '60 Enteric Vegetable Capsules (30-Day Supply)');

    // Science & Cellular Mechanism Resolution
    $clinicalMechanism = $getVal('science_en', 'science_ar') 
        ?: ($isObj ? $product->clinical_mechanism : ($product['professional_info']['clinical_mechanism'] ?? null));

    if (empty($clinicalMechanism)) {
        $clinicalMechanism = $isRtl
            ? 'يعمل المستحضر عبر آلية دوائية ثلاثية: تحفيز تكوين الأستيل كولين العصبي، الحفاظ على سلامة الغشاء الفسفوليبيدي للخلية العصبية، وتحييد الجذور الحرة داخل الميتوكوندريا لحماية الطاقة الخلوية.'
            : 'Formulated to target the core bio-energetic pathways: activates presynaptic acetylcholine synthesis, stabilizes neuronal phospholipid membrane bilayer fluidity, and quenches reactive oxygen species within mitochondrial complexes.';
    }

    // Warnings & Contraindications Resolution
    $warningsText = $isObj ? ($product->warnings ?? null) : ($product['professional_info']['warnings'] ?? null);
    $contraindicationsText = $isObj ? ($product->contraindications ?? null) : ($product['professional_info']['contraindications'] ?? null);

    if (empty($warningsText)) {
        $warningsText = $isRtl 
            ? 'يُحفظ بعيداً عن متناول الأطفال في مكان جاف وبارد تحت 25 درجة مئوية بعيداً عن أشعة الشمس المباشرة. يُنصح باستشارة الطبيب المعالج لمن يتناولون مسيلات الدم أو الحوامل والمرضعات.'
            : 'Keep out of reach of children. Store in a cool, dry, dark environment below 25°C. Individuals on anticoagulants (blood thinners) or under specialized clinical supervision should consult their physician prior to use.';
    }
@endphp

<x-layouts.customer :title="$pName . ' — ' . __('app.brand_name')" :description="$pShortDesc">
    <!-- Inline Scoped CSS for Ultra-Smooth Collapsible Accordion Grid Animations -->
    <style>
        .bz-collapsible-grid {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.38s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            opacity: 0;
        }
        .bz-collapsible-item.is-open .bz-collapsible-grid {
            grid-template-rows: 1fr;
            opacity: 1;
        }
        .bz-collapsible-inner {
            overflow: hidden;
        }
        .bz-chevron-icon {
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .bz-collapsible-item.is-open .bz-chevron-icon {
            transform: rotate(180deg);
        }
        .bz-collapsible-item.is-open {
            border-color: rgba(103, 179, 74, 0.45);
            box-shadow: 0 12px 30px -10px rgba(10, 79, 120, 0.08);
        }
        .filter-tab-pill.active {
            background: #0A4F78;
            color: #ffffff;
            border-color: #0A4F78;
        }
        .dark .filter-tab-pill.active {
            background: #2A8FC2;
            color: #031827;
            border-color: #2A8FC2;
        }
        @keyframes bzPulseGlow {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.15); }
        }
        .bz-pulse-dot {
            animation: bzPulseGlow 2.5s infinite ease-in-out;
        }
    </style>

    <div class="py-10 sm:py-16 bg-[#F6F5EF] dark:bg-[#031827] min-h-screen transition-colors">
      <div id="product-detail-container" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- Top Back Navigation & Breadcrumb -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-[#0A4F78]/15">
          <a href="{{ route('customer.products') }}" class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] hover:text-[#67B34A] transition-colors">
            <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i> {{ $isRtl ? 'العودة لكتالوج المستحضرات' : 'BACK TO PRODUCTS' }}
          </a>
          <div class="text-[11px] font-bold uppercase tracking-widest text-[#031827]/60 dark:text-[#F6F5EF]/60">
            <a href="{{ route('customer.home') }}" class="hover:text-[#0A4F78] dark:hover:text-[#2A8FC2]">{{ $isRtl ? 'الرئيسية' : 'HOME' }}</a> / 
            <a href="{{ route('customer.products') }}" class="hover:text-[#0A4F78] dark:hover:text-[#2A8FC2]">{{ $isRtl ? 'المتجر' : 'PRODUCTS' }}</a> / 
            <span class="text-[#0A4F78] dark:text-[#2A8FC2]">{{ $pName }}</span>
          </div>
        </div>

        <!-- 1. PRODUCT HERO & BUY BOX -->
        @if($secBuyBox['is_visible'])
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start {{ $secBuyBox['responsive_class'] }}">
          
          <!-- Image Gallery Container -->
          <div class="lg:col-span-6 space-y-4">
            <div class="aspect-square rounded-3xl overflow-hidden border border-[#0A4F78]/20 shadow-2xl relative bg-[#031827] group">
              <img 
                id="product-main-img"
                src="{{ $pImage }}" 
                alt="{{ $pName }}" 
                width="600"
                height="600"
                fetchpriority="high"
                decoding="async"
                onerror="this.onerror=null; this.src='/assets/products/{{ $pSlug }}.jpg'; this.onerror=function(){this.src='/assets/products/blue-mind.jpg';};" 
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out" 
              />
              <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#031827]/50 via-transparent to-transparent"></div>
              <div class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-white/10 rounded-3xl"></div>
              
              <!-- Category Badge -->
              <span class="absolute top-5 left-5 rtl:left-auto rtl:right-5 z-10 text-xs font-black uppercase tracking-widest px-3.5 py-1.5 rounded-full bg-white/95 dark:bg-[#031827]/95 text-[#0A4F78] dark:text-[#2A8FC2] backdrop-blur-md shadow-md border border-white/40 dark:border-[#2A8FC2]/40">
                {{ $pCategory }}
              </span>

              <!-- Live Verification Pill -->
              <div class="absolute bottom-5 left-5 rtl:left-auto rtl:right-5 z-10 flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#031827]/85 backdrop-blur-md border border-white/15 text-[11px] text-white font-mono">
                <span class="w-2 h-2 rounded-full bg-[#67B34A] bz-pulse-dot"></span>
                <span>HPLC TRIPLE-ASSAYED</span>
              </div>
            </div>

            <!-- Thumbnail Carousel if Multiple Images -->
            @if(count($pImages) > 1)
              <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-none" id="product-thumbnails-container">
                @foreach($pImages as $idx => $tImg)
                  <button type="button" onclick="switchMainImage('{{ $tImg }}', this)" 
                    class="w-20 h-20 rounded-xl overflow-hidden border-2 transition-all shrink-0 cursor-pointer {{ $idx === 0 ? 'border-[#67B34A] ring-2 ring-[#67B34A]/30' : 'border-[#0A4F78]/20 opacity-70 hover:opacity-100' }}">
                    <img src="{{ $tImg }}" 
                         alt="{{ $pName }} thumbnail {{ $idx + 1 }}" 
                         width="80"
                         height="80"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover" 
                         onerror="this.onerror=null; this.closest('button').style.display='none';" />
                  </button>
                @endforeach
              </div>
            @endif
          </div>

          <!-- Product Buy Box Details -->
          <div class="lg:col-span-6 space-y-6">
            <div class="space-y-2">
              <div class="flex items-center gap-2.5 flex-wrap">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-[#67B34A]/15 text-[#67B34A] font-bold text-xs">
                  <i class="fa-solid fa-star text-amber-400"></i>
                  <span>{{ number_format($pRating, 2) }}</span>
                </div>
                <span class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 font-medium">
                  ({{ $pReviewsCount }}+ {{ $isRtl ? 'تقييم سريري معتمد' : 'Verified Clinical Reviews' }})
                </span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#2A8FC2]/10 dark:text-[#2A8FC2] font-mono font-bold">
                  SKU: {{ $pSku }}
                </span>
              </div>

              <h1 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                {{ $pName }}
              </h1>
              
              <p class="text-xs uppercase font-extrabold tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">
                {{ $pTagline }}
              </p>
            </div>

            <!-- Price & Stock Status -->
            <div class="flex items-center gap-3.5 flex-wrap">
              <div class="text-3xl sm:text-4xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                @currency($displayPrice)
              </div>
              @if($hasSale)
                <div class="text-xl line-through text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">
                  @currency($pPrice)
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#02131F]/90 dark:bg-[#062B49]/95 text-white border border-emerald-500/30 shadow-md">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                  <span class="font-mono text-emerald-400 dark:text-[#B8D98A] font-black text-xs tracking-tight">
                    {{ $isRtl ? "وفر {$discountPercentage}% اليوم" : "SAVE {$discountPercentage}% TODAY" }}
                  </span>
                </span>
              @endif
              <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full {{ $pStock > 10 ? 'bg-[#67B34A]/15 text-[#67B34A]' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400' }}">
                <span class="w-2 h-2 rounded-full {{ $pStock > 10 ? 'bg-[#67B34A]' : 'bg-amber-500' }}"></span>
                {{ $pStock > 10 ? ($isRtl ? 'متوفر بالمخزون البارد' : 'In Stock · Cold-Chain Ready') : ($isRtl ? 'كمية محدودة متبقية' : 'Limited Batch Remaining') }}
              </span>
            </div>

            <p class="text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium">
              {{ $pDescription }}
            </p>

            <!-- Dynamic Protocol Variant Selection Options -->
            @php
              // Calculate dynamic protocol duration pricing & discount directly from product data
              $bundleSavingsPct = ($bundleData && !empty($bundleData['savings_percentage'])) ? (int)$bundleData['savings_percentage'] : 0;
              $protocolDiscountPct = $bundleSavingsPct > 0 ? $bundleSavingsPct : ($discountPercentage > 0 ? $discountPercentage : 15);
              $protocolMultiPrice = round(($displayPrice * 3) * (1 - ($protocolDiscountPct / 100)), 2);
              $protocolMultiSavingsLabel = $isRtl ? "وفر {$protocolDiscountPct}%" : "SAVE {$protocolDiscountPct}%";
            @endphp
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 space-y-3 shadow-sm">
              <div class="flex items-center justify-between">
                <span class="text-xs font-black uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] flex items-center gap-1.5">
                  <i class="fa-solid fa-layer-group text-primary"></i> 
                  <span>{{ $isRtl ? 'اختر خطة البروتوكول العلاجي:' : 'Select Protocol Duration:' }}</span>
                </span>
                <span class="text-[10px] font-mono text-[#67B34A] font-bold">
                  {{ $isRtl ? 'خصم مخصص للمدة' : 'Controlled Protocol Pricing' }}
                </span>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="selectProtocolVariant(1, {{ $displayPrice }}, this)" 
                  class="protocol-variant-btn p-3.5 rounded-2xl border-2 border-[#67B34A] bg-[#67B34A]/5 text-left rtl:text-right transition-all cursor-pointer shadow-sm">
                  <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-black text-[#031827] dark:text-white">{{ $isRtl ? 'بروتوكول 30 يوماً' : '30-Day Starter' }}</span>
                    <span class="text-xs font-bold text-[#67B34A] bg-[#67B34A]/10 px-1.5 py-0.5 rounded-md font-mono">1x</span>
                  </div>
                  <span class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 block truncate">{{ $packageSize }}</span>
                </button>

                <button type="button" onclick="selectProtocolVariant(3, {{ $protocolMultiPrice }}, this)" 
                  class="protocol-variant-btn p-3.5 rounded-2xl border border-[#0A4F78]/20 hover:border-[#2A8FC2] text-left rtl:text-right transition-all cursor-pointer relative overflow-hidden bg-white/50 dark:bg-white/5 shadow-sm">
                  <span class="absolute top-0 right-0 rtl:right-auto rtl:left-0 text-[10px] font-mono font-black uppercase tracking-wider bg-[#02131F] dark:bg-[#0A4F78] text-emerald-400 dark:text-[#B8D98A] border-b border-l rtl:border-l-0 rtl:border-r border-emerald-500/30 px-2.5 py-0.5 rounded-bl-xl rtl:rounded-bl-none rtl:rounded-br-xl shadow-xs">
                    {{ $protocolMultiSavingsLabel }}
                  </span>
                  <div class="flex justify-between items-center mb-1 mt-1">
                    <span class="text-xs font-black text-[#031827] dark:text-white">{{ $isRtl ? 'بروتوكول 90 يوماً' : '90-Day Cellular' }}</span>
                    <span class="text-xs font-bold text-primary bg-primary/10 px-1.5 py-0.5 rounded-md font-mono">3x</span>
                  </div>
                  <span class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 block truncate">{{ $isRtl ? '3 عبوات كاملة (الأنسب للنتائج)' : '3 Complete Bottles (Recommended)' }}</span>
                </button>
              </div>
            </div>

            <!-- Quantity & Actions -->
            <div class="space-y-4 pt-2 border-t border-[#0A4F78]/20">
              <div class="flex items-center gap-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                  {{ $isRtl ? 'الكمية:' : 'QUANTITY:' }}
                </span>
                <div class="flex items-center border border-[#0A4F78]/30 rounded-xl overflow-hidden bg-white dark:bg-[#031827]">
                  <button type="button" onclick="updateQty(-1)" class="px-3.5 py-2.5 cursor-pointer font-bold hover:bg-[#0A4F78]/10 text-[#031827] dark:text-white transition-colors">
                    <i class="fa-solid fa-minus text-xs"></i>
                  </button>
                  <span id="detail-qty-val" class="px-4 text-xs font-bold text-[#031827] dark:text-white font-mono">1</span>
                  <button type="button" onclick="updateQty(1)" class="px-3.5 py-2.5 cursor-pointer font-bold hover:bg-[#0A4F78]/10 text-[#031827] dark:text-white transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                  </button>
                </div>
              </div>

              <div class="space-y-2">
                <div class="flex gap-4">
                  <button type="button" onclick="BLUEZONE_CART.add('{{ $pSlug }}', currentDetailQty); if(window.toast){ window.toast.success('{{ $isRtl ? 'تمت إضافة المستحضر إلى السلة بنجاح' : 'Added to Clinical Protocol Cart' }}'); }" 
                    class="flex-1 py-4 px-6 rounded-2xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs uppercase font-extrabold tracking-widest shadow-xl cursor-pointer transition-all active:scale-95 flex items-center justify-center gap-3">
                    <i class="fa-solid {{ $buyBtnIcon ?: 'fa-cart-shopping' }}"></i>
                    <span>{{ $buyBtnText }}</span>
                  </button>

                  <button type="button" onclick="BLUEZONE_WISHLIST.toggle('{{ $pSlug }}')" aria-label="Wishlist" 
                    class="p-4 rounded-2xl border-2 border-[#0A4F78]/30 dark:border-[#2A8FC2]/40 text-[#0A4F78] dark:text-[#2A8FC2] hover:bg-[#0A4F78]/10 font-bold cursor-pointer transition-all">
                    <i class="fa-regular fa-heart text-lg"></i>
                  </button>
                </div>

                @if(!empty($buyBtnSubtext))
                  <div class="text-[11px] font-mono text-[#67B34A] flex items-center justify-center gap-1.5 pt-0.5">
                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                    <span>{{ $buyBtnSubtext }}</span>
                  </div>
                @endif
              </div>
            </div>

            <!-- Clinical Features Badge List -->
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-[#0A4F78]/15 text-xs font-bold text-[#031827]/75 dark:text-[#F6F5EF]/75">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-[#67B34A]"></i> 
                <span>{{ $isRtl ? 'مستخلصات نباتية مطابقة حيوياً 100%' : '100% Bio-Identical Extracts' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-[#67B34A]"></i> 
                <span>{{ $isRtl ? 'فحص مخبري ثلاثي للمعادن الثقيلة' : 'Third-Party Heavy Metal Screened' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-[#67B34A]"></i> 
                <span>{{ $isRtl ? 'خالٍ من الملونات والمواد المالئة' : 'Zero Artificial Fillers or Binders' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-[#67B34A]"></i> 
                <span>{{ $isRtl ? 'محفوظ في سلاسل شحن مبردة' : 'Cold-Chain Nitrogen Sealed' }}</span>
              </div>
            </div>

          </div>
        </div>
        @endif

        <!-- ========================================================================= -->
        <!-- 2. PROFESSIONAL COLLAPSIBLE PRODUCT DETAILS ACCORDION STUDIO              -->
        <!-- ========================================================================= -->
        @if($secDossier['is_visible'])
        <div class="space-y-6 pt-6 {{ $secDossier['responsive_class'] }}" id="clinical-specifications-dossier">
          
          <!-- Dossier Section Header & Interactive Controls -->
          <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-4 border-b border-[#0A4F78]/20">
            <div>
              <span class="text-[11px] font-extrabold uppercase tracking-[0.28em] text-[#0A4F78] dark:text-[#2A8FC2] block mb-1">
                {{ $pdSettings['dossier_badge'] }}
              </span>
              <h2 class="text-2xl sm:text-4xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                {{ $pdSettings['dossier_title'] }}
              </h2>
            </div>

            <!-- Accordion Global Actions -->
            <div class="flex items-center gap-2">
              <button type="button" onclick="expandAllProductDetails()" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[#0A4F78]/20 dark:border-white/20 hover:bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] transition-colors flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-angles-down"></i>
                <span>{{ $isRtl ? 'توسيع الكل' : 'Expand All' }}</span>
              </button>
              <button type="button" onclick="collapseAllProductDetails()" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[#0A4F78]/20 dark:border-white/20 hover:bg-[#0A4F78]/10 text-[#031827]/70 dark:text-[#F6F5EF]/70 transition-colors flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-angles-up"></i>
                <span>{{ $isRtl ? 'طي الكل' : 'Collapse All' }}</span>
              </button>
            </div>
          </div>

          <!-- Quick Navigation Jump Filter Pills -->
          <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs font-bold">
            @if($sec1['is_visible'])
            <button type="button" onclick="openAndScrollToAccordion('accordion-ingredients')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer {{ $sec1['responsive_class'] }}">
              <i class="fa-solid fa-flask-vial text-[#67B34A]"></i>
              <span>{{ $sec1['title'] ?: ($isRtl ? 'المكونات الفعالة' : 'Active Compounds') }}</span>
            </button>
            @endif

            @if($sec2['is_visible'])
            <button type="button" onclick="openAndScrollToAccordion('accordion-benefits')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer {{ $sec2['responsive_class'] }}">
              <i class="fa-solid fa-bolt text-amber-500"></i>
              <span>{{ $sec2['title'] ?: ($isRtl ? 'الفوائد السريرية' : 'Clinical Benefits') }}</span>
            </button>
            @endif

            @if($sec3['is_visible'])
            <button type="button" onclick="openAndScrollToAccordion('accordion-usage')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer {{ $sec3['responsive_class'] }}">
              <i class="fa-solid fa-calendar-check text-[#2A8FC2]"></i>
              <span>{{ $sec3['title'] ?: ($isRtl ? 'بروتوكول الاستخدام' : 'Usage Protocol') }}</span>
            </button>
            @endif

            @if($sec4['is_visible'])
            <button type="button" onclick="openAndScrollToAccordion('accordion-science')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer {{ $sec4['responsive_class'] }}">
              <i class="fa-solid fa-dna text-purple-500"></i>
              <span>{{ $sec4['title'] ?: ($isRtl ? 'الآلية العلمية' : 'Cellular Science') }}</span>
            </button>
            @endif

            @if($sec5['is_visible'])
            <button type="button" onclick="openAndScrollToAccordion('accordion-safety')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer {{ $sec5['responsive_class'] }}">
              <i class="fa-solid fa-shield-halved text-emerald-600"></i>
              <span>{{ $sec5['title'] ?: ($isRtl ? 'السلامة والتحذيرات' : 'Safety & Warnings') }}</span>
            </button>
            @endif

            @if($sec6['is_visible'])
            <button type="button" onclick="openAndScrollToAccordion('accordion-specs')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer {{ $sec6['responsive_class'] }}">
              <i class="fa-solid fa-certificate text-indigo-500"></i>
              <span>{{ $sec6['title'] ?: ($isRtl ? 'المواصفات والضمان' : 'Monograph & Specs') }}</span>
            </button>
            @endif
          </div>

          <!-- THE 6 COLLAPSIBLE ACCORDION ITEMS -->
          <div class="space-y-4">
            
            <!-- 1. ACCORDION: ACTIVE BIO-COMPOUNDS & INGREDIENTS -->
            @if($sec1['is_visible'])
            <div id="accordion-ingredients" class="bz-collapsible-item {{ $sec1['default_open'] ? 'is-open' : '' }} {{ $sec1['responsive_class'] }} rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-ingredients')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-[#67B34A]/15 text-[#67B34A] flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-flask-vial"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-[#67B34A]/15 text-[#67B34A] font-extrabold">SECTION 01</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $sec1['badge'] }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $sec1['title'] }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#2A8FC2]/15 dark:text-[#2A8FC2] text-xs font-bold font-mono">
                    {{ count($ingredientsList) }} {{ $isRtl ? 'مركبات نشطة' : 'Bio-Actives' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  @if($sec1['custom_content_enabled'] && !empty($sec1['custom_content']))
                    <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed pt-6">
                      {!! $sec1['custom_content'] !!}
                    </div>
                  @else
                    <p class="text-xs sm:text-sm text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed font-medium">
                      {{ $isRtl 
                          ? 'تخضع جميع المواد الفعالة في هذه التركيبة لمعايرة مخبرية دقيقة وفق دستور الأدوية العالمي، لضمان أعلى توافر حيوي وامتصاص خلوي دون أي شوائب اصطناعية.'
                          : 'Every active bio-compound in this formulation is calibrated to therapeutic clinical thresholds using triple third-party HPLC verification, guaranteeing maximum cellular uptake and batch-to-batch consistency.' }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                      @foreach($ingredientsList as $ing)
                        @php
                          $ingName = $isRtl ? ($ing['name_ar'] ?? $ing['name_en'] ?? $ing['name'] ?? '') : ($ing['name_en'] ?? $ing['name'] ?? '');
                          $ingDose = $ing['dose'] ?? $ing['dosage'] ?? $ing['amount'] ?? 'Standardized';
                          $ingRole = $ing['role'] ?? ($isRtl ? 'دعم الوظائف الحيوية الخلوية' : 'Targeted cellular biological pathway');
                          $ingOrigin = $ing['origin'] ?? ($isRtl ? 'مستخلص طبيعي' : 'Botanical Bio-Active');
                        @endphp
                        <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 relative overflow-hidden group/ing hover:border-[#67B34A]/50 transition-colors">
                          <div class="flex justify-between items-start gap-2">
                            <span class="text-xs font-bold text-[#031827] dark:text-[#F6F5EF] leading-snug">
                              {{ $ingName }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-[11px] font-mono font-black shrink-0">
                              {{ $ingDose }}
                            </span>
                          </div>
                          <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">
                            {{ $ingRole }}
                          </p>
                          <div class="pt-2 border-t border-[#0A4F78]/10 flex items-center justify-between text-[10px] text-[#0A4F78] dark:text-[#2A8FC2] font-mono">
                            <span><i class="fa-solid fa-seedling mr-1 ml-1 text-[#67B34A]"></i> {{ $ingOrigin }}</span>
                            <span>99%+ PURE</span>
                          </div>
                        </div>
                      @endforeach
                    </div>

                    <!-- Quality Standards Badges -->
                    <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-white/5 border border-[#0A4F78]/10 flex flex-wrap gap-4 items-center justify-around text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2]">
                      <span><i class="fa-solid fa-check-double text-[#67B34A] mr-1.5 ml-1.5"></i> {{ $isRtl ? 'فحص HPLC ثلاثي' : 'Triple HPLC Assayed' }}</span>
                      <span><i class="fa-solid fa-shield-virus text-[#67B34A] mr-1.5 ml-1.5"></i> {{ $isRtl ? 'خالٍ من الملوثات والسموم' : 'Heavy Metal Screened' }}</span>
                      <span><i class="fa-solid fa-dna text-[#67B34A] mr-1.5 ml-1.5"></i> {{ $isRtl ? 'نباتي غير معدل وراثياً' : '100% Non-GMO & Vegan' }}</span>
                      <span><i class="fa-solid fa-snowflake text-[#67B34A] mr-1.5 ml-1.5"></i> {{ $isRtl ? 'معبأ بتقنية حفظ النيتروجين' : 'Nitrogen Flushed' }}</span>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            @endif

            <!-- 2. ACCORDION: CLINICAL BENEFITS & TIMELINE -->
            @if($sec2['is_visible'])
            <div id="accordion-benefits" class="bz-collapsible-item {{ $sec2['default_open'] ? 'is-open' : '' }} {{ $sec2['responsive_class'] }} rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-benefits')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-bolt"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-amber-500/15 text-amber-600 dark:text-amber-400 font-extrabold">SECTION 02</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $sec2['badge'] }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $sec2['title'] }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-bold font-mono">
                    {{ count($benefitsList) }} {{ $isRtl ? 'محاور رئيسية' : 'Core Outcomes' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  @if($sec2['custom_content_enabled'] && !empty($sec2['custom_content']))
                    <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed pt-6">
                      {!! $sec2['custom_content'] !!}
                    </div>
                  @else
                    <!-- Bullet list of clinical outcomes -->
                    <div class="space-y-3">
                      @foreach($benefitsList as $benefit)
                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/10">
                          <span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
                            <i class="fa-solid fa-check"></i>
                          </span>
                          <span class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed">
                            {{ $benefit }}
                          </span>
                        </div>
                      @endforeach
                    </div>

                    <!-- 3-Phase Cumulative Timeline -->
                    <div class="space-y-3 pt-2">
                      <span class="text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] block">
                        {{ $isRtl ? 'مراحل التراكم الحيوي واستجابة الجسم:' : 'Progressive Biological Timeline:' }}
                      </span>
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
                          <span class="text-xs font-extrabold text-[#67B34A] font-mono uppercase block">{{ $isRtl ? 'المرحلة 1 · اليوم 1 إلى 7' : 'Phase 1 · Days 1–7' }}</span>
                          <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? 'الاستجابة الخلوية الفورية' : 'Acute Perfusion & Alertness' }}</h4>
                          <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                            {{ $isRtl ? 'تحسين تدفق الدم الدماغي، رفع مستوى اليقظة، وتحييد بوادر الإجهاد الذهني اليومي.' : 'Immediate microcirculation enhancement and neural stamina without nervous system crash.' }}
                          </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
                          <span class="text-xs font-extrabold text-[#2A8FC2] font-mono uppercase block">{{ $isRtl ? 'المرحلة 2 · اليوم 14 إلى 30' : 'Phase 2 · Days 14–30' }}</span>
                          <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? 'تجدد المشابك العصبية' : 'Synaptic Consolidation' }}</h4>
                          <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                            {{ $isRtl ? 'تحفيز إشارات BDNF، استقرار غشاء الخلايا الفسفوليبيدي، وزيادة كفاءة الذاكرة العاملة.' : 'Elevated BDNF growth signaling and sustained neurotransmitter balance for cognitive resilience.' }}
                          </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
                          <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#67B34A] font-mono uppercase block">{{ $isRtl ? 'المرحلة 3 · اليوم 60 فما فوق' : 'Phase 3 · Days 60+' }}</span>
                          <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? 'حماية طول العمر الخلوي' : 'Deep Cellular Longevity' }}</h4>
                          <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                            {{ $isRtl ? 'حماية شاملة للميتوكوندريا، ودعم صحة الأوعية الدموية ومرونة الأداء الدماغي الممتد.' : 'Mitochondrial resilience and ongoing genomic stability supporting lifelong cognitive health.' }}
                          </p>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            @endif

            <!-- 3. ACCORDION: PROTOCOL & USAGE INSTRUCTIONS -->
            @if($sec3['is_visible'])
            <div id="accordion-usage" class="bz-collapsible-item {{ $sec3['default_open'] ? 'is-open' : '' }} {{ $sec3['responsive_class'] }} rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-usage')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-[#2A8FC2]/15 text-[#2A8FC2] flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-calendar-check"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-[#2A8FC2]/15 text-[#2A8FC2] font-extrabold">SECTION 03</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $sec3['badge'] }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $sec3['title'] }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-[#2A8FC2]/10 text-[#2A8FC2] text-xs font-bold font-mono">
                    {{ $dosageText }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  @if($sec3['custom_content_enabled'] && !empty($sec3['custom_content']))
                    <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed pt-6">
                      {!! $sec3['custom_content'] !!}
                    </div>
                  @else
                    <div class="p-4 sm:p-6 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border-inline-start-4 border-primary space-y-2">
                      <span class="text-xs font-extrabold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] block">
                        <i class="fa-solid fa-circle-info mr-1 ml-1"></i> {{ $isRtl ? 'الإرشاد السريري المباشر:' : 'Primary Administration Directive:' }}
                      </span>
                      <p class="text-xs sm:text-sm text-[#031827] dark:text-[#F6F5EF] font-medium leading-relaxed">
                        {{ $usageText }}
                      </p>
                    </div>

                    <!-- Visual Protocol Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                      <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
                        <div class="w-10 h-10 rounded-full bg-[#67B34A]/15 text-[#67B34A] mx-auto flex items-center justify-center text-base">
                          <i class="fa-solid fa-sun"></i>
                        </div>
                        <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? '1. التوقيت اليومي' : '1. Timing' }}</h4>
                        <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                          {{ $isRtl ? 'يُفضل تناوله صباحاً ليتزامن مع إيقاع الساعة البيولوجية للجسم.' : 'Administer each morning to harmonize with your natural circadian cortisol curve.' }}
                        </p>
                      </div>

                      <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
                        <div class="w-10 h-10 rounded-full bg-[#2A8FC2]/15 text-[#2A8FC2] mx-auto flex items-center justify-center text-base">
                          <i class="fa-solid fa-glass-water"></i>
                        </div>
                        <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? '2. السائل الناقل' : '2. Hydration' }}</h4>
                        <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                          {{ $isRtl ? '250 مل من الماء النقي لتسريع ذوبان الكبسولة المعوية النباتية.' : '250ml mineralized water facilitates rapid gastro-resistant dissolution.' }}
                        </p>
                      </div>

                      <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
                        <div class="w-10 h-10 rounded-full bg-amber-500/15 text-amber-500 mx-auto flex items-center justify-center text-base">
                          <i class="fa-solid fa-bottle-droplet"></i>
                        </div>
                        <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? '3. تعزيز الامتصاص' : '3. Lipid Synergy' }}</h4>
                        <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                          {{ $isRtl ? 'تناوله مع زيت الزيتون البكر الممتاز يضاعف امتصاص مضادات الأكسدة.' : 'Ingesting with healthy fats boosts lipophilic flavonoid bioavailability by up to 300%.' }}
                        </p>
                      </div>

                      <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
                        <div class="w-10 h-10 rounded-full bg-purple-500/15 text-purple-500 mx-auto flex items-center justify-center text-base">
                          <i class="fa-solid fa-repeat"></i>
                        </div>
                        <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? '4. الاستمرارية' : '4. Regularity' }}</h4>
                        <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                          {{ $isRtl ? 'لا يسبب الإدمان أو الاعتمادية، ومصمم للاستخدام اليومي المستمر بأمان.' : 'Zero tolerance buildup or dependency. Engineered for uninterrupted daily longevity.' }}
                        </p>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            @endif

            <!-- 4. ACCORDION: CELLULAR SCIENCE & PHARMACOKINETICS -->
            @if($sec4['is_visible'])
            <div id="accordion-science" class="bz-collapsible-item {{ $sec4['default_open'] ? 'is-open' : '' }} {{ $sec4['responsive_class'] }} rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-science')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-purple-500/15 text-purple-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-dna"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-purple-500/15 text-purple-600 dark:text-purple-400 font-extrabold">SECTION 04</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $sec4['badge'] }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $sec4['title'] }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 text-xs font-bold font-mono">
                    {{ $isRtl ? 'مسارات حيوية دقيقة' : 'Precision Pathways' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  @if($sec4['custom_content_enabled'] && !empty($sec4['custom_content']))
                    <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed pt-6">
                      {!! $sec4['custom_content'] !!}
                    </div>
                  @else
                    <div class="space-y-4">
                      <p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium">
                        {{ $clinicalMechanism }}
                      </p>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">
                          <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block">
                            <i class="fa-solid fa-microchip mr-1 ml-1 text-[#67B34A]"></i> {{ $isRtl ? 'نفاذية الحاجز الدموي الدماغي (BBB):' : 'Blood-Brain Barrier (BBB) Kinetics:' }}
                          </span>
                          <p class="text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed">
                            {{ $isRtl ? 'مركبات الفسفوليبيد المرفقة تمكن الجزيئات الحيوية من عبور الحاجز الدموي الدماغي بكفاءة، مما يوفر تركيزاً نشطاً داخل المشابك العصبية دون فقدان هضمي.' : 'Phospholipid-conjugated carriers enable efficient transport across the blood-brain barrier for localized neuronal receptor binding.' }}
                          </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">
                          <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block">
                            <i class="fa-solid fa-atom mr-1 ml-1 text-purple-500"></i> {{ $isRtl ? 'حماية ميتوكوندريا الخلية والتنفس الهوائي:' : 'Mitochondrial Redox Equilibrium:' }}
                          </span>
                          <p class="text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed">
                            {{ $isRtl ? 'يحمي الإنزيم المساعد CoQ10 وسلسلة مضادات الأكسدة المعقدة من تسرب الإلكترونات والإجهاد التأكسدي، مما يعزز عمر الخلية وقدرتها الإنتاجية.' : 'Sustains electron transport chain integrity (Complexes I & III), shielding mitochondrial DNA from premature senescence.' }}
                          </p>
                        </div>
                      </div>

                      <div class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20 text-xs text-[#031827] dark:text-[#F6F5EF] flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-purple-500 text-white flex items-center justify-center shrink-0 font-bold">
                          <i class="fa-solid fa-book-bookmark"></i>
                        </span>
                        <span>
                          {{ $isRtl ? 'مبني على أبحاث طول العمر المستوحاة من دراسات المعمرين في المناطق الزرقاء الخمس المنشورة في كبرى الدوريات الطبية.' : 'Validated against empirical longevity data from centenarian cohort studies published in peer-reviewed journals of cellular pharmacology.' }}
                        </span>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            @endif

            <!-- 5. ACCORDION: SAFETY, WARNINGS & QUALITY CONTROLS -->
            @if($sec5['is_visible'])
            <div id="accordion-safety" class="bz-collapsible-item {{ $sec5['default_open'] ? 'is-open' : '' }} {{ $sec5['responsive_class'] }} rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-safety')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-shield-halved"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-extrabold">SECTION 05</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $sec5['badge'] }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $sec5['title'] }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold font-mono">
                    {{ $isRtl ? 'معايير أمان معتمدة' : 'cGMP Verified' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  @if($sec5['custom_content_enabled'] && !empty($sec5['custom_content']))
                    <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed pt-6">
                      {!! $sec5['custom_content'] !!}
                    </div>
                  @else
                    <!-- Amber Medical Warning Box -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 space-y-2">
                      <div class="flex items-center gap-2 text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $isRtl ? 'تنبيه سريري وإرشادات الحفظ:' : 'Medical Advisory & Precautions:' }}</span>
                      </div>
                      <p class="text-xs leading-relaxed">
                        {{ $warningsText }}
                      </p>
                    </div>

                    <!-- Allergen & Purity Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs font-bold">
                      <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
                        <i class="fa-solid fa-ban text-[#67B34A] text-base"></i>
                        <span class="block text-[#031827] dark:text-white">{{ $isRtl ? 'خالٍ من الجلوتين' : 'Gluten-Free' }}</span>
                      </div>
                      <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
                        <i class="fa-solid fa-leaf text-[#67B34A] text-base"></i>
                        <span class="block text-[#031827] dark:text-white">{{ $isRtl ? 'خالٍ من الصويا' : 'Soy-Free' }}</span>
                      </div>
                      <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
                        <i class="fa-solid fa-droplet-slash text-[#67B34A] text-base"></i>
                        <span class="block text-[#031827] dark:text-white">{{ $isRtl ? 'خالٍ من المشتقات الحيوانية' : '100% Vegan' }}</span>
                      </div>
                      <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
                        <i class="fa-solid fa-flask text-[#67B34A] text-base"></i>
                        <span class="block text-[#031827] dark:text-white">{{ $isRtl ? 'دون مواد حافظة' : 'No Preservatives' }}</span>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            @endif

            <!-- 6. ACCORDION: CLINICAL MONOGRAPH & SUPPLY SPECIFICATIONS -->
            @if($sec6['is_visible'])
            <div id="accordion-specs" class="bz-collapsible-item {{ $sec6['default_open'] ? 'is-open' : '' }} {{ $sec6['responsive_class'] }} rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-specs')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 text-indigo-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-certificate"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 font-extrabold">SECTION 06</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $sec6['badge'] }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $sec6['title'] }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-bold font-mono">
                    {{ $isRtl ? 'بيانات المنتج' : 'Technical Data' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  @if($sec6['custom_content_enabled'] && !empty($sec6['custom_content']))
                    <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed pt-6">
                      {!! $sec6['custom_content'] !!}
                    </div>
                  @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs font-medium">
                      <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                        <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'حجم العبوة والجرعات:' : 'Supply Volume:' }}</span>
                        <span class="font-bold text-[#031827] dark:text-white">{{ $packageSize }}</span>
                      </div>
                      <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                        <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'رمز المنتج SKU:' : 'Product SKU:' }}</span>
                        <span class="font-bold text-[#0A4F78] dark:text-[#2A8FC2] font-mono">{{ $pSku }}</span>
                      </div>
                      <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                        <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'جهة التصنيع المعتمدة:' : 'Facility Standards:' }}</span>
                        <span class="font-bold text-[#67B34A]">cGMP & FDA Registered</span>
                      </div>
                      <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                        <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'صيغة الكبسولة:' : 'Delivery Matrix:' }}</span>
                        <span class="font-bold text-[#031827] dark:text-white">{{ $isRtl ? 'كبسولات نباتية معوية' : 'Enteric Veg Capsule' }}</span>
                      </div>
                      <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                        <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'العمر التخزيني:' : 'Shelf Life Stability:' }}</span>
                        <span class="font-bold text-[#031827] dark:text-white">24 Months Unopened</span>
                      </div>
                      <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                        <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'ضمان الرضا الطبي:' : 'Satisfaction Guarantee:' }}</span>
                        <span class="font-bold text-[#67B34A]">{{ $isRtl ? '30 يوماً استرداد كامل' : '30-Day Clinical Refund' }}</span>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            @endif

          </div>
        </div>
        @endif

        @php
          $bundleData = $isObj ? $product->getSynergisticBundle() : null;
        @endphp

        <!-- ========================================================================= -->
        <!-- 3. COMPLEMENTARY PROTOCOL FORMULATIONS (RELATED PRODUCTS & BUNDLE)        -->
        <!-- ========================================================================= -->
        @if($secRelated['is_visible'])
          <div class="space-y-10 pt-10 border-t border-[#0A4F78]/20 {{ $secRelated['responsive_class'] }}">

            @if($bundleData && !empty($bundleData['enabled']) && !empty($bundleData['items']) && count($bundleData['items']) > 0)
              <!-- ==========================================================
                   SYNERGISTIC LONGEVITY PROTOCOL BUNDLE (CONTROLLED DISCOUNT)
                   ========================================================== -->
              <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#02131F] via-[#062B49] to-[#0A4F78] border-2 border-pink-500/30 p-6 sm:p-8 text-white shadow-2xl">
                <!-- Glowing Ambient Accent Orbs -->
                <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-pink-500/15 blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-[#67B34A]/20 blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6">
                  
                  <!-- Bundle Header -->
                  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/10 pb-5">
                    <div class="space-y-1.5">
                      <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-pink-500 text-white font-mono text-xs font-black uppercase tracking-wider shadow-sm">
                          <i class="fa-solid fa-sparkles text-[10px]"></i>
                          {{ $bundleData['badge'] }}
                        </span>
                        <span class="px-3 py-1 rounded-full bg-white/10 text-white font-mono text-xs font-bold border border-white/15">
                          <i class="fa-solid fa-link text-[#2A8FC2] mr-1 ml-1"></i>
                          {{ $isRtl ? 'تكامل حيوي موثق سريرياً' : 'Clinically Validated Synergy' }}
                        </span>
                      </div>
                      <h3 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                        {{ $isRtl ? 'بروتوكول التكامل الحيوي الموصى به (خصم تلقائي)' : 'Recommended Synergistic Protocol Bundle' }}
                      </h3>
                      <p class="text-xs sm:text-sm text-white/80 max-w-2xl leading-relaxed">
                        {{ $isRtl 
                            ? 'احصل على أقصى توافر حيوي ونتائج ممتدة عبر دمج المستحضرات التكميلية المتوافقة مع هذا المستحضر بخصم منسق خاص.' 
                            : 'Achieve enhanced metabolic longevity and bio-cellular efficacy by combining complementary formulations together at a controlled protocol discount.' }}
                      </p>
                    </div>

                    <div class="text-start md:text-end shrink-0">
                      <span class="inline-block px-3 py-1.5 rounded-xl bg-emerald-500/20 text-[#B8D98A] font-mono text-xs font-black border border-emerald-500/30">
                        <i class="fa-solid fa-badge-percent mr-1 ml-1"></i>
                        {{ $isRtl ? "وفر {$bundleData['savings_percentage']}% مع الباقة" : "Save {$bundleData['savings_percentage']}% with Protocol" }}
                      </span>
                    </div>
                  </div>

                  <!-- Visual Bundle Flow: Primary Product + Complementary Products = Total Box -->
                  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    
                    <!-- Products Flow Column (lg:col-span-8) -->
                    <div class="lg:col-span-8 flex flex-col sm:flex-row items-center gap-4 flex-wrap">
                      
                      <!-- 1. Primary Product Card (Locked) -->
                      <div class="w-full sm:w-44 p-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md flex flex-col items-center text-center space-y-2 relative shadow-lg">
                        <span class="absolute top-2.5 start-2.5 px-2 py-0.5 rounded-md bg-[#67B34A] text-white text-[9px] font-mono font-black uppercase">
                          {{ $isRtl ? 'المستحضر الأساسي' : 'PRIMARY' }}
                        </span>
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-white/5 border border-white/10 mt-3">
                          <img src="{{ $pImage }}" alt="{{ $pName }}" width="80" height="80" loading="lazy" decoding="async" class="w-full h-full object-cover">
                        </div>
                        <div class="w-full">
                          <div class="text-xs font-black text-white line-clamp-1" title="{{ $pName }}">{{ $pName }}</div>
                          <div class="text-xs font-mono font-bold text-[#67B34A] mt-0.5">
                            @currency($pSalePrice ?: $pPrice)
                          </div>
                        </div>
                      </div>

                      <!-- Plus Separator -->
                      <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white text-xs font-bold border border-white/20 shrink-0 shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                      </div>

                      <!-- 2. Complementary Products List -->
                      @foreach($bundleData['items'] as $bIdx => $bProd)
                        @php
                          $bName = $isRtl && !empty($bProd->name_ar) ? $bProd->name_ar : ($bProd->name_en ?? '');
                          $bImg = \App\Models\Product::normalizeUrl($bProd->primary_image_url ?? $bProd->image);
                          $bPrice = (float) ($bProd->sale_price ?: $bProd->price);
                        @endphp
                        <label class="w-full sm:w-44 p-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/15 backdrop-blur-md flex flex-col items-center text-center space-y-2 relative shadow-lg cursor-pointer transition-all bz-bundle-store-item"
                               data-prod-id="{{ $bProd->id }}"
                               data-prod-price="{{ $bPrice }}"
                               data-prod-name-en="{{ $bProd->name_en }}"
                               data-prod-name-ar="{{ $bProd->name_ar }}"
                               data-prod-slug="{{ $bProd->slug }}"
                               data-prod-image="{{ $bImg }}">
                          
                          <input type="checkbox" checked 
                                 class="absolute top-2.5 start-2.5 w-4 h-4 rounded text-pink-600 focus:ring-0 cursor-pointer bz-bundle-checkbox"
                                 onchange="recalculateStorefrontBundle()">

                          <div class="w-20 h-20 rounded-xl overflow-hidden bg-white/5 border border-white/10 mt-3">
                            <img src="{{ $bImg }}" alt="{{ $bName }}" width="80" height="80" loading="lazy" decoding="async" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                          </div>
                          <div class="w-full">
                            <div class="text-xs font-black text-white line-clamp-1" title="{{ $bName }}">{{ $bName }}</div>
                            <div class="text-xs font-mono font-bold text-pink-300 mt-0.5">
                              @currency($bPrice)
                            </div>
                          </div>
                        </label>

                        @if(!$loop->last)
                          <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white text-xs font-bold border border-white/20 shrink-0 shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                          </div>
                        @endif
                      @endforeach

                    </div>

                    <!-- Equal & Bundle Summary Action Card (lg:col-span-4) -->
                    <div class="lg:col-span-4 p-5 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-xl space-y-4 shadow-xl">
                      <div class="space-y-2 border-b border-white/10 pb-3">
                        <div class="flex items-center justify-between text-xs text-white/70 font-mono">
                          <span>{{ $isRtl ? 'المجموع الأصلي للمستحضرات:' : 'Original Total:' }}</span>
                          <span class="line-through" id="storefrontBundleOriginal">@currency($bundleData['total_original'])</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-pink-300 font-mono font-bold">
                          <span>{{ $isRtl ? 'خصم باقة البروتوكول:' : 'Protocol Bundle Savings:' }}</span>
                          <span id="storefrontBundleDiscount">-@currency($bundleData['discount_amount'])</span>
                        </div>
                        <div class="flex items-center justify-between text-base font-black text-white pt-1">
                          <span>{{ $isRtl ? 'السعر النهائي للباقة:' : 'Bundle Price:' }}</span>
                          <span class="text-xl font-mono text-[#67B34A]" id="storefrontBundleFinal">@currency($bundleData['final_price'])</span>
                        </div>
                      </div>

                      <!-- 1-Click Buy Protocol Button -->
                      <button type="button" 
                              id="btnBuyStorefrontBundle"
                              onclick="addCompleteBundleToCart()" 
                              class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-pink-500 via-rose-500 to-pink-600 hover:from-pink-600 hover:to-rose-600 text-white text-xs font-black uppercase tracking-wider shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer border border-white/20">
                        <i class="fa-solid fa-cart-plus text-sm" id="bundleBtnIcon"></i>
                        <span id="bundleBtnText">{{ $bundleBtnText }}</span>
                      </button>

                      <div class="text-[11px] text-center text-white/60 font-mono">
                        <i class="fa-solid fa-shield-halved text-[#67B34A] mr-1 ml-1"></i>
                        {{ $isRtl ? 'تطبق نسبة الخصم تلقائياً عند الدفع' : 'Controlled discount applied instantly' }}
                      </div>
                    </div>

                  </div>

                </div>
              </div>

              <script>
                // Storefront Dynamic Bundle Engine
                const bzBundlePrimary = {
                  id: {{ $pId }},
                  price: {{ (float) ($pSalePrice ?: $pPrice) }},
                  name_en: "{{ addslashes($product->name_en ?? '') }}",
                  name_ar: "{{ addslashes($product->name_ar ?? '') }}",
                  slug: "{{ $pSlug }}",
                  image: "{{ $pImage }}",
                };
                const bzBundleDiscountType = "{{ $bundleData['discount_type'] }}";
                const bzBundleDiscountValue = {{ (float) $bundleData['discount_value'] }};

                function recalculateStorefrontBundle() {
                  const items = document.querySelectorAll('.bz-bundle-store-item');
                  let compTotal = 0;
                  let selectedCount = 0;

                  items.forEach(item => {
                    const chk = item.querySelector('.bz-bundle-checkbox');
                    if (chk && chk.checked) {
                      compTotal += parseFloat(item.dataset.prodPrice) || 0;
                      selectedCount++;
                      item.style.opacity = '1';
                    } else {
                      item.style.opacity = '0.4';
                    }
                  });

                  const originalTotal = bzBundlePrimary.price + compTotal;
                  let discountAmount = 0;
                  if (bzBundleDiscountType === 'percentage') {
                    discountAmount = (originalTotal * bzBundleDiscountValue) / 100.0;
                  } else {
                    discountAmount = Math.min(originalTotal, bzBundleDiscountValue);
                  }

                  const finalPrice = Math.max(0, originalTotal - discountAmount);

                  const origEl = document.getElementById('storefrontBundleOriginal');
                  const discEl = document.getElementById('storefrontBundleDiscount');
                  const finalEl = document.getElementById('storefrontBundleFinal');

                  if (origEl) origEl.textContent = 'EGP ' + originalTotal.toFixed(2);
                  if (discEl) discEl.textContent = '-EGP ' + discountAmount.toFixed(2);
                  if (finalEl) finalEl.textContent = 'EGP ' + finalPrice.toFixed(2);
                }

                async function addCompleteBundleToCart() {
                  const btn = document.getElementById('btnBuyStorefrontBundle');
                  const icon = document.getElementById('bundleBtnIcon');
                  const text = document.getElementById('bundleBtnText');
                  const isAr = {{ $isRtl ? 'true' : 'false' }};

                  if (icon) icon.className = 'fa-solid fa-spinner fa-spin text-sm';
                  if (text) text.textContent = isAr ? 'جاري إضافة البروتوكول...' : 'Adding Protocol...';

                  try {
                    const checkedItems = document.querySelectorAll('.bz-bundle-store-item .bz-bundle-checkbox:checked');
                    
                    // Calculate discount factor
                    let compTotal = 0;
                    checkedItems.forEach(chk => {
                      const item = chk.closest('.bz-bundle-store-item');
                      compTotal += parseFloat(item.dataset.prodPrice) || 0;
                    });
                    const originalTotal = bzBundlePrimary.price + compTotal;
                    let discountAmount = (bzBundleDiscountType === 'percentage')
                      ? (originalTotal * bzBundleDiscountValue) / 100.0
                      : Math.min(originalTotal, bzBundleDiscountValue);
                    const discountRatio = originalTotal > 0 ? (1 - (discountAmount / originalTotal)) : 1;

                    // Build payload
                    const payloadItems = [
                      {
                        product_id: bzBundlePrimary.id,
                        name_en: bzBundlePrimary.name_en,
                        name_ar: bzBundlePrimary.name_ar,
                        price: bzBundlePrimary.price,
                        discounted_price: Math.round(bzBundlePrimary.price * discountRatio * 100) / 100,
                        slug: bzBundlePrimary.slug,
                        image: bzBundlePrimary.image,
                        quantity: 1,
                      }
                    ];

                    checkedItems.forEach(chk => {
                      const item = chk.closest('.bz-bundle-store-item');
                      const basePrice = parseFloat(item.dataset.prodPrice) || 0;
                      payloadItems.push({
                        product_id: parseInt(item.dataset.prodId),
                        name_en: item.dataset.prodNameEn,
                        name_ar: item.dataset.prodNameAr,
                        price: basePrice,
                        discounted_price: Math.round(basePrice * discountRatio * 100) / 100,
                        slug: item.dataset.prodSlug,
                        image: item.dataset.prodImage,
                        quantity: 1,
                      });
                    });

                    const response = await fetch('{{ route("customer.cart.add-bundle") }}', {
                      method: 'POST',
                      headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                      },
                      body: JSON.stringify({ items: payloadItems }),
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                      throw new Error(data.message || (isAr ? 'تعذر إضافة الباقة إلى السلة.' : 'Failed to add bundle.'));
                    }

                    // Success feedback
                    if (icon) icon.className = 'fa-solid fa-check text-sm';
                    if (text) text.textContent = isAr ? 'تمت إضافة البروتوكول بنجاح!' : 'Protocol Added to Cart!';

                    if (window.toast) {
                      window.toast.success(data.message);
                    }

                    // Refresh cart drawer
                    if (window.BLUEZONE_CART) {
                      if (typeof window.BLUEZONE_CART.fetchCart === 'function') {
                        window.BLUEZONE_CART.fetchCart();
                      }
                      if (typeof window.BLUEZONE_CART.open === 'function') {
                        setTimeout(() => window.BLUEZONE_CART.open(), 400);
                      }
                    }

                    setTimeout(() => {
                      if (icon) icon.className = 'fa-solid fa-cart-plus text-sm';
                      if (text) text.textContent = '{{ addslashes($bundleBtnText) }}';
                    }, 3000);

                  } catch (err) {
                    console.error('Add bundle error:', err);
                    if (icon) icon.className = 'fa-solid fa-triangle-exclamation text-sm';
                    if (text) text.textContent = isAr ? 'فشلت الإضافة' : 'Error adding';
                    if (window.toast) {
                      window.toast.error(err.message);
                    }
                  }
                }
              </script>
            @endif

            @if(isset($relatedProducts) && count($relatedProducts) > 0)
            <div class="flex justify-between items-end">
              <div>
                <span class="text-[11px] font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2] block mb-1">
                  {{ $pdSettings['related_badge'] }}
                </span>
                <h3 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF]">
                  {{ $pdSettings['related_title'] }}
                </h3>
              </div>
              <a href="{{ route('customer.products') }}" class="text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] hover:text-[#67B34A] transition-colors">
                {{ $isRtl ? 'عرض المتجر كاملاً' : 'EXPLORE ALL' }} <i class="fa-solid fa-arrow-right rtl:rotate-180 mr-1 ml-1"></i>
              </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
              @foreach($relatedProducts as $relProduct)
                @php
                  $relIsObj = is_object($relProduct);
                  $relSlug = $relIsObj ? ($relProduct->slug ?? '') : ($relProduct['slug'] ?? '');
                  $relName = $relIsObj 
                      ? ($isRtl && !empty($relProduct->name_ar) ? $relProduct->name_ar : ($relProduct->name_en ?? $relProduct->name ?? ''))
                      : ($isRtl && !empty($relProduct['name_ar']) ? $relProduct['name_ar'] : ($relProduct['name_en'] ?? $relProduct['name'] ?? ''));
                  $relImg = $relIsObj 
                      ? ($relProduct->primary_image_url ?? asset('assets/products/blue-cell.webp'))
                      : asset($relProduct['image'] ?? 'assets/products/blue-cell.webp');
                  $relPrice = $relIsObj ? ($relProduct->price ?? 79) : ($relProduct['price'] ?? 79);
                  $relCategory = $relIsObj 
                      ? (($relProduct->relationLoaded('category') && $relProduct->category)
                          ? ($isRtl && !empty($relProduct->category->name_ar) ? $relProduct->category->name_ar : ($relProduct->category->name ?? 'LONGEVITY'))
                          : 'LONGEVITY')
                      : ($isRtl && !empty($relProduct['category_ar']) ? $relProduct['category_ar'] : ($relProduct['category_en'] ?? 'LONGEVITY'));
                @endphp
                <div class="rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 overflow-hidden shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                  <div>
                    <a href="{{ route('customer.product.show', $relSlug) }}" class="block aspect-square relative overflow-hidden bg-[#031827]">
                      <img src="{{ $relImg }}" alt="{{ $relName }}" 
                        width="400"
                        height="400"
                        loading="lazy"
                        decoding="async"
                        onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-cell.webp') }}';"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                      <span class="absolute top-4 left-4 rtl:left-auto rtl:right-4 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-white/90 dark:bg-[#031827]/90 text-[#0A4F78] dark:text-[#2A8FC2] backdrop-blur-md">
                        {{ $relCategory }}
                      </span>
                    </a>

                    <div class="p-6 space-y-2">
                      <h4 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">
                        <a href="{{ route('customer.product.show', $relSlug) }}" class="hover:text-primary transition-colors">
                          {{ $relName }}
                        </a>
                      </h4>
                      <div class="text-xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                        @currency($relPrice)
                      </div>
                    </div>
                  </div>

                  <div class="p-6 pt-0">
                    <a href="{{ route('customer.product.show', $relSlug) }}" class="block w-full py-3 text-center text-xs font-extrabold uppercase tracking-wider rounded-xl border border-[#0A4F78]/30 dark:border-white/30 text-[#031827] dark:text-white hover:bg-[#0A4F78] hover:text-white hover:border-[#0A4F78] transition-all">
                      {{ $isRtl ? 'عرض ملف المستحضر' : 'VIEW CLINICAL FILE' }}
                    </a>
                  </div>
                </div>
              @endforeach
            </div>
            @endif
          </div>
        @endif

      </div>
    </div>

    <!-- Product Detail Script with Smooth Collapsible Controller -->
    <script>
      let currentDetailQty = 1;

      function updateQty(delta) {
        currentDetailQty = Math.max(1, currentDetailQty + delta);
        const qtyEl = document.getElementById('detail-qty-val');
        if (qtyEl) qtyEl.textContent = currentDetailQty;
      }

      function switchMainImage(src, btn) {
        const mainImg = document.getElementById('product-main-img');
        if (mainImg) {
          mainImg.style.opacity = '0.3';
          setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
          }, 150);
        }
        document.querySelectorAll('.aspect-square button, .flex button').forEach(b => {
          b.classList.remove('border-[#67B34A]', 'ring-2', 'ring-[#67B34A]/30');
        });
        if (btn) {
          btn.classList.add('border-[#67B34A]', 'ring-2', 'ring-[#67B34A]/30');
        }
      }

      function selectProtocolVariant(months, price, btn) {
        document.querySelectorAll('.protocol-variant-btn').forEach(b => {
          b.classList.remove('border-[#67B34A]', 'bg-[#67B34A]/5');
          b.classList.add('border-[#0A4F78]/20');
        });
        if (btn) {
          btn.classList.remove('border-[#0A4F78]/20');
          btn.classList.add('border-[#67B34A]', 'bg-[#67B34A]/5');
        }
        currentDetailQty = months;
        const qtyEl = document.getElementById('detail-qty-val');
        if (qtyEl) qtyEl.textContent = currentDetailQty;
      }

      // ==========================================
      // ACCORDION COLLAPSE CONTROLLER
      // ==========================================
      function toggleProductDetailItem(accordionId) {
        const item = document.getElementById(accordionId);
        if (!item) return;
        item.classList.toggle('is-open');
      }

      function expandAllProductDetails() {
        document.querySelectorAll('.bz-collapsible-item').forEach(item => {
          item.classList.add('is-open');
        });
      }

      function collapseAllProductDetails() {
        document.querySelectorAll('.bz-collapsible-item').forEach(item => {
          item.classList.remove('is-open');
        });
      }

      function openAndScrollToAccordion(accordionId) {
        const item = document.getElementById(accordionId);
        if (!item) return;

        // Open item if closed
        item.classList.add('is-open');

        // Smooth scroll to the element
        const yOffset = -100; 
        const y = item.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' });

        // Highlight tab pill
        document.querySelectorAll('.filter-tab-pill').forEach(pill => {
          pill.classList.remove('active');
        });
        if (event && event.currentTarget) {
          event.currentTarget.classList.add('active');
        }
      }
    </script>
</x-layouts.customer>
